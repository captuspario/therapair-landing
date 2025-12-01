#!/usr/bin/env node
/**
 * Comprehensive test flow for email → token → survey → tracking
 * Validates:
 * 1. Token generation and linking to therapist name/email
 * 2. Survey response recording in Notion
 * 3. Sandbox/landing page tracking
 * 4. Cross-platform data linking
 */

import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const baseUrl = process.env.RESEARCH_TEST_BASE_URL || 'https://therapair.com.au';
const tokensFile = path.join(__dirname, '..', 'test-tokens.json');

if (!fs.existsSync(tokensFile)) {
  console.error('❌ test-tokens.json not found. Run generate-batch-tokens.mjs first.');
  process.exit(1);
}

const tokens = JSON.parse(fs.readFileSync(tokensFile, 'utf-8'));

async function testTokenSession(token, therapist) {
  console.log(`\n🔍 Testing session lookup for ${therapist.therapist_name}...`);
  const url = `${baseUrl}/api/research/session.php?token=${encodeURIComponent(token)}`;
  
  try {
    const response = await fetch(url, { headers: { Accept: 'application/json' } });
    const data = await response.json();
    
    if (!response.ok) {
      console.error(`  ❌ Session lookup failed: ${data.error || response.status}`);
      return false;
    }
    
    // Validate token links to correct therapist
    const matches = 
      data.data?.therapist_name === therapist.therapist_name &&
      data.data?.email === therapist.email &&
      data.data?.therapist_id === therapist.therapist_id;
    
    if (!matches) {
      console.error(`  ❌ Token data mismatch!`);
      console.error(`     Expected: ${therapist.therapist_name}, ${therapist.email}`);
      console.error(`     Got: ${data.data?.therapist_name}, ${data.data?.email}`);
      return false;
    }
    
    console.log(`  ✅ Token correctly linked to ${therapist.therapist_name}`);
    console.log(`     Email: ${data.data?.email}`);
    console.log(`     ID: ${data.data?.therapist_id}`);
    return true;
  } catch (error) {
    console.error(`  ❌ Network error: ${error.message}`);
    return false;
  }
}

async function testSurveySubmission(token, therapist) {
  console.log(`\n📝 Testing survey submission for ${therapist.therapist_name}...`);
  
  const payload = {
    token,
    session_id: `test-flow-${Date.now()}-${therapist.therapist_id}`,
    consent: {
      accepted: true,
      version: '2025-11-13',
      timestamp: new Date().toISOString(),
    },
    survey: {
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
      biggest_gap: `Test submission from ${therapist.therapist_name} - validating token linking and response recording.`,
      screens_clients: 'Yes',
      open_to_sharing: 'Yes',
      questions_matter: ['Values alignment', 'Client goals clarity'],
      questions_matter_other: '',
      personality_test: 'Maybe',
      too_personal: ['Family history'],
      too_personal_other: '',
      profile_detail_level: '7',
      onboarding_time: '15-20 minutes',
      free_listing_interest: 'Yes',
      profile_intent: 'Yes',
      future_contact: 'Yes',
      email: therapist.email,
      comments: `Automated test flow for ${therapist.therapist_name} (${therapist.therapist_id})`,
    },
    therapist: {
      therapist_id: therapist.therapist_id,
      therapist_name: therapist.therapist_name,
      first_name: therapist.first_name,
      practice_name: therapist.practice_name,
      email: therapist.email,
      directory_page_id: therapist.directory_page_id,
      therapist_research_id: therapist.therapist_research_id,
    },
    metadata: {
      utm: {
        source: 'test-flow',
        medium: 'automated-testing',
        campaign: 'therapist-research-validation',
      },
      landing_path: '/research/survey',
      referrer: 'https://therapair.com.au/test',
      sandbox_visit: false,
      therapist_id: therapist.therapist_id,
      test_flow: true,
    },
  };
  
  try {
    const response = await fetch(`${baseUrl}/api/research/response.php`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
      },
      body: JSON.stringify(payload),
    });
    
    const data = await response.json();
    
    if (!response.ok) {
      console.error(`  ❌ Submission failed: ${data.error || response.status}`);
      if (data.debug) console.error(`     Debug: ${data.debug}`);
      return false;
    }
    
    console.log(`  ✅ Survey response recorded successfully`);
    console.log(`     Notion Record ID: ${data.record_id || '(not returned)'}`);
    console.log(`     Therapist: ${therapist.therapist_name}`);
    console.log(`     Email: ${therapist.email}`);
    return { success: true, record_id: data.record_id };
  } catch (error) {
    console.error(`  ❌ Network error: ${error.message}`);
    return false;
  }
}

