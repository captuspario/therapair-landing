#!/usr/bin/env node
/**
 * Send A/B Test Batch - Research Email Campaign
 * 
 * Sends research invitation emails to a small batch (default 10 recipients)
 * with A/B testing on subject line and intro paragraph.
 * 
 * Usage: node send-ab-test-batch.mjs [batch_size] [RESEND_API_KEY]
 * Example: node send-ab-test-batch.mjs 10 YOUR_API_KEY
 */

import { Resend } from 'resend';
import crypto from 'node:crypto';
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import dotenv from 'dotenv';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Load .env file
const envPath = path.join(__dirname, '..', '.env');
if (fs.existsSync(envPath)) {
  dotenv.config({ path: envPath });
}

// Get arguments
const batchSize = parseInt(process.argv[2]) || 10;
const apiKey = process.argv[3] || process.env.RESEND_API_KEY;

if (!apiKey) {
  console.error('❌ Error: Resend API key required');
  console.error('Usage: node send-ab-test-batch.mjs [batch_size] [RESEND_API_KEY]');
  console.error('Example: node send-ab-test-batch.mjs 10 YOUR_API_KEY');
  process.exit(1);
}

// Configuration
const NOTION_TOKEN = process.env.NOTION_TOKEN || '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';
const DIRECTORY_DB_ID = process.env.DIRECTORY_DB_ID || '28c5c25944da80a48d85fd43119f4ec1'; // VIC Therapist DB
const SECRET = process.env.NOTION_SECRET || process.env.RESEARCH_TOKEN_SECRET || 'I1WPkhdJs6Fw2XAa+BRH2hCiezYuP8FjT1xoG5tp/koajYB7tlleXc3lzvwVUqNv';
const resend = new Resend(apiKey);

// A/B Test Variants
const VARIANTS = {
  A: {
    subject: 'Help shape a better therapist-matching system',
    preview: 'We found your details in the VIC therapists register and thought you might be interested in shaping a new approach to therapist–client matching.',
    intro: `Hi {FIRST_NAME},

We found your details in the publicly available VIC therapists register and thought you might be interested in shaping a new approach to therapist–client matching.`
  },
  B: {
    subject: '5 minutes to improve therapist matching?',
    preview: 'Quick research survey: Help us understand what questions matter most for meaningful therapist–client matching.',
    intro: `Hi {FIRST_NAME},

Quick question: What if clients could find therapists who truly fit them—by values, lived experience, and communication style, not just modality or postcode?`
  }
};

