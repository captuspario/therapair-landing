# Email Template Validation Guide

## 🎯 Purpose

This guide helps prevent broken links and missing CTAs in email templates when making changes.

## ✅ Pre-Deployment Checklist

Before deploying email template changes, always:

1. **Run the validation script:**
   ```bash
   php scripts/validate-email-template.php
   ```

2. **Check for:**
   - ✅ Both CTAs present (Sandbox Demo + Research Survey)
   - ✅ All links use tracking (`track.php`)
   - ✅ No direct links to sandbox/survey
   - ✅ Valid URL formats

3. **Test the actual email:**
   - Submit a test EOI form
   - Check the received email
   - Click all links to verify they work

## 🔧 How It Works

The validation script:
- Generates a mock email template
- Applies tracking link replacement
- Extracts all links
- Validates:
  - Link format (must be valid URLs)
  - Tracking usage (sandbox/survey must use `track.php`)
  - Required CTAs (both sandbox and survey must be present)
  - Tracking URL structure (email hash, destination, UTM params)

## 🚨 Common Issues

### Issue: "Missing Sandbox Demo CTA"
**Cause:** The sandbox link was removed or commented out  
**Fix:** Ensure the sandbox CTA is present in the email template

### Issue: "Missing Research Survey CTA"
**Cause:** The survey link was removed or commented out  
**Fix:** Ensure the survey CTA is present in the email template

### Issue: "Direct link (should use tracking)"
**Cause:** Link uses direct URL instead of `track.php`  
**Fix:** Ensure `addTrackingToEmailLinks()` is called and working

### Issue: "Invalid URL format"
**Cause:** Link is malformed or broken  
**Fix:** Check the link syntax in the template

## 📝 Best Practices

1. **Always run validation before committing:**
   ```bash
   php scripts/validate-email-template.php && git commit -m "Update email template"
   ```

2. **Test with real email:**
   - Use a test email address
   - Submit EOI form
   - Verify all links work

3. **Keep CTAs consistent:**
   - Sandbox Demo (primary CTA)
   - Research Survey (secondary CTA)
   - Both should always be present

4. **Use tracking for all external links:**
   - Sandbox → `track.php?dest=sandbox`
   - Survey → `track.php?dest=survey`
   - This enables click tracking in Notion

## 🔄 Integration with Deployment

The deployment script should run validation automatically:

```bash
# In deploy-to-hostinger.sh
if ! php scripts/validate-email-template.php; then
    echo "❌ Email template validation failed!"
    exit 1
fi
```

## 📚 Related Files

- `submit-form.php` - Email template generation
- `email-template-base.php` - Base template and button styles
- `track.php` - Link tracking and redirection
- `scripts/validate-email-template.php` - Validation script