async function testSandboxTracking(therapist) {
  console.log(`\n🎯 Testing sandbox tracking for ${therapist.therapist_name}...`);
  console.log(`   Sandbox URL: ${therapist.sandbox_url}`);
  console.log(`   ⚠️  Manual step: Visit the sandbox URL and interact with it`);
  console.log(`   The URL includes therapist_id=${therapist.therapist_id} for tracking`);
  return true;
}

async function testLandingPageTracking(therapist) {
  console.log(`\n🏠 Testing landing page tracking for ${therapist.therapist_name}...`);
  console.log(`   Landing URL: ${therapist.landing_url}`);
  console.log(`   ⚠️  Manual step: Visit the landing page and submit the form`);
  console.log(`   The URL includes therapist_id=${therapist.therapist_id} for tracking`);
  return true;
}

async function runCompleteTest() {
  console.log('\n🧪 Therapair Research - Complete Flow Test\n');
  console.log('='.repeat(60));
  
  const results = [];
  
  for (const therapist of tokens) {
    console.log(`\n${'='.repeat(60)}`);
    console.log(`Testing: ${therapist.therapist_name}`);
    console.log('='.repeat(60));
    
    const result = {
      therapist: therapist.therapist_name,
      email: therapist.email,
      therapist_id: therapist.therapist_id,
      token_session: false,
      survey_submission: false,
      record_id: null,
    };
    
    // Test 1: Token session lookup
    result.token_session = await testTokenSession(therapist.token, therapist);
    
    // Test 2: Survey submission
    if (result.token_session) {
      const submission = await testSurveySubmission(therapist.token, therapist);
      if (submission) {
        result.survey_submission = submission.success;
        result.record_id = submission.record_id;
      }
    }
    
    // Test 3 & 4: Manual tracking tests (just print URLs)
    await testSandboxTracking(therapist);
    await testLandingPageTracking(therapist);
    
    results.push(result);
    
    // Small delay between tests
    await new Promise((resolve) => setTimeout(resolve, 1000));
  }
  
  // Summary
  console.log(`\n${'='.repeat(60)}`);
  console.log('📊 Test Summary');
  console.log('='.repeat(60));
  
  results.forEach((r) => {
    console.log(`\n${r.therapist} (${r.email})`);
    console.log(`  Token Session: ${r.token_session ? '✅' : '❌'}`);
    console.log(`  Survey Submission: ${r.survey_submission ? '✅' : '❌'}`);
    if (r.record_id) {
      console.log(`  Notion Record: ${r.record_id}`);
    }
  });
  
  const allPassed = results.every((r) => r.token_session && r.survey_submission);
  
  console.log(`\n${'='.repeat(60)}`);
  if (allPassed) {
    console.log('✅ All automated tests passed!');
  } else {
    console.log('⚠️  Some tests failed. Review the output above.');
  }
  console.log('='.repeat(60));
  
  console.log('\n📋 Next Steps:');
  console.log('1. Check Notion database for the recorded responses');
  console.log('2. Manually test sandbox URLs to verify tracking');
  console.log('3. Manually test landing page URLs to verify form tracking');
  console.log('4. Verify emails were received with correct tokens\n');
  
  // Save results
  const resultsPath = path.join(__dirname, '..', 'test-results.json');
  fs.writeFileSync(resultsPath, JSON.stringify(results, null, 2));
  console.log(`📄 Detailed results saved to: ${resultsPath}\n`);
}

runCompleteTest().catch((error) => {
  console.error('\n❌ Test flow failed:', error);
  process.exit(1);
});