function base64urlEncode(str) {
  return Buffer.from(str)
    .toString('base64')
    .replace(/\+/g, '-')
    .replace(/\//g, '_')
    .replace(/=/g, '');
}

function signToken(payload) {
  const header = { alg: 'HS256', typ: 'JWT' };
  const headerB64 = base64urlEncode(JSON.stringify(header));
  const payloadB64 = base64urlEncode(JSON.stringify(payload));
  const signedPortion = `${headerB64}.${payloadB64}`;
  const signature = crypto
    .createHmac('sha256', SECRET)
    .update(signedPortion)
    .digest();
  const signatureB64 = signature.toString('base64')
    .replace(/\+/g, '-')
    .replace(/\//g, '_')
    .replace(/=/g, '');
  return `${signedPortion}.${signatureB64}`;
}

async function getEligibleTherapists(limit) {
  console.log(`\n📋 Fetching eligible therapists from VIC database...`);
  
  // Fetch therapists who haven't been sent the research invite yet
  const url = `https://api.notion.com/v1/databases/${DIRECTORY_DB_ID}/query`;
  const response = await fetch(url, {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${NOTION_TOKEN}`,
      'Content-Type': 'application/json',
      'Notion-Version': '2022-06-28',
    },
    body: JSON.stringify({
      filter: {
        or: [
          {
            property: 'Research Invite Sent',
            checkbox: {
              equals: false
            }
          },
          {
            property: 'Research Invite Sent',
            checkbox: {
              is_empty: true
            }
          }
        ]
      },
      page_size: limit * 2, // Get more than needed for filtering
    }),
  });

  if (!response.ok) {
    const error = await response.json();
    throw new Error(`Failed to fetch therapists: ${error.message || response.status}`);
  }

  const data = await response.json();
  const therapists = data.results || [];
  
  // Filter to only those with email addresses
  const eligible = therapists
    .filter(t => {
      const props = t.properties || {};
      const email = props['Email Address']?.email || props['Email']?.email;
      const firstName = props['First Name']?.title?.[0]?.text?.content;
      return email && firstName;
    })
    .slice(0, limit);
  
  console.log(`   ✅ Found ${eligible.length} eligible therapists`);
  return eligible;
}

async function assignVariant(therapist, variant) {
  const pageId = therapist.id;
  const props = therapist.properties || {};
  
  const firstName = props['First Name']?.title?.[0]?.text?.content || '';
  const email = props['Email Address']?.email || props['Email']?.email || '';
  const fullname = props['Fullname']?.rich_text?.[0]?.text?.content || firstName;
  
  // Update Notion with variant assignment and mark as sent
  const updateUrl = `https://api.notion.com/v1/pages/${pageId}`;
  const updateResponse = await fetch(updateUrl, {
    method: 'PATCH',
    headers: {
      'Authorization': `Bearer ${NOTION_TOKEN}`,
      'Content-Type': 'application/json',
      'Notion-Version': '2022-06-28',
    },
    body: JSON.stringify({
      properties: {
        'Research Variant': {
          select: {
            name: variant
          }
        },
        'Research Invite Sent': {
          checkbox: true
        },
        'Research Invite Sent Date': {
          date: {
            start: new Date().toISOString().split('T')[0]
          }
        },
        'Research Experiment ID': {
          rich_text: [
            {
              text: {
                content: 'EXP-01-subject-intro-v1'
              }
            }
          ]
        }
      }
    }),
  });

  if (!updateResponse.ok) {
    const error = await updateResponse.json();
    console.warn(`   ⚠️  Could not update Notion for ${email}: ${error.message}`);
  }

  return {
    pageId,
    firstName,
    email,
    fullname,
    variant
  };
}

async function sendEmail(therapist, variantData) {
  const { firstName, email, fullname, variant, pageId } = therapist;
  const variantCopy = VARIANTS[variant];
  
  // Generate token
  const timestamp = Date.now();
  const therapistId = `VIC-${pageId.slice(-8).toUpperCase()}`;
  
  const tokenPayload = {
    therapist_id: therapistId,
    therapist_name: fullname,
    first_name: firstName,
    practice_name: 'VIC Therapist',
    email: email,
    directory_page_id: pageId,
    therapist_research_id: `research-${timestamp}`,
    variant: variant,
    experiment_id: 'EXP-01-subject-intro-v1',
    exp: Math.floor(Date.now() / 1000) + (30 * 24 * 60 * 60), // 30 days
  };
  
  const token = signToken(tokenPayload);
  
  // Create URLs with tracking
  const surveyUrl = `https://therapair.com.au/research/survey/index.html?token=${encodeURIComponent(token)}&utm_source=email&utm_medium=research&utm_campaign=therapist_research&utm_content=survey&utm_experiment=EXP-01&utm_variant=${variant}`;
  const landingUrl = `https://therapair.com.au/?utm_source=email&utm_medium=research&utm_campaign=therapist_research&utm_content=learn_more&utm_experiment=EXP-01&utm_variant=${variant}&therapist_id=${therapistId}`;

  // Read email template
  const emailHtmlPath = path.join(__dirname, '..', 'therapair-research-email.html');
  let emailHtml = '';
  if (fs.existsSync(emailHtmlPath)) {
    emailHtml = fs.readFileSync(emailHtmlPath, 'utf-8');
    // Replace placeholders
    emailHtml = emailHtml.replace(/\{FIRST_NAME\}/g, firstName);
    emailHtml = emailHtml.replace(/\{SURVEY_URL\}/g, surveyUrl);
    emailHtml = emailHtml.replace(/\{LANDING_URL\}/g, landingUrl);
    
    // Replace intro paragraph with variant-specific intro
    const introMatch = emailHtml.match(/<p[^>]*>Hi \{FIRST_NAME\},[\s\S]*?<\/p>/);
    if (introMatch) {
      const variantIntro = variantCopy.intro.replace(/\{FIRST_NAME\}/g, firstName);
      emailHtml = emailHtml.replace(introMatch[0], `<p style="margin: 0 0 16px 0; color: #4A5568; font-size: 16px; line-height: 1.6;">${variantIntro}</p>`);
    }
  } else {
    throw new Error(`Email template not found at: ${emailHtmlPath}`);
  }

  // Create plain text version
  const plainText = `${variantCopy.intro.replace(/\{FIRST_NAME\}/g, firstName)}

What is Therapair?

Therapair is a small, not-for-profit initiative from Unison Mental Health (https://unisonmentalhealth.com). We're exploring a better way for clients to find therapists who truly fit them—by values, lived experience, and communication style, not just modality or postcode.

We've built a basic sandbox demo with placeholder therapist profiles to show the idea, but the current questions are only a first draft. To make this genuinely useful, we need your perspective as a practitioner.

If you have 5–7 minutes, we'd love your help:
• Share your insights in a short research survey about which questions matter most and how many you'd realistically answer
• Optionally opt in for a one-year free listing when we launch (using your existing public profile)

This research will help us understand which questions create the most meaningful personalisation, how much people are willing to complete, and what actually resonates with both therapists and clients.

JOIN THE RESEARCH SURVEY (5–7 minutes):
${surveyUrl}

Learn more about Therapair:
${landingUrl}

Participation is optional and non-commercial. Your responses are used only for improving therapist–client matching research.

Warm regards,
The Therapair Team

---
Connecting you with therapists who actually get you.

Contact: contact@therapair.com.au
Website: https://therapair.com.au
Email Preferences: https://therapair.com.au/email-preferences.html?email=${encodeURIComponent(email)}

Privacy Policy: https://therapair.com.au/legal/privacy-policy.html
Consent & Removal: https://therapair.com.au/legal/consent-removal.html`;

  // Send email
  const result = await resend.emails.send({
    from: 'Therapair Research <user-research@therapair.com.au>',
    to: email,
    subject: variantCopy.subject,
    html: emailHtml,
    text: plainText,
    reply_to: 'contact@therapair.com.au',
    tags: [
      { name: 'campaign', value: 'therapist_research' },
      { name: 'experiment', value: 'EXP-01-subject-intro-v1' },
      { name: 'variant', value: variant },
      { name: 'therapist_id', value: therapistId },
      { name: 'batch', value: `batch-${new Date().toISOString().split('T')[0]}` },
    ],
  });

  if (result.error) {
    throw new Error(`Resend error: ${result.error.message || JSON.stringify(result.error)}`);
  }

  return {
    email,
    firstName,
    variant,
    messageId: result.data?.id,
    therapistId,
    token
  };
}

async function main() {
  console.log('\n🎯 A/B Test Batch - Research Email Campaign');
  console.log('='.repeat(60));
  console.log(`Batch Size: ${batchSize} recipients`);
  console.log(`Split: ${Math.ceil(batchSize / 2)} Variant A, ${Math.floor(batchSize / 2)} Variant B`);
  console.log('='.repeat(60));

  try {
    // Get eligible therapists
    const eligible = await getEligibleTherapists(batchSize);
    
    if (eligible.length < batchSize) {
      console.warn(`\n⚠️  Warning: Only ${eligible.length} eligible therapists found (requested ${batchSize})`);
    }

    // Assign variants (alternating for even split)
    const assigned = eligible.map((therapist, index) => {
      const variant = index % 2 === 0 ? 'A' : 'B';
      return assignVariant(therapist, variant);
    });

    const variantA = assigned.filter(t => t.variant === 'A');
    const variantB = assigned.filter(t => t.variant === 'B');

    console.log(`\n📊 Assignment:`);
    console.log(`   Variant A: ${variantA.length} recipients`);
    console.log(`   Variant B: ${variantB.length} recipients`);

    // Send emails
    console.log(`\n📧 Sending emails...`);
    const results = [];
    const errors = [];

    for (const therapist of assigned) {
      try {
        const result = await sendEmail(therapist, VARIANTS[therapist.variant]);
        results.push(result);
        console.log(`   ✅ ${therapist.variant}: ${therapist.email} (${therapist.firstName})`);
        
        // Rate limiting - Resend allows 2 requests/second
        await new Promise(resolve => setTimeout(resolve, 600));
      } catch (error) {
        errors.push({ therapist, error: error.message });
        console.error(`   ❌ ${therapist.variant}: ${therapist.email} - ${error.message}`);
      }
    }

    // Summary
    console.log(`\n${'='.repeat(60)}`);
    console.log('📊 Batch Summary:');
    console.log(`   ✅ Sent: ${results.length} emails`);
    console.log(`   ❌ Errors: ${errors.length} emails`);
    console.log(`   Variant A: ${results.filter(r => r.variant === 'A').length} sent`);
    console.log(`   Variant B: ${results.filter(r => r.variant === 'B').length} sent`);
    
    if (errors.length > 0) {
      console.log(`\n⚠️  Errors:`);
      errors.forEach(e => {
        console.log(`   - ${e.therapist.email}: ${e.error}`);
      });
    }

    // Save batch data
    const batchData = {
      batch_id: `batch-${new Date().toISOString().split('T')[0]}-${Date.now()}`,
      sent_at: new Date().toISOString(),
      batch_size: batchSize,
      results: results.map(r => ({
        email: r.email,
        firstName: r.firstName,
        variant: r.variant,
        messageId: r.messageId,
        therapistId: r.therapistId
      })),
      errors: errors.map(e => ({
        email: e.therapist.email,
        error: e.error
      }))
    };

    const batchPath = path.join(__dirname, '..', `batch-${batchData.batch_id}.json`);
    fs.writeFileSync(batchPath, JSON.stringify(batchData, null, 2));
    console.log(`\n📄 Batch data saved to: ${batchPath}`);

    console.log(`\n💡 Next Steps:`);
    console.log(`   1. Check Notion dashboard for tracking data`);
    console.log(`   2. Monitor Resend dashboard for opens/clicks`);
    console.log(`   3. Review survey responses in Notion`);
    console.log(`   4. Analyze A/B test results before sending next batch`);
    console.log(`\n✅ Batch complete!\n`);

  } catch (error) {
    console.error('\n❌ Error:', error.message);
    console.error(error.stack);
    process.exit(1);
  }
}

main().catch((error) => {
  console.error('\n❌ Fatal error:', error.message);
  console.error(error.stack);
  process.exit(1);
});

