# 🚨 IMPORTANT: Server Configuration Required

## Fix for HTTP 500 Error - Resend Email Setup

The form submission has been updated to use **Resend** for reliable email delivery, but you need to add the Resend API key to the server's `config.php` file.

---

## ✅ What Was Fixed

1. **Fixed HTTP 500 Error**: Added proper error handling with fallback values
2. **Switched to Resend**: Replaced unreliable PHP `mail()` with Resend API
3. **Added Error Logging**: Comprehensive logging for debugging
4. **Graceful Fallbacks**: If Resend fails, falls back to PHP mail()

---

## 🔧 Required: Update Server config.php

**SSH into your Hostinger server** and add these lines to `config.php`:

```php
// Resend API configuration (for email delivery)
define('RESEND_API_KEY', 're_CVygh1m3_ADNfJd5t8me7GNo2ZERE8VtU'); // Get from https://resend.com/api-keys
define('USE_RESEND', true); // Set to false to use PHP mail() as fallback
```

### Quick SSH Command:

```bash
ssh u549396201@45.87.81.159 -p 65002
cd domains/therapair.com.au/public_html
nano config.php
# Add the RESEND_API_KEY and USE_RESEND lines above
# Save and exit (Ctrl+X, then Y, then Enter)
```

---

## 📧 Resend API Key

**Current API Key:** `re_CVygh1m3_ADNfJd5t8me7GNo2ZERE8VtU`

**To get a new key:**
1. Go to: https://resend.com/api-keys
2. Sign up/login (free: 3,000 emails/month)
3. Create API key
4. Copy and add to config.php

---

## ✅ Verify It's Working

After updating config.php:

1. **Test the form**: https://therapair.com.au/#get-started
2. **Check email inbox**: You should receive:
   - Admin notification email
   - User confirmation email
3. **Check server logs**: `/var/log/php-errors.log` or Hostinger error logs

---

## 🔍 Troubleshooting

### Still getting 500 error?
- Check PHP error logs on server
- Verify `RESEND_API_KEY` is defined in config.php
- Check that cURL is enabled (required for Resend API)

### Emails not arriving?
- Check spam folder
- Verify Resend API key is valid
- Check Resend dashboard: https://resend.com/emails
- Look for error messages in server logs

### Want to disable Resend?
Set in config.php:
```php
define('USE_RESEND', false); // Falls back to PHP mail()
```

---

## 📝 Current Email Setup

- **Sender**: `onboarding@resend.dev` (Resend's verified domain - works immediately)
- **From Name**: `Therapair Team`
- **Admin Email**: `contact@therapair.com.au`
- **Reply-To**: User's email (for admin) or admin email (for user)

---

**Status**: Code deployed, awaiting server config update
**Next Step**: Add RESEND_API_KEY to server's config.php



