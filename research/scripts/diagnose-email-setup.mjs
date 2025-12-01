#!/usr/bin/env node
/**
 * Diagnose Resend MCP and Email Setup
 * Checks if everything is configured correctly
 */

import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

console.log('\n🔍 Diagnosing Resend Email Setup');
console.log('='.repeat(60));

// Check 1: Resend MCP Server
console.log('\n1️⃣ Checking Resend MCP Server...');
const mcpPath = '/Users/tino/Projects/Therapair/email-resend-mcp/build/index.js';
if (fs.existsSync(mcpPath)) {
  console.log('   ✅ MCP server file exists:', mcpPath);
  const stats = fs.statSync(mcpPath);
  console.log('   📦 File size:', stats.size, 'bytes');
  console.log('   📅 Last modified:', stats.mtime.toISOString());
} else {
  console.log('   ❌ MCP server file NOT found:', mcpPath);
  console.log('   💡 Run: cd email-resend-mcp && npm run build');
}

// Check 2: Email HTML file
console.log('\n2️⃣ Checking Email HTML...');
const emailHtmlPath = path.join(__dirname, '..', 'tino-research-email.html');
if (fs.existsSync(emailHtmlPath)) {
  console.log('   ✅ Email HTML exists:', emailHtmlPath);
  const html = fs.readFileSync(emailHtmlPath, 'utf-8');
  console.log('   📦 File size:', html.length, 'characters');
  const hasToken = html.includes('token=');
  console.log('   🔑 Contains token:', hasToken ? '✅ Yes' : '❌ No');
  const hasSurveyLink = html.includes('research/survey');
  console.log('   🔗 Contains survey link:', hasSurveyLink ? '✅ Yes' : '❌ No');
} else {
  console.log('   ❌ Email HTML NOT found:', emailHtmlPath);
}

// Check 3: Token data
console.log('\n3️⃣ Checking Token Data...');
const tokenPath = path.join(__dirname, '..', 'tino-token-data.json');
if (fs.existsSync(tokenPath)) {
  console.log('   ✅ Token data exists:', tokenPath);
  const tokenData = JSON.parse(fs.readFileSync(tokenPath, 'utf-8'));
  console.log('   📧 Email:', tokenData.email || 'Not found');
  console.log('   🔑 Token:', tokenData.token ? '✅ Present' : '❌ Missing');
  console.log('   🔗 Survey URL:', tokenData.survey_url ? '✅ Present' : '❌ Missing');
} else {
  console.log('   ❌ Token data NOT found:', tokenPath);
}

// Check 4: Send script
console.log('\n4️⃣ Checking Send Script...');
const sendScriptPath = path.join(__dirname, 'send-research-email.mjs');
if (fs.existsSync(sendScriptPath)) {
  console.log('   ✅ Send script exists:', sendScriptPath);
  const stats = fs.statSync(sendScriptPath);
  console.log('   📦 File size:', stats.size, 'bytes');
  const isExecutable = (stats.mode & parseInt('111', 8)) !== 0;
  console.log('   🔧 Executable:', isExecutable ? '✅ Yes' : '❌ No');
} else {
  console.log('   ❌ Send script NOT found:', sendScriptPath);
}

// Check 5: Resend API Key
console.log('\n5️⃣ Checking Resend API Key...');
const apiKey = process.env.RESEND_API_KEY;
if (apiKey) {
  console.log('   ✅ RESEND_API_KEY environment variable is set');
  console.log('   🔑 Key starts with:', apiKey.substring(0, 3) + '...');
  console.log('   📏 Key length:', apiKey.length, 'characters');
} else {
  console.log('   ⚠️  RESEND_API_KEY environment variable NOT set');
  console.log('   💡 Set it with: export RESEND_API_KEY=your_key_here');
}

// Check 6: Node.js and dependencies
console.log('\n6️⃣ Checking Node.js Environment...');
try {
  const { execSync } = await import('child_process');
  const nodeVersion = execSync('node --version', { encoding: 'utf-8' }).trim();
  console.log('   ✅ Node.js version:', nodeVersion);
  
  // Check if resend package is available
  try {
    const resendPath = execSync('npm list resend 2>/dev/null | head -1', { encoding: 'utf-8' }).trim();
    if (resendPath.includes('resend@')) {
      console.log('   ✅ Resend package available');
    } else {
      console.log('   ⚠️  Resend package may not be installed');
    }
  } catch (e) {
    console.log('   ⚠️  Could not check Resend package');
  }
} catch (e) {
  console.log('   ❌ Error checking Node.js:', e.message);
}

// Check 7: Cursor MCP Configuration
console.log('\n7️⃣ Checking Cursor MCP Configuration...');
console.log('   ℹ️  Cursor MCP config is stored in Cursor settings');
console.log('   📍 Location: Cursor → Settings → Features → Model Context Protocol');
console.log('   💡 To check:');
console.log('      1. Open Cursor Settings (Cmd+Shift+P → "Cursor Settings")');
console.log('      2. Go to "MCP" section');
console.log('      3. Look for "resend" server');
console.log('      4. Verify path points to:', mcpPath);
console.log('      5. Verify API key is set in args or env');

// Summary
console.log('\n' + '='.repeat(60));
console.log('📋 Summary');
console.log('='.repeat(60));
console.log('\n✅ Ready to send if:');
console.log('   1. Resend API key is set (get from https://resend.com/api-keys)');
console.log('   2. Run: node send-research-email.mjs YOUR_API_KEY');
console.log('\n💡 Or configure Resend MCP in Cursor:');
console.log('   1. Get API key from https://resend.com/api-keys');
console.log('   2. Add to Cursor MCP settings (see CURSOR-SETUP.md)');
console.log('   3. Restart Cursor');
console.log('   4. Ask Cursor to send the email\n');

