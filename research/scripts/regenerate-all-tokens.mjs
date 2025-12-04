#!/usr/bin/env node
/**
 * Regenerate all tokens with correct signature encoding
 * This fixes tokens that were generated with the old (incorrect) encoding
 * Usage: node regenerate-all-tokens.mjs
 */

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

const SECRET = process.env.NOTION_SECRET || process.env.RESEARCH_TOKEN_SECRET || 'I1WPkhdJs6Fw2XAa+BRH2hCiezYuP8FjT1xoG5tp/koajYB7tlleXc3lzvwVUqNv';

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

async function main() {
  console.log('\n🔄 Regenerating All Research Tokens');
  console.log('='.repeat(60));
  
  // Regenerate test-tokens.json
  const testTokensPath = path.join(__dirname, '..', 'test-tokens.json');
  if (fs.existsSync(testTokensPath)) {
    console.log('\n📋 Regenerating test-tokens.json...');
    const tokens = JSON.parse(fs.readFileSync(testTokensPath, 'utf-8'));
    
    for (const tokenData of tokens) {
      const payload = {
        therapist_id: tokenData.therapist_id,
        therapist_name: tokenData.therapist_name,
        first_name: tokenData.first_name,
        practice_name: tokenData.practice_name,
        email: tokenData.email,
        directory_page_id: tokenData.directory_page_id || null,
        therapist_research_id: tokenData.therapist_research_id,
        exp: Math.floor(Date.now() / 1000) + (30 * 24 * 60 * 60), // 30 days
      };
      
      const newToken = signToken(payload);
      tokenData.token = newToken;
      tokenData.survey_url = `https://therapair.com.au/research/survey/index.html?token=${encodeURIComponent(newToken)}`;
      
      console.log(`  ✅ Regenerated token for ${tokenData.therapist_name}`);
    }
    
    fs.writeFileSync(testTokensPath, JSON.stringify(tokens, null, 2));
    console.log(`\n✅ Updated ${testTokensPath}`);
  }
  
  // Regenerate tino-token-data.json
  const tinoTokenPath = path.join(__dirname, '..', 'tino-token-data.json');
  if (fs.existsSync(tinoTokenPath)) {
    console.log('\n📋 Regenerating tino-token-data.json...');
    const tokenData = JSON.parse(fs.readFileSync(tinoTokenPath, 'utf-8'));
    
    const payload = {
      therapist_id: tokenData.therapist_id,
      therapist_name: tokenData.therapist_name,
      first_name: tokenData.first_name,
      practice_name: tokenData.practice_name,
      email: tokenData.email,
      directory_page_id: tokenData.directory_page_id || null,
      therapist_research_id: tokenData.therapist_research_id || `tino-${Date.now()}`,
      exp: Math.floor(Date.now() / 1000) + (30 * 24 * 60 * 60), // 30 days
    };
    
    const newToken = signToken(payload);
    tokenData.token = newToken;
    tokenData.survey_url = `https://therapair.com.au/research/survey/index.html?token=${encodeURIComponent(newToken)}`;
    tokenData.sandbox_url = `https://therapair.com.au/sandbox/sandbox-demo.html?utm_source=email&utm_medium=research&utm_campaign=therapist_research&therapist_id=${tokenData.therapist_id}`;
    tokenData.landing_url = `https://therapair.com.au/?utm_source=email&utm_medium=research&utm_campaign=therapist_research&therapist_id=${tokenData.therapist_id}`;
    tokenData.created_at = new Date().toISOString();
    
    fs.writeFileSync(tinoTokenPath, JSON.stringify(tokenData, null, 2));
    console.log(`  ✅ Regenerated token for ${tokenData.therapist_name}`);
    console.log(`\n✅ Updated ${tinoTokenPath}`);
  }
  
  console.log('\n' + '='.repeat(60));
  console.log('✅ All tokens regenerated with correct encoding!');
  console.log('\n💡 Next Steps:');
  console.log('   1. Run verify-all-tokens.mjs to verify all tokens work');
  console.log('   2. Resend invitation emails with the new tokens if needed\n');
}

main().catch((error) => {
  console.error('\n❌ Error:', error.message);
  console.error(error.stack);
  process.exit(1);
});

