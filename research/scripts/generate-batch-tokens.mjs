#!/usr/bin/env node
/**
 * Generate multiple test tokens for different therapists
 * Used for testing email delivery, token linking, and response tracking
 */

import crypto from 'node:crypto';
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const secret = 'I1WPkhdJs6Fw2XAa+BRH2hCiezYuP8FjT1xoG5tp/koajYB7tlleXc3lzvwVqNv';

// Test therapist data
const testTherapists = [
  {
    therapist_id: 'VIC-TEST-001',
    therapist_name: 'Sarah Mitchell',
    first_name: 'Sarah',
    practice_name: 'Mindful Connections',
    email: 'sarah.mitchell+test@therapair.com.au',
    directory_page_id: null,
    therapist_research_id: 'test-001',
  },
  {
    therapist_id: 'VIC-TEST-002',
    therapist_name: 'James Chen',
    first_name: 'James',
    practice_name: 'Harmony Therapy',
    email: 'james.chen+test@therapair.com.au',
    directory_page_id: null,
    therapist_research_id: 'test-002',
  },
  {
    therapist_id: 'VIC-TEST-003',
    therapist_name: 'Emma Thompson',
    first_name: 'Emma',
    practice_name: 'Wellness Collective',
    email: 'emma.thompson+test@therapair.com.au',
    directory_page_id: null,
    therapist_research_id: 'test-003',
  },
];

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
    .createHmac('sha256', secret)
    .update(signedPortion)
    .digest();
  const signatureB64 = base64urlEncode(signature.toString('binary'));
  return `${signedPortion}.${signatureB64}`;
}

const tokens = testTherapists.map((therapist) => {
  const payload = {
    ...therapist,
    exp: Math.floor(Date.now() / 1000) + (30 * 24 * 60 * 60), // 30 days
  };
  const token = signToken(payload);
  return {
    ...therapist,
    token,
    survey_url: `https://therapair.com.au/research/survey/index.html?token=${encodeURIComponent(token)}`,
    sandbox_url: `https://therapair.com.au/sandbox/sandbox-demo.html?utm_source=email&utm_medium=research&utm_campaign=therapist_research&therapist_id=${therapist.therapist_id}`,
    landing_url: `https://therapair.com.au/?utm_source=email&utm_medium=research&utm_campaign=therapist_research&therapist_id=${therapist.therapist_id}`,
  };
});

// Generate email template
const emailTemplate = tokens
  .map(
    (t) => `
========================================
Therapist: ${t.therapist_name}
Email: ${t.email}
Therapist ID: ${t.therapist_id}
========================================

Hi ${t.first_name},

Thank you for your interest in the Therapair research study.

Your personalized survey link:
${t.survey_url}

Try the sandbox demo:
${t.sandbox_url}

Visit the landing page:
${t.landing_url}

This token is valid for 30 days and is linked to your email address.

Best regards,
Therapair Team
`
  )
  .join('\n\n');

// Save to file
const outputPath = path.join(__dirname, '..', 'test-tokens.json');
const emailPath = path.join(__dirname, '..', 'test-email-template.txt');

fs.writeFileSync(outputPath, JSON.stringify(tokens, null, 2));
fs.writeFileSync(emailPath, emailTemplate);

console.log('\n✅ Generated test tokens for', tokens.length, 'therapists\n');
console.log('📄 Token data saved to:', outputPath);
console.log('📧 Email template saved to:', emailPath);
console.log('\n📋 Quick Reference:\n');

tokens.forEach((t) => {
  console.log(`${t.therapist_name} (${t.email})`);
  console.log(`  Survey: ${t.survey_url}`);
  console.log(`  Sandbox: ${t.sandbox_url}`);
  console.log('');
});

console.log('\n💡 Next steps:');
console.log('1. Review test-tokens.json for all token data');
console.log('2. Use test-email-template.txt to send test emails');
console.log('3. Run npm run research:test-flow to validate the entire pipeline\n');

