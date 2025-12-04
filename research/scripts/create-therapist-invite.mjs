#!/usr/bin/env node
/**
 * Create a therapist profile in Notion directory and generate invitation email
 * Usage: node create-therapist-invite.mjs
 */

import crypto from 'node:crypto';
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const NOTION_TOKEN = process.env.NOTION_TOKEN || '';
const DIRECTORY_DB_ID = process.env.DIRECTORY_DB_ID || '';
const SECRET = process.env.NOTION_SECRET || '';

// Therapist data
const therapist = {
  therapist_id: 'VIC-TINO-001',
  therapist_name: 'Tino Man',
  first_name: 'Tino',
  practice_name: 'Therapair Research',
  email: 'tinoman@me.com',
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

async function createNotionTherapist(therapist) {
  console.log('\n📝 Creating therapist profile in Notion...');
  
  // Try to find existing entry by email first
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
    }
  }

  // Create new entry if not found
  if (!directoryPageId) {
    const createUrl = 'https://api.notion.com/v1/pages';
    const properties = {};

    // Try to set common properties (adjust based on your actual schema)
    if (therapist.therapist_name) {
      properties['Name'] = {
        title: [{ text: { content: therapist.therapist_name } }],
      };
    }
    if (therapist.email) {
      properties['Email'] = { email: therapist.email };
    }
    if (therapist.profession) {
      properties['Profession'] = { select: { name: therapist.profession } };
    }
    if (therapist.location) {
      properties['Location'] = { rich_text: [{ text: { content: therapist.location } }] };
    }
    if (therapist.practice_name) {
      properties['Practice Name'] = { rich_text: [{ text: { content: therapist.practice_name } }] };
    }

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
      console.error(`     This is okay - we'll proceed with token generation anyway.`);
    }
  }

  return directoryPageId;
}

async function main() {
  console.log('\n🎯 Creating Therapist Invitation');
  console.log('='.repeat(60));
  console.log(`Therapist: ${therapist.therapist_name}`);
  console.log(`Email: ${therapist.email}`);
  console.log('='.repeat(60));

  // Step 1: Create Notion entry
  const directoryPageId = await createNotionTherapist(therapist);
  
  // Step 2: Generate token
  console.log('\n🔑 Generating secure token...');
  const tokenPayload = {
    therapist_id: therapist.therapist_id,
    therapist_name: therapist.therapist_name,
    first_name: therapist.first_name,
    practice_name: therapist.practice_name,
    email: therapist.email,
    directory_page_id: directoryPageId,
    therapist_research_id: `tino-${Date.now()}`,
    exp: Math.floor(Date.now() / 1000) + (30 * 24 * 60 * 60), // 30 days
  };
  
  const token = signToken(tokenPayload);
  console.log(`  ✅ Token generated (expires in 30 days)`);

  // Step 3: Create URLs
  const surveyUrl = `https://therapair.com.au/research/survey/index.html?token=${encodeURIComponent(token)}`;
  const sandboxUrl = `https://therapair.com.au/sandbox/sandbox-demo.html?utm_source=email&utm_medium=research&utm_campaign=therapist_research&therapist_id=${therapist.therapist_id}`;
  const landingUrl = `https://therapair.com.au/?utm_source=email&utm_medium=research&utm_campaign=therapist_research&therapist_id=${therapist.therapist_id}`;

  // Step 4: Generate email
  const emailContent = `
Subject: Help us build a better therapist-matching system

Hi ${therapist.first_name},

Thank you for your interest in the Therapair research study.

As practitioners, we know that the right fit between therapist and client can change everything. Therapair is building a new way to match people with therapists who truly fit them—by values, lived experience, and communication style.

We need your help to shape the future:
• Try our sandbox demo to see the concept in action
• Share your insights in a short 5-7 minute research survey
• Help us understand which questions create the deepest personalisation

YOUR PERSONALIZED SURVEY LINK:
${surveyUrl}

TRY THE SANDBOX DEMO:
${sandboxUrl}

VISIT THE LANDING PAGE:
${landingUrl}

This token is valid for 30 days and is linked to your email address (${therapist.email}).

Your responses will help us build a platform that truly serves both therapists and clients.

Best regards,
Therapair Team

---
Therapair Research Study
contact@therapair.com.au
`;

  // Save email to file
  const emailPath = path.join(__dirname, '..', 'tino-invitation-email.txt');
  fs.writeFileSync(emailPath, emailContent);

  // Save token data
  const tokenData = {
    ...therapist,
    directory_page_id: directoryPageId,
    token,
    survey_url: surveyUrl,
    sandbox_url: sandboxUrl,
    landing_url: landingUrl,
    created_at: new Date().toISOString(),
  };
  const tokenPath = path.join(__dirname, '..', 'tino-token-data.json');
  fs.writeFileSync(tokenPath, JSON.stringify(tokenData, null, 2));

  // Output summary
  console.log('\n' + '='.repeat(60));
  console.log('✅ Invitation Created Successfully!');
  console.log('='.repeat(60));
  console.log(`\n📧 Email saved to: ${emailPath}`);
  console.log(`📄 Token data saved to: ${tokenPath}`);
  console.log(`\n🔗 Your Survey Link:`);
  console.log(`   ${surveyUrl}`);
  console.log(`\n🎯 Your Sandbox Link:`);
  console.log(`   ${sandboxUrl}`);
  console.log(`\n🏠 Your Landing Page Link:`);
  console.log(`   ${landingUrl}`);
  
  if (directoryPageId) {
    console.log(`\n📊 Notion Directory Entry:`);
    console.log(`   https://notion.so/${directoryPageId.replace(/-/g, '')}`);
  }
  
  console.log(`\n💡 Next Steps:`);
  console.log(`   1. Copy the email content from ${emailPath}`);
  console.log(`   2. Send it to ${therapist.email}`);
  console.log(`   3. Complete the survey to test response recording`);
  console.log(`   4. Check Notion database to verify your response was saved\n`);
}

main().catch((error) => {
  console.error('\n❌ Error:', error.message);
  console.error(error.stack);
  process.exit(1);
});

