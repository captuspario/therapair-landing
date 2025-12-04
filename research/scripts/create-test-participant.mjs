#!/usr/bin/env node
/**
 * Create a test participant in VIC therapists database and send research invitation
 * Usage: node create-test-participant.mjs <email> <name> [RESEND_API_KEY]
 * Example: node create-test-participant.mjs tinoman@me.com "Tino Kuhn" YOUR_API_KEY
 */

import { Resend } from 'resend';
import crypto from 'node:crypto';
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import dotenv from 'dotenv';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Load .env file from research directory
const envPath = path.join(__dirname, '..', '.env');
if (fs.existsSync(envPath)) {
  dotenv.config({ path: envPath });
}

// Get arguments
const email = process.argv[2];
const name = process.argv[3];
const apiKey = process.argv[4] || process.env.RESEND_API_KEY;

if (!email || !name) {
  console.error('❌ Error: Email and name required');
  console.error('Usage: node create-test-participant.mjs <email> <name> [RESEND_API_KEY]');
  console.error('Example: node create-test-participant.mjs tinoman@me.com "Tino Kuhn" YOUR_API_KEY');
  process.exit(1);
}

if (!apiKey) {
  console.error('❌ Error: Resend API key required');
  console.error('Usage: node create-test-participant.mjs <email> <name> YOUR_API_KEY');
  console.error('Or set RESEND_API_KEY environment variable');
  process.exit(1);
}

// Configuration
const NOTION_TOKEN = process.env.NOTION_TOKEN || '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';
const DIRECTORY_DB_ID = process.env.DIRECTORY_DB_ID || '28c5c25944da80a48d85fd43119f4ec1'; // VIC Therapist DB
const SECRET = process.env.NOTION_SECRET || process.env.RESEARCH_TOKEN_SECRET || 'I1WPkhdJs6Fw2XAa+BRH2hCiezYuP8FjT1xoG5tp/koajYB7tlleXc3lzvwVUqNv';
const resend = new Resend(apiKey);

// Parse name into first and last
const nameParts = name.trim().split(/\s+/);
const firstName = nameParts[0] || name;
const lastName = nameParts.slice(1).join(' ') || '';

// Generate unique therapist ID
const timestamp = Date.now();
const therapistId = `VIC-TEST-${timestamp.toString().slice(-6)}`;

