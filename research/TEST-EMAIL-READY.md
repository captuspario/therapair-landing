# ✅ Test Email Ready to Send

## What's Been Prepared

1. **✅ Token Generated** - Secure JWT token for `tinoman@me.com`
2. **✅ Email HTML Created** - Professional HTML email matching the campaign template
3. **✅ Tracking Enabled** - All links include UTM parameters for tracking
4. **✅ Personalized Survey Link** - Survey link includes your unique token

## Your Personalized Survey Link

```
https://therapair.com.au/research/survey/index.html?token=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ0aGVyYXBpc3RfaWQiOiJWSUMtVElOTy0wMDEiLCJ0aGVyYXBpc3RfbmFtZSI6IlRpbm8gTWFuIiwiZmlyc3RfbmFtZSI6IlRpbm8iLCJwcmFjdGljZV9uYW1lIjoiVGhlcmFwYWlyIFJlc2VhcmNoIiwiZW1haWwiOiJ0aW5vbWFuQG1lLmNvbSIsImRpcmVjdG9yeV9wYWdlX2lkIjpudWxsLCJ0aGVyYXBpc3RfcmVzZWFyY2hfaWQiOiJ0aW5vLTE3NjM0MzY3MjMzMjYiLCJleHAiOjE3NjYwMjg3MjN9.NMO4BWfCgCXClMO-wq41GlHDnMK-woMTI3E7dnYmw5tvNsOFwr09wrvCuT8y&utm_source=email&utm_medium=research&utm_campaign=therapist_research&utm_content=survey
```

## How to Send the Email

### Option 1: Using Resend MCP (Recommended)

If you've configured the Resend MCP server in Cursor:

1. **Make sure Resend MCP is configured** (see `email-resend-mcp/CURSOR-SETUP.md`)
2. **Ask Cursor to send the email:**
   ```
   Send an email to tinoman@me.com with subject "Help us build a better therapist-matching system", from contact@therapair.com.au (or onboarding@resend.dev if domain not verified), with HTML body from the file products/landing-page/research/tino-research-email.html
   ```

### Option 2: Using the Script (Quick)

1. **Get your Resend API key:**
   - Go to https://resend.com/api-keys
   - Create a new API key
   - Copy it

2. **Run the script:**
   ```bash
   cd products/landing-page/research/scripts
   node send-research-email.mjs YOUR_RESEND_API_KEY
   ```

   Or set environment variable:
   ```bash
   export RESEND_API_KEY=your_key_here
   node send-research-email.mjs
   ```

### Option 3: Manual Send via Resend Dashboard

1. Go to https://resend.com/emails
2. Click "Send Email"
3. Copy the HTML from `tino-research-email.html`
4. Paste into the HTML editor
5. Set:
   - **To:** tinoman@me.com
   - **From:** contact@therapair.com.au (or onboarding@resend.dev)
   - **Subject:** Help us build a better therapist-matching system
6. Send

## What to Test

Once you receive the email:

1. **✅ Click the survey link** - Should open with your token pre-filled
2. **✅ Complete the survey** - Fill out all questions
3. **✅ Submit the survey** - Verify submission works
4. **✅ Check Notion database** - Your response should appear in:
   - Research Responses DB: https://www.notion.so/2995c25944da80a5b5d1f0eb9db74a36
   - Look for entry with email: tinoman@me.com
5. **✅ Verify tracking** - Check that UTM parameters are captured

## Files Created

- `tino-research-email.html` - HTML email template
- `tino-token-data.json` - Token and URL data
- `tino-invitation-email.txt` - Plain text version
- `scripts/send-research-email.mjs` - Script to send via Resend API

## Email Details

- **To:** tinoman@me.com
- **Subject:** Help us build a better therapist-matching system
- **From:** contact@therapair.com.au (or onboarding@resend.dev if domain not verified)
- **Reply-To:** contact@therapair.com.au
- **Tracking:** All links include UTM parameters
- **Token:** Valid for 30 days

## Next Steps After Testing

Once you've confirmed everything works:

1. ✅ Email delivery works
2. ✅ Survey link works with token
3. ✅ Responses save to Notion
4. ✅ Tracking parameters work

Then you're ready to:
- Generate tokens for all 200+ therapists
- Send batch emails (via Resend API or MCP)
- Monitor responses in Notion

---

**Ready to send!** Choose one of the options above to send your test email.

