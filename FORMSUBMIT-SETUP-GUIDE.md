# 📧 FormSubmit.co Setup Guide for Therapair Landing Page

## ✅ What You've Got

I've created a **complete FormSubmit.co integration** for your landing page that:

- ✅ Captures all audience types (Individual, Therapist, Organization, Supporter/Investor)
- ✅ Handles dynamic form fields based on audience selection
- ✅ Sends professional emails to you at `contact@therapair.com.au`
- ✅ Sends automatic confirmation emails to users
- ✅ Includes spam protection (honeypot field)
- ✅ Redirects to a beautiful thank-you page
- ✅ Tracks conversions with Google Analytics
- ✅ **100% FREE** (no monthly costs)

---

## 📁 Files Created

1. **`index-formsubmit.html`** - Your updated landing page with FormSubmit.co integration
2. **`thank-you.html`** - Professional thank-you page shown after submission
3. **`FORMSUBMIT-SETUP-GUIDE.md`** - This guide!

---

## 🚀 Setup Instructions (10 minutes)

### **Step 1: Verify Your FormSubmit.co Email** (One-time activation)

FormSubmit.co requires a one-time verification to prevent spam. Here's how:

1. **Upload both files to your Hostinger hosting:**
   - `index-formsubmit.html` (rename to `index.html` or keep as test version)
   - `thank-you.html`

2. **Visit your landing page** and fill out the form with YOUR email (use a test submission)

3. **Check your inbox** at `contact@therapair.com.au` for an email from FormSubmit.co that says:
   ```
   Subject: Confirm Email for FormSubmit
   ```

4. **Click the activation link** in that email

5. **Done!** Your form is now active and ready to receive submissions.

---

### **Step 2: Upload Files to Hostinger**

#### **Option A: Using Hostinger File Manager (Easiest)**

1. Log in to your Hostinger control panel
2. Go to **Files** → **File Manager**
3. Navigate to your `public_html` or website root directory
4. Click **Upload** and upload:
   - `index-formsubmit.html`
   - `thank-you.html`
5. **Rename** `index-formsubmit.html` to `index.html` (or keep your old `index.html` and rename this to test it first)

#### **Option B: Using FTP (If you prefer)**

1. Use an FTP client (FileZilla, Cyberduck)
2. Connect to your Hostinger FTP (credentials in Hostinger panel)
3. Upload both files to your website root

---

### **Step 3: Test Your Form**

1. **Visit your website** (e.g., `https://therapair.com.au`)
2. **Fill out the form** with real data
3. **Check you receive:**
   - ✅ Email notification at `contact@therapair.com.au`
   - ✅ User receives automatic confirmation email
   - ✅ Redirect to thank-you page works
4. **Check spam folder** if you don't see emails immediately

---

## 🎨 Customization Options

### **Change Email Configurations**

Edit these lines in `index-formsubmit.html` (around line 330):

```html
<!-- Change the recipient email -->
<form action="https://formsubmit.co/YOUR-EMAIL@example.com" method="POST">

<!-- Change email subject -->
<input type="hidden" name="_subject" value="🎯 Your Custom Subject">

<!-- Change auto-response message -->
<input type="hidden" name="_autoresponse" value="Your custom message to users...">

<!-- Change redirect URL -->
<input type="hidden" name="_next" value="https://yourdomain.com/custom-thank-you-page">
```

### **Disable CAPTCHA (Already Done)**

CAPTCHA is disabled for better user experience:
```html
<input type="hidden" name="_captcha" value="false">
```

If you want to enable it (to reduce spam):
```html
<input type="hidden" name="_captcha" value="true">
```

### **Customize Email Template**

FormSubmit.co offers different email templates. Current setting:
```html
<input type="hidden" name="_template" value="box">
```

Available templates:
- `box` (default, clean design)
- `table` (tabular layout)
- `basic` (plain text)

---

## 📊 Email Format You'll Receive

### **For Individual Interest:**
```
Subject: 🎯 New Interest: Individual Seeking Therapy

Audience Type: Individual
Email: user@example.com
Therapy Interests: LGBTQ+ affirming care, Trauma-informed care
```

### **For Therapist Interest:**
```
Subject: 👨‍⚕️ New Interest: Mental Health Professional

Audience Type: Therapist
Full Name: Dr. Jane Smith
Professional Title: Licensed Clinical Psychologist
Organization: ABC Therapy Group
Email: jane@example.com
Specializations: Trauma therapy, EMDR, working with LGBTQ+ community...
```

---

## 🔒 Spam Protection Features

Your form includes multiple spam protection layers:

1. **Honeypot Field** (`_honey`) - Hidden field that bots fill but humans don't
2. **CAPTCHA Disabled** - But you can enable if needed
3. **FormSubmit.co's built-in filters** - Blocks obvious spam
4. **Email verification** - Only receives from verified domains

---

## 🐛 Troubleshooting

### **Problem: Not receiving emails**

