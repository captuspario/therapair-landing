# 🔍 Email Debug Guide - Form Submission Issues

## Current Issue
- Emails not coming through Resend
- No emails received

## ✅ What I've Fixed

1. **Improved Error Logging**: Added comprehensive error logging to see exactly what's happening
2. **Fixed Resend API Format**: Changed `to` from array to string (matches working example)
3. **Better Error Messages**: Now logs full API responses for debugging
4. **Diagnostic Scripts**: Created test scripts to check configuration

## 🧪 How to Debug

### Step 1: Run Debug Script on Server

SSH into your server and run:

```bash
ssh u549396201@45.87.81.159 -p 65002
cd domains/therapair.com.au/public_html
php debug-form-submission.php
```

This will:
- Check all configuration
- Test Resend API connection
- Show detailed error messages
- Verify cURL is working

### Step 2: Check Server Error Logs

Check PHP error logs on the server:

```bash
# Common log locations:
tail -f /var/log/php-errors.log
tail -f /var/log/apache2/error.log
tail -f /var/log/nginx/error.log

# Or check Hostinger error logs in control panel
```

Look for lines containing:
- "Resend email"
- "Resend API"
- Error messages from form submission

### Step 3: Test Resend API Directly

Run the test script on the server:

```bash
cd domains/therapair.com.au/public_html
php test-resend-email.php
```

This will send a test email and show the full API response.

### Step 4: Verify Resend API Key

1. Go to: https://resend.com/api-keys
2. Check if the API key `re_CVygh1m3_ADNfJd5t8me7GNo2ZERE8VtU` is still active
3. Check Resend dashboard: https://resend.com/emails
   - Look for any failed emails
   - Check error messages

### Step 5: Test Form Submission

1. Fill out the form at: https://therapair.com.au/#get-started
2. Submit it
3. Immediately check server error logs
4. Check Resend dashboard for email attempts

## 🔧 Common Issues & Fixes

### Issue 1: API Key Invalid
**Symptoms**: HTTP 401 or 403 errors
**Fix**: Verify API key is correct in config.php on server

### Issue 2: cURL Not Available
**Symptoms**: cURL errors in logs
**Fix**: Install/enable cURL on server

### Issue 3: Domain Not Verified
**Symptoms**: Emails rejected by Resend
**Fix**: Use `onboarding@resend.dev` as sender (already done)

### Issue 4: API Rate Limit
**Symptoms**: HTTP 429 errors
**Fix**: Check Resend dashboard for rate limits

### Issue 5: Email Going to Spam
**Symptoms**: Emails sent but not received
**Fix**: 
- Check spam folder
- Check Resend dashboard delivery status
- Verify sender domain

## 📋 Quick Checklist

- [ ] RESEND_API_KEY is set in config.php on server
- [ ] USE_RESEND is set to `true` in config.php
- [ ] cURL is available on server
- [ ] Resend API key is valid and active
- [ ] No rate limits on Resend account
- [ ] Check server error logs for detailed errors
- [ ] Check Resend dashboard for email status

## 🚀 Next Steps

1. **Run debug script** on server to see exact errors
2. **Check error logs** for detailed error messages
3. **Test Resend API** directly using test script
4. **Check Resend dashboard** for email delivery status
5. **Share error messages** from logs for further debugging

## 📞 If Still Not Working

Share the output from:
1. `php debug-form-submission.php` on server
2. Error log entries from form submission
3. Resend dashboard error messages

This will help identify the exact issue.



