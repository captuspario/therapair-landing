# Email Server Testing Guide

This guide explains how to test the complete email → token → survey → tracking pipeline to ensure:
1. Each person receives an email with their own unique token
2. The token is correctly linked to their name and email
3. Their responses are recorded in Notion
4. Their feedback from sandbox/landing page is tracked and linked

---

## 🎯 Quick Start

### Step 1: Generate Test Tokens

Generate tokens for multiple test therapists:

```bash
cd products/landing-page
npm run research:generate-tokens
```

This creates:
- `test-tokens.json` - All token data with URLs
- `test-email-template.txt` - Email template ready to send

### Step 2: Review Generated Tokens

Open `test-tokens.json` to see:
- Each therapist's token
- Personalized survey URL
- Sandbox URL with tracking
- Landing page URL with tracking

### Step 3: Send Test Emails

**Option A: Manual Email (Recommended for Testing)**

1. Open `test-email-template.txt`
2. Copy each therapist's email section
3. Send via your email client (Gmail, Outlook, etc.)
4. Use `contact@therapair.com.au` as sender

**Option B: Automated Email (Production)**

For production, integrate with your email service (Mailchimp, SendGrid, etc.) using the token data from `test-tokens.json`.

### Step 4: Run Complete Flow Test

Test the entire pipeline:

```bash
npm run research:test-flow
```

This validates:
- ✅ Token session lookup (token → therapist name/email)
- ✅ Survey submission recording
- ✅ Notion database writes
- ⚠️ Manual: Sandbox tracking (you'll need to visit URLs)
- ⚠️ Manual: Landing page tracking (you'll need to visit URLs)

---

## 📧 Email Template Structure

Each email should include:

```
Subject: Help us build a better therapist-matching system

Hi [First Name],

Thank you for your interest in the Therapair research study.

Your personalized survey link:
https://therapair.com.au/research/survey/index.html?token=[TOKEN]

Try the sandbox demo:
https://therapair.com.au/sandbox/sandbox-demo.html?utm_source=email&utm_medium=research&utm_campaign=therapist_research&therapist_id=[THERAPIST_ID]

Visit the landing page:
https://therapair.com.au/?utm_source=email&utm_medium=research&utm_campaign=therapist_research&therapist_id=[THERAPIST_ID]

This token is valid for 30 days and is linked to your email address.

Best regards,
Therapair Team
```

---

## 🔍 What Gets Tested

### 1. Token Linking ✅

**Test:** Each token correctly identifies the therapist

**Validation:**
- Token → Therapist name match
- Token → Email match
- Token → Therapist ID match

**How to verify:**
```bash
npm run research:test-flow
```

Look for:
```
✅ Token correctly linked to Sarah Mitchell
   Email: sarah.mitchell+test@therapair.com.au
   ID: VIC-TEST-001
```

### 2. Response Recording ✅

**Test:** Survey responses are saved with correct therapist association

**Validation:**
- Response appears in Notion database
- Therapist name/email matches token
- All survey fields populated correctly

**How to verify:**
1. Run `npm run research:test-flow`
2. Check Notion database for new entries
3. Verify "Therapist Name" and "Email" match the token

### 3. Sandbox Tracking ⚠️

**Test:** Sandbox interactions are tracked and linked to therapist

**Current Implementation:**
- Sandbox URL includes `therapist_id` parameter
- UTM parameters track source (`utm_source=email`, `utm_medium=research`)

**How to verify:**
1. Visit sandbox URL from test email
2. Interact with the sandbox
3. Check if feedback is recorded (depends on sandbox implementation)

**Future Enhancement:**
- Store `therapist_id` in sessionStorage
- Send feedback to Notion with therapist association
- Link sandbox feedback to survey responses

### 4. Landing Page Tracking ⚠️

**Test:** Landing page form submissions are tracked and linked

**Current Implementation:**
- Landing page URL includes `therapist_id` parameter
- UTM parameters track source

**How to verify:**
1. Visit landing page URL from test email
2. Submit the contact form
3. Check if submission includes therapist_id (requires form handler update)

**Future Enhancement:**
- Capture `therapist_id` from URL parameter
- Store in form submission metadata
- Link to Notion research database

---

## 🛠️ Manual Testing Checklist

### Email Delivery
- [ ] Test emails received by all test therapists
- [ ] Each email has unique token
- [ ] Survey link works and shows correct therapist name
- [ ] Sandbox link includes tracking parameters
- [ ] Landing page link includes tracking parameters

### Token Validation
- [ ] Each token links to correct therapist name
- [ ] Each token links to correct email
- [ ] Token session lookup returns correct data
- [ ] Tokens expire after 30 days (test expiration)

### Survey Submission
- [ ] Complete survey using test token
- [ ] Response appears in Notion database
- [ ] Therapist name/email matches token
- [ ] All survey fields saved correctly
- [ ] Consent timestamp recorded

### Cross-Platform Tracking
- [ ] Visit sandbox with therapist_id parameter
- [ ] Visit landing page with therapist_id parameter
- [ ] Verify tracking parameters are preserved
- [ ] Check if interactions can be linked (future enhancement)

---

## 📊 Test Results

After running `npm run research:test-flow`, results are saved to `test-results.json`:

```json
[
  {
    "therapist": "Sarah Mitchell",
    "email": "sarah.mitchell+test@therapair.com.au",
    "therapist_id": "VIC-TEST-001",
    "token_session": true,
    "survey_submission": true,
    "record_id": "2ae5c259-44da-8169-b594-d51c0f4026c0"
  }
]
```

---

## 🚀 Production Deployment

### Before Going Live:

1. **Generate Real Tokens**
   - Use real therapist data from your directory
   - Generate tokens for all ~200 therapists
   - Store tokens securely (not in git)

2. **Email Service Setup**
   - Configure Mailchimp/SendGrid with email template
   - Use personalized merge tags for tokens
   - Test email deliverability

3. **Tracking Implementation**
   - Ensure sandbox captures `therapist_id`
   - Ensure landing page form captures `therapist_id`
   - Link all interactions in Notion database

4. **Monitoring**
   - Track email open rates
   - Monitor survey completion rates
   - Verify Notion database updates
   - Check for failed token validations

---

## 🐛 Troubleshooting

### Token Not Working
- Check token hasn't expired (30 days)
- Verify `RESEARCH_TOKEN_SECRET` matches in config
- Check token format (should be JWT-like: `header.payload.signature`)

### Survey Not Saving
- Verify Notion database ID is correct
- Check Notion integration has database access
- Review server error logs
- Run `npm run research:smoke` to test API

### Tracking Not Working
- Verify URL parameters are preserved
- Check browser console for errors
- Ensure tracking code is implemented in sandbox/landing page
- Test with different browsers

---

## 📝 Next Steps

1. **Enhance Sandbox Tracking**
   - Implement sessionStorage for therapist_id
   - Send feedback to Notion API
   - Link to survey responses

2. **Enhance Landing Page Tracking**
   - Update form handler to capture therapist_id
   - Store in submission metadata
   - Link to research database

3. **Analytics Dashboard**
   - Track email → survey conversion
   - Monitor sandbox engagement
   - Measure landing page interactions
   - Link all touchpoints per therapist

---

## 📞 Support

If you encounter issues:
1. Check `test-results.json` for detailed error messages
2. Review server error logs
3. Verify Notion database permissions
4. Test with `npm run research:smoke` for API validation

