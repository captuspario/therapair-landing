# 📧 How Emails Are Being Sent

## Current Email Sending Method

### **Primary: Resend API** (via PHP cURL)

**How it works:**
1. PHP makes an HTTP POST request to Resend API
2. Uses cURL to send JSON payload
3. Authenticates with Bearer token (API key)
4. Resend API handles delivery

**API Details:**
- **Endpoint**: `https://api.resend.com/emails`
- **Method**: POST
- **Authentication**: `Authorization: Bearer {API_KEY}`
- **Content-Type**: `application/json`
- **Sender**: `onboarding@resend.dev` (Resend's verified domain)

**Payload Format:**
```json
{
  "from": "Therapair Team <onboarding@resend.dev>",
  "to": "contact@therapair.com.au",
  "subject": "Email Subject",
  "html": "<h1>HTML content</h1>",
  "reply_to": "user@example.com"
}
```

**Code Location:**
- Function: `sendEmailViaResend()` in `submit-form.php` (line ~427)
- Called for both admin and user emails

---

### **Fallback: PHP mail()** (if Resend fails)

- Only used if Resend returns `false`
- Uses PHP's built-in `mail()` function
- Unreliable - often blocked or goes to spam
- Still configured as backup

---

## 🔄 Email Flow

### When Form is Submitted:

1. **Form submission received** → `submit-form.php`

2. **Admin Email** (to `contact@therapair.com.au`):
   - ✅ Try Resend API first
   - ⚠️  Fallback to PHP mail() if Resend fails
   - Contains form submission details

3. **User Confirmation Email** (to user's email):
   - ✅ Try Resend API first
   - ⚠️  Fallback to PHP mail() if Resend fails
   - Personalized confirmation message

---

## 🔑 Configuration

**In `config.php`:**
```php
define('RESEND_API_KEY', 're_CVygh1m3_ADNfJd5t8me7GNo2ZERE8VtU');
define('USE_RESEND', true);
define('ADMIN_EMAIL', 'contact@therapair.com.au');
define('FROM_EMAIL', 'contact@therapair.com.au');
```

---

## ⚠️ Current Limitation

**Resend Account Status:** Testing Mode
- ✅ Can send to: `tinoman@me.com` (verified test email)
- ❌ Cannot send to: Other recipients until domain verified

**To Fix:**
1. Verify `therapair.com.au` domain in Resend
2. Update sender to: `contact@therapair.com.au`
3. Then can send to any email address

---

## 📊 Email Delivery Status

- **Resend API**: ✅ Configured and working
- **PHP mail()**: ⚠️  Available as fallback
- **Domain Verification**: ⏳ Pending (needed for production)

---

## 🧪 Testing

**Test with verified email:**
- Email: `tinoman@me.com`
- Should work immediately with Resend

**Check Resend Dashboard:**
- https://resend.com/emails
- See delivery status and any errors

---

## 📝 Summary

- **Primary**: Resend API (reliable, professional)
- **Fallback**: PHP mail() (unreliable, but functional)
- **Status**: Working, but limited to test email until domain verified

