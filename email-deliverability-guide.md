# Email Deliverability Guide for Therapair

## Current Status
✅ Emails are being sent successfully  
❌ Emails are going to spam folders  

## Quick Fixes (Immediate)

### 1. Add Email to Safe Senders
- **Gmail**: Add `noreply@therapair.com.au` to contacts
- **Outlook**: Add to safe senders list
- **Apple Mail**: Mark as not junk

### 2. Check These Folders
- Spam/Junk folder
- Promotions tab (Gmail)
- All Mail folder

## Long-term Solutions (Recommended)

### 1. Domain Authentication (Most Important)
Add these DNS records to your domain:

#### SPF Record
```
Type: TXT
Name: @ (or therapair.com.au)
Value: v=spf1 include:_spf.google.com ~all
```

#### DKIM Record
```
Type: TXT
Name: default._domainkey
Value: [Get from Hostinger email settings]
```

#### DMARC Record
```
Type: TXT
Name: _dmarc
Value: v=DMARC1; p=quarantine; rua=mailto:contact@therapair.com.au
```

### 2. Professional Email Service (Best Option)
Consider upgrading to:
- **SendGrid** ($15/month for 40k emails)
- **Mailgun** ($35/month for 50k emails)
- **Amazon SES** ($0.10 per 1000 emails)

Benefits:
- Better deliverability
- Detailed analytics
- Bounce handling
- Reputation management

### 3. Email Template Improvements
- Avoid spam trigger words
- Keep subject lines simple
- Include unsubscribe option
- Use proper HTML structure

## Hostinger-Specific Setup

### 1. Email Configuration
- Log into Hostinger control panel
- Go to Email section
- Set up SPF/DKIM records
- Configure email forwarding

### 2. PHP Mail Settings
Current setup uses PHP's `mail()` function which is basic but functional.

## Testing Email Deliverability

### Tools to Check
- **Mail-tester.com** - Free spam score checker
- **MXToolbox** - DNS record checker
- **Google Postmaster Tools** - Gmail deliverability

### Test Process
1. Send test email to multiple providers
2. Check spam scores
3. Monitor delivery rates
4. Adjust settings based on results

## Current Email Headers (Already Implemented)
```
From: Therapair <noreply@therapair.com.au>
Reply-To: contact@therapair.com.au
X-Mailer: Therapair Automated Response
X-Priority: 3
Content-Type: text/html; charset=UTF-8
```

## Next Steps
1. **Immediate**: Check spam folders and add to safe senders
2. **Short-term**: Set up SPF/DKIM records via Hostinger
3. **Long-term**: Consider professional email service for better deliverability

## Cost Analysis
- **Current**: Free (PHP mail)
- **Professional Service**: $15-35/month
- **ROI**: Better user experience, fewer missed emails, professional appearance
