#!/usr/bin/env node
/**
 * Send research invitation email via Resend
 * Usage: node send-research-email.mjs [RESEND_API_KEY]
 */

import { Resend } from 'resend';
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

// Get API key from command line, .env file, or environment variable
const apiKey = process.argv[2] || process.env.RESEND_API_KEY;

if (!apiKey) {
  console.error('❌ Error: Resend API key required');
  console.error('Usage: node send-research-email.mjs YOUR_API_KEY');
  console.error('Or set RESEND_API_KEY environment variable');
  process.exit(1);
}

const resend = new Resend(apiKey);

// Read email HTML (use new template with logo and design system)
const emailHtmlPath = path.join(__dirname, '..', 'therapair-research-email.html');
let emailHtml = '';
if (fs.existsSync(emailHtmlPath)) {
    emailHtml = fs.readFileSync(emailHtmlPath, 'utf-8');
    // Replace token placeholder if needed
    // Token will be inserted by the calling script
} else {
    // Fallback to old template if new one doesn't exist
    const oldPath = path.join(__dirname, '..', 'tino-research-email.html');
    emailHtml = fs.existsSync(oldPath) ? fs.readFileSync(oldPath, 'utf-8') : '';
}

// Plain text version
const plainText = `
Help us build a better therapist-matching system

Hi Tino,

As practitioners, we know that the right fit between therapist and client can change everything. Therapair is a small, non-profit initiative from Unison Mental Health that's building a new way to match people with the therapists who truly fit them—by values, lived experience, and communication style.

We're currently running a pilot with real practitioners and have created a basic sandbox demo with 100 realistic therapist profiles to show the concept. However, we know the current questions are just a starting point—we need to make them much more sophisticated to create truly personalised connections between therapists and clients.

We need your help to shape the future:
• Try our sandbox demo (basic version) to see the concept in action
• Share your insights in a short 5-7 minute research survey about what questions matter most and how many you'd be willing to answer
• If you'd like to be included when we launch, we're offering a one-year free listing (using your existing public profile)

This research is crucial—we need to understand which questions create the deepest personalisation, how many questions people will actually answer, and what options resonate most with both therapists and clients.

Your perspective as a practitioner is invaluable in shaping a platform that truly serves both therapists and clients.

JOIN THE RESEARCH SURVEY:
https://therapair.com.au/research/survey/index.html?token=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ0aGVyYXBpc3RfaWQiOiJWSUMtVElOTy0wMDEiLCJ0aGVyYXBpc3RfbmFtZSI6IlRpbm8gTWFuIiwiZmlyc3RfbmFtZSI6IlRpbm8iLCJwcmFjdGljZV9uYW1lIjoiVGhlcmFwYWlyIFJlc2VhcmNoIiwiZW1haWwiOiJ0aW5vbWFuQG1lLmNvbSIsImRpcmVjdG9yeV9wYWdlX2lkIjpudWxsLCJ0aGVyYXBpc3RfcmVzZWFyY2hfaWQiOiJ0aW5vLTE3NjM0MzY3MjMzMjYiLCJleHAiOjE3NjYwMjg3MjN9.NMO4BWfCgCXClMO-wq41GlHDnMK-woMTI3E7dnYmw5tvNsOFwr09wrvCuT8y&utm_source=email&utm_medium=research&utm_campaign=therapist_research&utm_content=survey

TRY THE SANDBOX DEMO:
https://therapair.com.au/sandbox/sandbox-demo.html?utm_source=email&utm_medium=research&utm_campaign=therapist_research&utm_content=sandbox_demo&therapist_id=VIC-TINO-001

LEARN MORE:
https://therapair.com.au/documentation.html?utm_source=email&utm_medium=research&utm_campaign=therapist_research&utm_content=learn_more&therapist_id=VIC-TINO-001

Participation is optional and non-commercial. All information is used solely for improving therapist-client matching research. Thank you for contributing your perspective to a more inclusive future of care.

Warm regards,
The Therapair Team
Unison Mental Health

---
Therapair is a not-for-profit research initiative under Unison Mental Health.
All information shared remains confidential and used only for product research and ethical development.

Privacy Policy: https://therapair.com.au/legal/privacy-policy.html
Consent & Removal: https://therapair.com.au/legal/consent-removal.html
Contact Us: contact@therapair.com.au
`;

async function sendEmail() {
  console.log('\n📧 Sending Research Invitation Email');
  console.log('='.repeat(60));
  console.log('To: tinokuhn@gmail.com');
  console.log('Subject: Help us build a better therapist-matching system');
  console.log('='.repeat(60));

  try {
    const result = await resend.emails.send({
      from: 'Therapair Research <onboarding@resend.dev>', // Change to contact@therapair.com.au once domain is verified
      to: 'tinokuhn@gmail.com',
      subject: 'Help us build a better therapist-matching system',
      html: emailHtml,
      text: plainText,
      reply_to: 'contact@therapair.com.au',
    });

    if (result.error) {
      console.error('\n❌ Error sending email:');
      console.error(result.error);
      process.exit(1);
    }

    console.log('\n✅ Email sent successfully!');
    console.log(`📬 Message ID: ${result.data?.id || 'unknown'}`);
    console.log('\n💡 Next Steps:');
    console.log('   1. Check your inbox (and spam folder)');
    console.log('   2. Click the survey link to test the flow');
    console.log('   3. Complete the survey to verify responses are saved');
    console.log('   4. Check Notion database to confirm your response\n');
  } catch (error) {
    console.error('\n❌ Error:', error.message);
    if (error.response) {
      console.error('Response:', error.response);
    }
    process.exit(1);
  }
}

sendEmail();

