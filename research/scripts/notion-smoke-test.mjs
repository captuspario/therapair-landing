#!/usr/bin/env node
import fs from 'node:fs';
import path from 'node:path';
import process from 'node:process';
import { fileURLToPath } from 'node:url';
import dotenv from 'dotenv';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const projectRoot = path.resolve(__dirname, '..', '..');

const envCandidates = [
  path.join(projectRoot, '.env.research.local'),
  path.join(projectRoot, '.env.local'),
  path.join(projectRoot, '.env'),
];
for (const envPath of envCandidates) {
  if (fs.existsSync(envPath)) {
    dotenv.config({ path: envPath, override: false });
  }
}

dotenv.config({ override: false });

const args = process.argv.slice(2);
const getArg = (flag) => {
  const index = args.indexOf(flag);
  if (index === -1) return null;
  return args[index + 1] || null;
};

const baseUrl = (getArg('--base') || process.env.RESEARCH_TEST_BASE_URL || 'https://therapair.com.au').replace(/\/$/, '');
const token = getArg('--token') || process.env.RESEARCH_TEST_TOKEN || 'preview';
const emailOverride = getArg('--email') || process.env.RESEARCH_TEST_EMAIL || '';
const dryRun = args.includes('--dry-run');

const sessionUrl = `${baseUrl}/api/research/session.php?token=${encodeURIComponent(token)}`;

function logSection(title) {
  console.log(`\n=== ${title} ===`);
}

async function main() {
  logSection('Session lookup');
  const sessionResponse = await fetch(sessionUrl, { headers: { Accept: 'application/json' } });
  const sessionJson = await sessionResponse.json().catch(() => ({}));
  console.log('Status:', sessionResponse.status);
  console.log('Response:', JSON.stringify(sessionJson, null, 2));

  if (!sessionResponse.ok) {
    console.error('\nSession lookup failed. Aborting smoke test.');
    process.exit(1);
  }

  const consentVersion = sessionJson?.consent?.version || '2025-11-13';
  const therapistData = sessionJson?.data || {};
  const submissionEmail = emailOverride || therapistData.email || 'smoke-test+preview@therapair.com.au';

  const survey = {
    profession: 'Psychologist',
    profession_other: '',
    years_practice: '5-9 years',
    client_types: ['Adults', 'Couples'],
    client_types_other: '',
    modalities: ['CBT', 'ACT'],
    modalities_other: '',
    clients_find_you: ['Professional referrals', 'Directories (e.g. Psychology Today)'],
    clients_find_you_other: '',
    match_factors: ['Values alignment', 'Therapist lived experience'],
    match_factors_other: '',
    biggest_gap: 'Smoke-test submission to verify Notion integration pipeline.',
    screens_clients: 'Yes',
    open_to_sharing: 'Yes',
    questions_matter: ['Values alignment', 'Client goals clarity'],
    questions_matter_other: '',
    personality_test: 'Maybe',
    too_personal: ['Family history'],
    too_personal_other: '',
    profile_detail_level: '80',
    onboarding_time: '15-20 minutes',
    free_listing_interest: 'Yes',
    profile_intent: 'Yes',
    future_contact: 'Yes',
    email: submissionEmail,
    comments: 'Automated smoke test run via notion-smoke-test.mjs',
  };

  const payload = {
    token,
    session_id: `smoke-${Date.now()}`,
    consent: {
      accepted: true,
      version: consentVersion,
      timestamp: new Date().toISOString(),
    },
    survey,
    therapist: therapistData,
    metadata: {
      utm: {
        source: 'research-smoke-test',
        medium: 'qa',
        campaign: 'therapair-research',
      },
      landing_path: '/research/survey',
      referrer: 'https://therapair.com.au/qa',
      sandbox_visit: false,
      notes: 'Automated QA smoke test',
    },
  };

  logSection('Submission payload');
  console.log(JSON.stringify(payload, null, 2));

  if (dryRun) {
    console.log('\nDry run enabled. Skipping submission.');
    process.exit(0);
  }

  logSection('Submitting to response endpoint');
  const response = await fetch(`${baseUrl}/api/research/response.php`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      Accept: 'application/json',
    },
    body: JSON.stringify(payload),
  });

  const json = await response.json().catch(() => ({}));
  console.log('Status:', response.status);
  console.log('Response:', JSON.stringify(json, null, 2));

  if (!response.ok) {
    console.error('\nSmoke test failed. See response above.');
    process.exit(1);
  }

  console.log('\n✅ Notion smoke test succeeded. Check the research database for record ID:', json.record_id || '(not returned)');
}

main().catch((error) => {
  console.error('\nUnexpected smoke-test error:', error);
  process.exit(1);
});
