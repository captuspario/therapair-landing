# 🔐 Resend API Key Setup - Complete

## ✅ Status

- ✅ **API Key Saved Securely**
  - Location: `products/landing-page/research/.env`
  - Protected by `.gitignore` (will NOT be committed to Git)
  - Key: `re_CVygh1m3_ADNfJd5t8me7GNo2ZERE8VtU`

- ✅ **Email Sent Successfully**
  - Message ID: `8ca0d466-6f64-499a-8788-ddd26995685e`
  - Sent to: `tinoman@me.com`
  - Subject: "Help us build a better therapist-matching system"

---

## 📁 File Locations

### Secure Storage
- **`.env` file**: `products/landing-page/research/.env`
  - Contains: `RESEND_API_KEY=re_CVygh1m3_ADNfJd5t8me7GNo2ZERE8VtU`
  - ✅ Protected by `.gitignore`
  - ✅ Never committed to Git

### Scripts
- **Send script**: `products/landing-page/research/scripts/send-research-email.mjs`
  - Automatically loads API key from `.env` file
  - Can also accept API key as command-line argument
  - Can also use `RESEND_API_KEY` environment variable

---

## 🚀 How to Use

### Send Email (Automatic - Uses .env)
```bash
cd products/landing-page/research/scripts
node send-research-email.mjs
```

### Send Email (Manual API Key)
```bash
node send-research-email.mjs YOUR_API_KEY
```

### Send Email (Environment Variable)
```bash
export RESEND_API_KEY=your_key_here
node send-research-email.mjs
```

---

## 🔒 Security

- ✅ API key is stored in `.env` file (not in code)
- ✅ `.env` file is in `.gitignore` (won't be committed)
- ✅ Script loads from `.env` automatically
- ✅ Can override with command-line argument if needed

---

## 📧 Email Details

- **To:** tinoman@me.com
- **From:** onboarding@resend.dev (or contact@therapair.com.au if domain verified)
- **Subject:** Help us build a better therapist-matching system
- **Includes:** 
  - Personalized survey link with token
  - Sandbox demo link
  - Documentation link
  - UTM tracking parameters

---

## ✅ Next Steps

1. **Check your inbox** (tinoman@me.com)
   - Check spam folder if not in inbox
   - Email should arrive within seconds

2. **Test the survey link**
   - Click "Join the Research Survey" button
   - Verify token is pre-filled
   - Complete the survey

3. **Verify response saved**
   - Check Notion database: https://www.notion.so/2995c25944da80a5b5d1f0eb9db74a36
   - Look for entry with email: tinoman@me.com

---

## 🔄 For Future Emails

The API key is now saved securely. To send emails to other therapists:

1. Generate token for them (using `create-therapist-invite.mjs`)
2. Update email HTML with their personalized link
3. Run: `node send-research-email.mjs`

Or configure Resend MCP in Cursor for even easier sending!

---

**Setup Complete!** ✅

