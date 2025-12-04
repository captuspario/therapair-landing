# Token Validation Fix - Summary

## Problem
Research invitation tokens were showing as "invalid token" when users tried to access the survey.

## Root Cause
The token signature encoding was incorrect. The Node.js scripts were using:
```javascript
const signatureB64 = base64urlEncode(signature.toString('binary'));
```

This approach doesn't work correctly because:
1. `signature.digest()` returns a Buffer
2. `.toString('binary')` converts it to a binary string representation
3. Base64url encoding that binary string doesn't match PHP's `hash_hmac()` output

## Solution
Changed all token generation scripts to convert the Buffer directly to base64, then to base64url:
```javascript
const signatureB64 = signature.toString('base64')
  .replace(/\+/g, '-')
  .replace(/\//g, '_')
  .replace(/=/g, '');
```

This matches PHP's `base64url_encode(hash_hmac('sha256', $signedPortion, $secret, true))` output.

## Files Fixed
1. `research/scripts/send-invite-to-tino.mjs` - Fixed signature encoding
2. `research/scripts/create-therapist-invite.mjs` - Fixed signature encoding
3. `research/scripts/generate-batch-tokens.mjs` - Fixed signature encoding
4. `research/scripts/generate-test-token.mjs` - Fixed signature encoding

## Verification
- ✅ All existing tokens regenerated with correct encoding
- ✅ All tokens verified against `/api/research/session.php`
- ✅ New tokens generated with `send-invite-to-tino.mjs` validate correctly

## Scripts Added
1. `research/scripts/verify-all-tokens.mjs` - Verify all tokens in test files
2. `research/scripts/regenerate-all-tokens.mjs` - Regenerate all tokens with correct encoding

## Testing
To verify a token works:
```bash
curl "https://therapair.com.au/api/research/session.php?token=YOUR_TOKEN"
```

Should return:
```json
{
  "success": true,
  "data": {
    "therapist_id": "...",
    "therapist_name": "...",
    ...
  }
}
```

## Next Steps for New Participants
All new tokens generated with the updated scripts will work correctly. If you have existing participants with invalid tokens:

1. Run `node research/scripts/regenerate-all-tokens.mjs` to regenerate all tokens
2. Resend invitation emails with the new tokens
3. Run `node research/scripts/verify-all-tokens.mjs` to confirm all tokens validate

