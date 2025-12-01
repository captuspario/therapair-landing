#!/usr/bin/env node
import crypto from 'node:crypto';

const secret = 'I1WPkhdJs6Fw2XAa+BRH2hCiezYuP8FjT1xoG5tp/koajYB7tlleXc3lzvwVUqNv';

function base64urlEncode(str) {
  return Buffer.from(str)
    .toString('base64')
    .replace(/\+/g, '-')
    .replace(/\//g, '_')
    .replace(/=/g, '');
}

function base64urlDecode(str) {
  str = str.replace(/-/g, '+').replace(/_/g, '/');
  while (str.length % 4) {
    str += '=';
  }
  return Buffer.from(str, 'base64').toString();
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

const testPayload = {
  therapist_id: 'VIC-TEST-001',
  therapist_name: 'Test Therapist',
  first_name: 'Test',
  practice_name: 'Test Practice',
  email: 'test@therapair.com.au',
  directory_page_id: null,
  therapist_research_id: 'test-001',
  exp: Math.floor(Date.now() / 1000) + (30 * 24 * 60 * 60), // 30 days
};

const token = signToken(testPayload);

console.log('\n✅ Test token generated successfully!\n');
console.log('Token:', token);
console.log('\nTest URL:');
console.log(`https://therapair.com.au/research/survey/index.html?token=${encodeURIComponent(token)}\n`);
console.log('This token expires in 30 days.\n');
