# Credentials Setup & Validation

## Overview

This document tracks the API credentials and database IDs used for the Therapair research system.

## Current Configuration

### Notion Integration
- **Token:** `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`
- **Status:** ⚠️ Currently invalid - needs to be refreshed/regenerated

### Notion Databases
- **VIC Therapist DB:** `28c5c25944da80a48d85fd43119f4ec1`
- **Research DB:** `2995c25944da80a5b5d1f0eb9db74a36`
- **EOI DB:** `2995c25944da80a5b5d1f0eb9db74a36`
- **Feedback DB:** `2a75c25944da804cbd87d4daac0ae901`

### Resend API
- **Key:** `re_XBAyknhx_LvvGatDreKp5ffepJ2LjmFoW`
- **Status:** ✅ Valid

## Files

### Environment Variables
- **Location:** `research/.env` (gitignored)
- **Contains:** All API keys and database IDs
- **Never commit this file!**

### PHP Configuration
- **Location:** `config.php` (gitignored)
- **Contains:** PHP constants for API keys and database IDs
- **Never commit this file!**

## Validation

### Run Validation Script
```bash
cd research/scripts
node validate-credentials.mjs
```

This script checks:
- ✅ All required environment variables are set
- ✅ Notion API token is valid
- ✅ Database IDs are accessible
- ✅ Resend API key is valid

### When Credentials Break

1. **Notion Token Invalid:**
   - Go to: https://www.notion.so/my-integrations
   - Find the integration
   - Regenerate token or check permissions
   - Update `research/.env` and `config.php`

2. **Resend API Key Invalid:**
   - Go to: https://resend.com/api-keys
   - Create new API key
   - Update `research/.env` and `config.php`

3. **Database Access Denied:**
   - Check integration has access to the database
   - In Notion: Database → Share → Add integration
   - Verify database IDs are correct

## Prevention

### Before Running Scripts
Always run the validation script first:
```bash
node research/scripts/validate-credentials.mjs
```

### Regular Checks
- Run validation weekly
- Check for API key expiration emails
- Monitor script failures for credential errors

### Documentation
- Keep this file updated when credentials change
- Document any credential issues and resolutions
- Note expiration dates if known

## Quick Reference

### Update .env File
```bash
cd research
nano .env
# Edit credentials
# Save and exit
```

### Update config.php
```bash
cd products/landing-page
nano config.php
# Edit credentials
# Save and exit
```

### Test After Update
```bash
cd research/scripts
node validate-credentials.mjs
```