**Check:**
1. ✅ Did you activate FormSubmit.co via the verification email?
2. ✅ Is the email in your spam folder?
3. ✅ Is `contact@therapair.com.au` a valid, active email?
4. ✅ Try submitting with a different email to test

**Solution:** Submit the form again and check your spam folder for the FormSubmit.co activation email.

---

### **Problem: Redirect not working**

**Check:**
1. ✅ Is `thank-you.html` uploaded to your server?
2. ✅ Is the path correct in the `_next` field?

**Current setting:**
```html
<input type="hidden" name="_next" value="https://therapair.com.au/thank-you.html">
```

**Update to your actual domain if different!**

---

### **Problem: Form fields not showing**

**Check:**
1. ✅ Did you click on one of the audience selector cards first?
2. ✅ The form requires audience selection before showing fields

---

### **Problem: Auto-response not sending to users**

**Check:**
1. ✅ The `_autoresponse` field is properly set (it is by default)
2. ✅ User's email is valid
3. ✅ Check their spam folder

---

## 🎯 Advanced Features (Optional)

### **Add File Uploads**

FormSubmit.co supports file attachments:
```html
<input type="file" name="attachment" accept=".pdf,.doc,.docx">
```

### **CC Additional Emails**

Send copies to multiple people:
```html
<input type="hidden" name="_cc" value="admin@therapair.com.au,team@therapair.com.au">
```

### **BCC (Blind Copy)**

Send hidden copies:
```html
<input type="hidden" name="_bcc" value="archive@therapair.com.au">
```

### **Reply-To Field**

Set custom reply-to address:
```html
<input type="hidden" name="_replyto" value="support@therapair.com.au">
```

---

## 📈 Analytics & Tracking

Your form automatically tracks:

1. **Google Analytics Events:**
   - `audience_selected` - When user picks audience type
   - `form_submission` - When form is submitted
   - `conversion` - On thank-you page

2. **View Analytics In:**
   - Google Analytics → Events
   - Look for "Form Interaction" and "Form Submission" events

---

## 💰 Cost Breakdown

| Service | Cost | What It Does |
|---------|------|--------------|
| **FormSubmit.co** | **$0/month** | Unlimited form submissions & emails |
| **Hostinger** | Your existing plan | Hosts the HTML files |
| **Total** | **$0/month** | Complete form submission system |

---

## 🚀 Next Steps (Optional Upgrades)

### **If You Need More Features Later:**

1. **Add to Airtable/Google Sheets** (Free automation with Zapier)
   - Zapier free tier: 100 tasks/month
   - Create a Zap: FormSubmit.co email → Parse → Add to spreadsheet

2. **Add Slack Notifications** (Get instant alerts)
   - Use Zapier: FormSubmit.co → Slack webhook

3. **Add to CRM** (When you're ready)
   - Integrate with HubSpot, Pipedrive, etc. via Zapier

4. **HIPAA Compliance** (For sensitive data)
   - Consider upgrading to Postmark or custom backend
   - Current setup is fine for general interest forms

---

## 📋 Testing Checklist

Before going live, test these scenarios:

- [ ] **Individual submission** - Select "Individual", choose interests, submit
- [ ] **Therapist submission** - Select "Therapist", fill all fields, submit
- [ ] **Organization submission** - Select "Organization", fill all fields, submit
- [ ] **Supporter submission** - Select "Other", fill fields, submit
- [ ] **Email received** at contact@therapair.com.au
- [ ] **Auto-response sent** to user's email
- [ ] **Thank-you page** shows correctly
- [ ] **Mobile testing** - Form works on phone
- [ ] **Spam folder check** - Emails not going to spam

---

## 🆘 Need Help?

### **FormSubmit.co Documentation:**
- https://formsubmit.co/documentation

### **Common Issues:**
1. **Not receiving emails?** → Check spam, verify activation
2. **Form not submitting?** → Check browser console for errors
3. **Redirect not working?** → Verify thank-you.html is uploaded

---

## 🎉 You're All Set!

Your landing page now has a **professional, free, and reliable** form submission system. No backend code needed, no monthly costs, and it just works!

### **What You Accomplished:**
✅ Professional email notifications
✅ Automatic user confirmations
✅ Beautiful thank-you page
✅ Spam protection
✅ Analytics tracking
✅ Mobile-responsive forms
✅ Zero monthly costs

---

## 📝 Quick Reference

### **Your Form URL:**
```
https://therapair.com.au/index.html
```

### **Your Thank-You URL:**
```
https://therapair.com.au/thank-you.html
```

### **FormSubmit Action:**
```
https://formsubmit.co/contact@therapair.com.au
```

### **Test Submission Checklist:**
1. Visit form
2. Select audience type
3. Fill all required fields
4. Submit
5. Check email at contact@therapair.com.au
6. Verify redirect to thank-you page
7. Check user received auto-response

---

**Need modifications?** Just ask! I can help you customize the form, add fields, change styling, or integrate with other services.

**Ready to go live?** Just:
1. Activate via FormSubmit.co verification email
2. Upload both HTML files
3. Test once
4. You're live! 🚀