// Therapist data
const therapist = {
  therapist_id: therapistId,
  therapist_name: name,
  first_name: firstName,
  last_name: lastName,
  practice_name: 'Test Participant',
  email: email,
  profession: 'Psychologist',
  location: 'Melbourne, VIC',
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
  // Convert Buffer directly to base64url (not via binary string)
  const signatureB64 = signature.toString('base64')
    .replace(/\+/g, '-')
    .replace(/\//g, '_')
    .replace(/=/g, '');
  return `${signedPortion}.${signatureB64}`;
}

async function findOrCreateNotionTherapist(therapist) {
  console.log('\n📝 Checking for existing therapist entry in VIC database...');
  
  // Try to find existing entry by email
  const searchUrl = `https://api.notion.com/v1/databases/${DIRECTORY_DB_ID}/query`;
  const searchResponse = await fetch(searchUrl, {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${NOTION_TOKEN}`,
      'Content-Type': 'application/json',
      'Notion-Version': '2022-06-28',
    },
    body: JSON.stringify({
      filter: {
        property: 'Email',
        email: {
          equals: therapist.email,
        },
      },
    }),
  });

  let directoryPageId = null;

  if (searchResponse.ok) {
    const searchData = await searchResponse.json();
    if (searchData.results && searchData.results.length > 0) {
      directoryPageId = searchData.results[0].id;
      console.log(`  ✅ Found existing therapist entry: ${directoryPageId}`);
      console.log(`  📄 Notion URL: https://notion.so/${directoryPageId.replace(/-/g, '')}`);
      return directoryPageId;
    }
  }

  // Create new entry if not found
  console.log('  ➕ Creating new therapist entry in VIC database...');
  const createUrl = 'https://api.notion.com/v1/pages';
  
  // Build properties based on actual VIC database schema
  // Based on DATABASE-GUIDE.md, the VIC database uses:
  // - "Name" (Title) - main display name
  // - "Email Address" (Email) - not "Email"
  // - "First Name" (Text)
  // - "Last Name" (Text) 
  // - "Profession/Key Qualification/s" (Text)
  // - "Business Name" (Text)
  // - "Region" (Text)
  const properties = {};

  // Based on error messages from Notion:
  // - "First Name" is the title property (not Fullname)
  // - "Fullname" should be rich_text (not title)
  // - "Region" should be select (not rich_text)
  
  // Title property - "First Name" (this is the title property!)
  if (therapist.first_name) {
    properties['First Name'] = {
      title: [{ text: { content: therapist.first_name } }],
    };
  }
  
  // Fullname as rich_text (not title)
  if (therapist.therapist_name) {
    properties['Fullname'] = { 
      rich_text: [{ text: { content: therapist.therapist_name } }] 
    };
  }
  
  // Email property - "Email Address"
  if (therapist.email) {
    properties['Email Address'] = { email: therapist.email };
  }
  
  // Last Name
  if (therapist.last_name) {
    properties['Last Name'] = { rich_text: [{ text: { content: therapist.last_name } }] };
  }
  
  // Profession property - "Profession/Key Qualification/s"
  if (therapist.profession) {
    properties['Profession/Key Qualification/s'] = { rich_text: [{ text: { content: therapist.profession } }] };
  }
  
  // Business Name
  if (therapist.practice_name) {
    properties['Business Name'] = { rich_text: [{ text: { content: therapist.practice_name } }] };
  }
  
  // Region - skip for now as it needs to be a select with specific options
  // We don't know the available options, so we'll skip this to avoid errors

  const createResponse = await fetch(createUrl, {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${NOTION_TOKEN}`,
      'Content-Type': 'application/json',
      'Notion-Version': '2022-06-28',
    },
    body: JSON.stringify({
      parent: { database_id: DIRECTORY_DB_ID },
      properties,
    }),
  });

  if (createResponse.ok) {
    const createData = await createResponse.json();
    directoryPageId = createData.id;
    console.log(`  ✅ Created new therapist entry: ${directoryPageId}`);
    console.log(`  📄 Notion URL: https://notion.so/${directoryPageId.replace(/-/g, '')}`);
  } else {
    const error = await createResponse.json();
    console.error(`  ⚠️  Could not create Notion entry: ${error.message || createResponse.status}`);
    console.error(`     Response: ${JSON.stringify(error, null, 2)}`);
    console.error(`     This is okay - we'll proceed with token generation anyway.`);
  }

  return directoryPageId;
}

async function main() {
  console.log('\n🎯 Creating Test Participant and Research Invitation');
  console.log('='.repeat(60));
  console.log(`Name: ${therapist.therapist_name}`);
  console.log(`Email: ${therapist.email}`);
  console.log(`Therapist ID: ${therapist.therapist_id}`);
  console.log('='.repeat(60));

  // Step 1: Find or create Notion entry in VIC database
  const directoryPageId = await findOrCreateNotionTherapist(therapist);
  
  // Step 2: Generate token
  console.log('\n🔑 Generating secure token...');
  const tokenPayload = {
    therapist_id: therapist.therapist_id,
    therapist_name: therapist.therapist_name,
    first_name: therapist.first_name,
    practice_name: therapist.practice_name,
    email: therapist.email,
    directory_page_id: directoryPageId,
    therapist_research_id: `test-${timestamp}`,
    exp: Math.floor(Date.now() / 1000) + (30 * 24 * 60 * 60), // 30 days
  };
  
  const token = signToken(tokenPayload);
  console.log(`  ✅ Token generated (expires in 30 days)`);

  // Step 3: Create URLs with tracking
  const surveyUrl = `https://therapair.com.au/research/survey/index.html?token=${encodeURIComponent(token)}&utm_source=email&utm_medium=research&utm_campaign=therapist_research&utm_content=survey`;
  const sandboxUrl = `https://therapair.com.au/sandbox/sandbox-demo.html?utm_source=email&utm_medium=research&utm_campaign=therapist_research&utm_content=sandbox_demo&therapist_id=${therapist.therapist_id}`;
  const landingUrl = `https://therapair.com.au/?utm_source=email&utm_medium=research&utm_campaign=therapist_research&utm_content=learn_more&therapist_id=${therapist.therapist_id}`;

  // Step 4: Read and customize email HTML template
  const emailHtmlPath = path.join(__dirname, '..', 'therapair-research-email.html');
  let emailHtml = '';
  if (fs.existsSync(emailHtmlPath)) {
    emailHtml = fs.readFileSync(emailHtmlPath, 'utf-8');
    // Replace placeholders
    emailHtml = emailHtml.replace(/TOKEN_PLACEHOLDER/g, encodeURIComponent(token));
    emailHtml = emailHtml.replace(/\{THERAPIST_ID\}/g, therapist.therapist_id);
    emailHtml = emailHtml.replace(/\{FIRST_NAME\}/g, therapist.first_name);
    emailHtml = emailHtml.replace(/\{EMAIL\}/g, encodeURIComponent(therapist.email));
  } else {
    console.error('❌ Error: Email template not found at:', emailHtmlPath);
    process.exit(1);
  }

  // Step 5: Create plain text version
  const plainText = `Help shape a better therapist-matching system

Hi ${therapist.first_name},

We found your details in the publicly available VIC therapists register and thought you might be interested in shaping a new approach to therapist–client matching.

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
Email Preferences: https://therapair.com.au/email-preferences.html?email=${encodeURIComponent(therapist.email)}

Privacy Policy: https://therapair.com.au/legal/privacy-policy.html
Consent & Removal: https://therapair.com.au/legal/consent-removal.html`;

  // Step 6: Send email via Resend
  console.log('\n📧 Sending Research Invitation Email');
  console.log('='.repeat(60));
  console.log(`To: ${therapist.email}`);
  console.log('Subject: Help shape a better therapist-matching system');
  console.log('='.repeat(60));

  try {
    const result = await resend.emails.send({
      from: 'Therapair Research <user-research@therapair.com.au>',
      to: therapist.email,
      subject: 'Help shape a better therapist-matching system',
      html: emailHtml,
      text: plainText,
      reply_to: 'contact@therapair.com.au',
      tags: [
        { name: 'campaign', value: 'therapist_research' },
        { name: 'therapist_id', value: therapist.therapist_id },
        { name: 'test_participant', value: 'true' },
      ],
    });

    if (result.error) {
      console.error('\n❌ Error sending email:');
      console.error(result.error);
      process.exit(1);
    }

    console.log('\n✅ Email sent successfully!');
    console.log(`📬 Message ID: ${result.data?.id || 'unknown'}`);
    console.log(`\n🔗 Survey Link:`);
    console.log(`   ${surveyUrl}`);
    console.log(`\n🎯 Sandbox Link:`);
    console.log(`   ${sandboxUrl}`);
    console.log(`\n💡 Next Steps:`);
    console.log('   1. Check your inbox (and spam folder)');
    console.log('   2. Click the survey link to test the flow');
    console.log('   3. Complete the survey to verify responses are saved');
    console.log('   4. Test feedback widget on homepage, sandbox, and survey pages');
    console.log('   5. Check Notion databases to confirm tracking:\n');
    console.log('      - VIC Therapist DB: Entry with your email');
    console.log('      - Survey Responses DB: Your survey submission');
    console.log('      - Feedback DB: Any feedback widget entries\n');
    
    // Save token data for reference
    const tokenData = {
      ...therapist,
      directory_page_id: directoryPageId,
      token,
      survey_url: surveyUrl,
      sandbox_url: sandboxUrl,
      landing_url: landingUrl,
      created_at: new Date().toISOString(),
      test_participant: true,
    };
    const tokenPath = path.join(__dirname, '..', `test-participant-${therapist.email.replace('@', '-at-')}.json`);
    fs.writeFileSync(tokenPath, JSON.stringify(tokenData, null, 2));
    console.log(`📄 Token data saved to: ${tokenPath}\n`);
  } catch (error) {
    console.error('\n❌ Error:', error.message);
    if (error.response) {
      console.error('Response:', error.response);
    }
    process.exit(1);
  }
}

main().catch((error) => {
  console.error('\n❌ Error:', error.message);
  console.error(error.stack);
  process.exit(1);
});

