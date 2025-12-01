# 🚀 Send Test Email Now

## Quick Steps

### 1. Get Resend API Key (2 minutes)

1. Go to: **https://resend.com/api-keys**
2. Sign up/login (free account - 3,000 emails/month)
3. Click **"Create API Key"**
4. Name it: "Therapair Test"
5. **Copy the key** (starts with `re_`)

### 2. Send the Email

**Option A: Quick Command**
```bash
cd /Users/tino/Projects/Therapair/products/landing-page/research/scripts
node send-research-email.mjs YOUR_API_KEY_HERE
```

**Option B: With Environment Variable**
```bash
export RESEND_API_KEY=your_key_here
cd /Users/tino/Projects/Therapair/products/landing-page/research/scripts
node send-research-email.mjs
```

### 3. Check Your Email

- Check **inbox** (tinoman@me.com)
- Check **spam/junk folder**
- Email should arrive within seconds

---

## Alternative: Use Resend Dashboard

If you prefer the web interface:

1. Go to: **https://resend.com/emails**
2. Click **"Send Email"**
3. Copy HTML from: `products/landing-page/research/tino-research-email.html`
4. Paste into HTML editor
5. Set:
   - **To:** tinoman@me.com
   - **From:** onboarding@resend.dev (or contact@therapair.com.au if verified)
   - **Subject:** Help us build a better therapist-matching system
6. Click **Send**

---

## Troubleshooting

**"API key invalid"**
- Make sure you copied the full key (starts with `re_`)
- Check for extra spaces

**"Domain not verified"**
- Use `onboarding@resend.dev` as sender (works immediately)
- Or verify your domain in Resend dashboard

**Email not received**
- Check spam folder
- Wait 1-2 minutes
- Check Resend dashboard for delivery status

---

**Ready?** Just get your API key and run the command above! 🚀

