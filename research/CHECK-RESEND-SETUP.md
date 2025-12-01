# 🔍 Resend MCP & Email Setup Check

## ✅ What's Working

Based on the diagnostic:
- ✅ Resend MCP server is built and ready
- ✅ Email HTML template exists with token
- ✅ Token data is generated
- ✅ Send script is ready
- ✅ Node.js is installed (v22.19.0)

## ❌ What's Missing

- ⚠️ **Resend API Key** - Not configured
- ⚠️ **Resend MCP in Cursor** - May not be configured

---

## 🔧 How to Check Resend MCP in Cursor

### Step 1: Open Cursor Settings

1. Press `Cmd+Shift+P` (macOS) or `Ctrl+Shift+P` (Windows)
2. Type: **"Cursor Settings"**
3. Select it

### Step 2: Check MCP Configuration

1. Navigate to: **Features** → **Model Context Protocol**
2. Look for a server named **"resend"**
3. If it exists, check:
   - ✅ Path points to: `/Users/tino/Projects/Therapair/email-resend-mcp/build/index.js`
   - ✅ API key is set in `args` or `env`

### Step 3: If MCP is NOT Configured

You need to add it. Here's the configuration:

```json
{
  "mcpServers": {
    "resend": {
      "command": "node",
      "args": [
        "/Users/tino/Projects/Therapair/email-resend-mcp/build/index.js",
        "--key=YOUR_RESEND_API_KEY_HERE"
      ]
    }
  }
}
```

**Replace `YOUR_RESEND_API_KEY_HERE` with your actual Resend API key!**

---

## 🚀 Quick Fix: Send Email Now

Since the MCP might not be configured yet, use the script directly:

### Option 1: Get API Key & Run Script

1. **Get Resend API Key:**
   - Go to: https://resend.com/api-keys
   - Sign up/login (free: 3,000 emails/month)
   - Create API key
   - Copy it (starts with `re_`)

2. **Send Email:**
   ```bash
   cd /Users/tino/Projects/Therapair/products/landing-page/research/scripts
   node send-research-email.mjs YOUR_API_KEY_HERE
   ```

### Option 2: Use Environment Variable

```bash
export RESEND_API_KEY=your_key_here
cd /Users/tino/Projects/Therapair/products/landing-page/research/scripts
node send-research-email.mjs
```

---

## 📧 Test Email Details

- **To:** tinoman@me.com
- **Subject:** Help us build a better therapist-matching system
- **From:** onboarding@resend.dev (or contact@therapair.com.au if domain verified)
- **Includes:** Personalized survey link with token

---

## 🔍 Verify Email Was Sent

After running the script, you should see:
```
✅ Email sent successfully!
📬 Message ID: [some-id]
```

Then:
1. Check your inbox (tinoman@me.com)
2. Check spam folder
3. Email should arrive within seconds

---

## 🐛 Troubleshooting

### "API key invalid"
- Make sure you copied the full key
- Check for extra spaces
- Key should start with `re_`

### "Domain not verified"
- Use `onboarding@resend.dev` as sender (works immediately)
- Or verify your domain in Resend dashboard

### Email not received
- Check spam folder
- Wait 1-2 minutes
- Check Resend dashboard: https://resend.com/emails

### MCP not working in Cursor
- Make sure you restarted Cursor after adding MCP config
- Check the path to `build/index.js` is correct
- Verify Node.js is in your PATH

---

## 📝 Next Steps

1. **Get Resend API Key** (if you don't have one)
2. **Send test email** using the script
3. **Configure MCP in Cursor** (optional, for future use)
4. **Test the survey link** once email arrives

---

**Ready?** Get your API key and run the send script! 🚀

