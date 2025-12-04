#!/usr/bin/env node
/**
 * Validate API credentials and database connections
 * Usage: node validate-credentials.mjs
 * 
 * This script checks:
 * - Notion API token is valid
 * - Resend API key is valid
 * - Database IDs are accessible
 * - Environment variables are set correctly
 */

import dotenv from 'dotenv';
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { Resend } from 'resend';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Load .env file
const envPath = path.join(__dirname, '..', '.env');
if (fs.existsSync(envPath)) {
  dotenv.config({ path: envPath });
} else {
  console.error('❌ Error: .env file not found at:', envPath);
  process.exit(1);
}

// Get credentials
const NOTION_TOKEN = process.env.NOTION_TOKEN;
const RESEND_API_KEY = process.env.RESEND_API_KEY;
const DIRECTORY_DB_ID = process.env.DIRECTORY_DB_ID;
const RESEARCH_DB_ID = process.env.RESEARCH_DB_ID;
const EOI_DB_ID = process.env.EOI_DB_ID;
const FEEDBACK_DB_ID = process.env.FEEDBACK_DB_ID;
const RESEARCH_TOKEN_SECRET = process.env.RESEARCH_TOKEN_SECRET;

console.log('\n🔍 Validating Therapair Research Credentials...\n');
console.log('='.repeat(60));

let allValid = true;

// Check environment variables exist
console.log('\n📋 Checking Environment Variables...');
const requiredVars = {
  'NOTION_TOKEN': NOTION_TOKEN,
  'RESEND_API_KEY': RESEND_API_KEY,
  'DIRECTORY_DB_ID': DIRECTORY_DB_ID,
  'RESEARCH_DB_ID': RESEARCH_DB_ID,
  'EOI_DB_ID': EOI_DB_ID,
  'FEEDBACK_DB_ID': FEEDBACK_DB_ID,
  'RESEARCH_TOKEN_SECRET': RESEARCH_TOKEN_SECRET,
};

for (const [name, value] of Object.entries(requiredVars)) {
  if (value) {
    const masked = value.length > 10 ? `${value.substring(0, 6)}...${value.substring(value.length - 4)}` : '***';
    console.log(`  ✅ ${name}: ${masked}`);
  } else {
    console.log(`  ❌ ${name}: MISSING`);
    allValid = false;
  }
}

// Validate Notion API token
console.log('\n🔐 Validating Notion API Token...');
if (NOTION_TOKEN) {
  try {
    const response = await fetch('https://api.notion.com/v1/users/me', {
      headers: {
        'Authorization': `Bearer ${NOTION_TOKEN}`,
        'Notion-Version': '2022-06-28',
      },
    });

    if (response.ok) {
      const data = await response.json();
      console.log(`  ✅ Notion API token is valid`);
      console.log(`     User: ${data.name || data.id}`);
    } else {
      const error = await response.json();
      console.log(`  ❌ Notion API token is invalid: ${error.message || response.status}`);
      allValid = false;
    }
  } catch (error) {
    console.log(`  ❌ Error validating Notion token: ${error.message}`);
    allValid = false;
  }
} else {
  console.log('  ⚠️  NOTION_TOKEN not set, skipping validation');
  allValid = false;
}

// Validate database access
console.log('\n📊 Validating Database Access...');
const databases = [
  { name: 'VIC Therapist DB', id: DIRECTORY_DB_ID },
  { name: 'Research DB', id: RESEARCH_DB_ID },
  { name: 'EOI DB', id: EOI_DB_ID },
  { name: 'Feedback DB', id: FEEDBACK_DB_ID },
];

for (const db of databases) {
  if (!db.id) {
    console.log(`  ⚠️  ${db.name}: ID not set`);
    continue;
  }

  try {
    const response = await fetch(`https://api.notion.com/v1/databases/${db.id}`, {
      headers: {
        'Authorization': `Bearer ${NOTION_TOKEN}`,
        'Notion-Version': '2022-06-28',
      },
    });

    if (response.ok) {
      const data = await response.json();
      console.log(`  ✅ ${db.name}: Accessible (${data.title?.[0]?.plain_text || 'Untitled'})`);
    } else {
      const error = await response.json();
      console.log(`  ❌ ${db.name}: ${error.message || 'Access denied'}`);
      allValid = false;
    }
  } catch (error) {
    console.log(`  ❌ ${db.name}: Error - ${error.message}`);
    allValid = false;
  }
}

// Validate Resend API key
console.log('\n📧 Validating Resend API Key...');
if (RESEND_API_KEY) {
  try {
    const resend = new Resend(RESEND_API_KEY);
    // Try to get API keys list (lightweight check)
    const response = await fetch('https://api.resend.com/api-keys', {
      headers: {
        'Authorization': `Bearer ${RESEND_API_KEY}`,
      },
    });

    if (response.ok || response.status === 200) {
      console.log(`  ✅ Resend API key is valid`);
    } else if (response.status === 401) {
      console.log(`  ❌ Resend API key is invalid (401 Unauthorized)`);
      allValid = false;
    } else {
      // Some endpoints might return 403, but that's okay - key is valid
      console.log(`  ✅ Resend API key appears valid (status: ${response.status})`);
    }
  } catch (error) {
    console.log(`  ⚠️  Could not validate Resend key: ${error.message}`);
    // Don't fail on this - network issues might cause false negatives
  }
} else {
  console.log('  ⚠️  RESEND_API_KEY not set, skipping validation');
  allValid = false;
}

// Summary
console.log('\n' + '='.repeat(60));
if (allValid) {
  console.log('✅ All credentials validated successfully!');
  console.log('   You can now use the research scripts.\n');
  process.exit(0);
} else {
  console.log('❌ Some credentials failed validation.');
  console.log('   Please check the errors above and update your .env file.\n');
  process.exit(1);
}


