#!/usr/bin/env node
/**
 * Verify all existing tokens in test-tokens.json and tino-token-data.json
 * Usage: node verify-all-tokens.mjs
 */

import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const baseUrl = 'https://therapair.com.au';

async function verifyToken(token, therapistName) {
  try {
    const url = `${baseUrl}/api/research/session.php?token=${encodeURIComponent(token)}`;
    const response = await fetch(url);
    const data = await response.json();
    
    if (response.ok && data.success) {
      console.log(`  ✅ ${therapistName}: Valid`);
      return true;
    } else {
      console.log(`  ❌ ${therapistName}: ${data.error || 'Invalid'}`);
      return false;
    }
  } catch (error) {
    console.log(`  ❌ ${therapistName}: Error - ${error.message}`);
    return false;
  }
}

async function main() {
  console.log('\n🔍 Verifying All Research Tokens');
  console.log('='.repeat(60));
  
  let totalTokens = 0;
  let validTokens = 0;
  
  // Check test-tokens.json
  const testTokensPath = path.join(__dirname, '..', 'test-tokens.json');
  if (fs.existsSync(testTokensPath)) {
    console.log('\n📋 Checking test-tokens.json...');
    const tokens = JSON.parse(fs.readFileSync(testTokensPath, 'utf-8'));
    for (const tokenData of tokens) {
      totalTokens++;
      const isValid = await verifyToken(tokenData.token, tokenData.therapist_name);
      if (isValid) validTokens++;
    }
  }
  
  // Check tino-token-data.json
  const tinoTokenPath = path.join(__dirname, '..', 'tino-token-data.json');
  if (fs.existsSync(tinoTokenPath)) {
    console.log('\n📋 Checking tino-token-data.json...');
    const tokenData = JSON.parse(fs.readFileSync(tinoTokenPath, 'utf-8'));
    totalTokens++;
    const isValid = await verifyToken(tokenData.token, tokenData.therapist_name);
    if (isValid) validTokens++;
  }
  
  console.log('\n' + '='.repeat(60));
  console.log(`📊 Results: ${validTokens}/${totalTokens} tokens are valid`);
  if (validTokens < totalTokens) {
    console.log(`⚠️  ${totalTokens - validTokens} token(s) need to be regenerated`);
    process.exit(1);
  } else {
    console.log('✅ All tokens are valid!');
  }
}

main().catch((error) => {
  console.error('\n❌ Error:', error.message);
  process.exit(1);
});

