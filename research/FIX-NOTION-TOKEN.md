# Fix Notion API Token Issue

## Current Status
- ❌ Notion API token is invalid (401 Unauthorized)
- ✅ Resend API key is working
- ✅ Email sending works (but Notion entry creation fails)

## How to Fix

### Step 1: Check Integration Status
1. Go to: https://www.notion.so/my-integrations
2. Find your Therapair integration
3. Check if it's **Active**

### Step 2: Regenerate Token (if needed)
1. Click on the integration
2. Go to **"Internal Integration Token"**
3. Click **"Show"** then **"Copy"**
4. If token looks different, it may have been regenerated

### Step 3: Verify Database Access
For each database, ensure the integration has access:

1. **VIC Therapist DB** (`28c5c25944da80a48d85fd43119f4ec1`)
   - Open database in Notion
   - Click **"..."** (three dots) → **"Add connections"**
   - Select your Therapair integration
   - Click **"Confirm"**

2. **Research DB** (`2995c25944da80a5b5d1f0eb9db74a36`)
   - Repeat above steps

3. **EOI DB** (`2995c25944da80a5b5d1f0eb9db74a36`)
   - Repeat above steps

4. **Feedback DB** (`2a75c25944da804cbd87d4daac0ae901`)
   - Repeat above steps

### Step 4: Update Credentials
Once you have a valid token:

1. **Update `.env` file:**
   ```bash
   cd research
   nano .env
   # Update NOTION_TOKEN=your_new_token_here
   ```

2. **Update `config.php`:**
   ```bash
   cd products/landing-page
   nano config.php
   # Update define('NOTION_TOKEN', 'your_new_token_here');
   ```

### Step 5: Validate
```bash
cd research/scripts
node validate-credentials.mjs
```

You should see:
- ✅ Notion API token is valid
- ✅ All databases accessible

## Alternative: Create Integration Fresh

If the token keeps failing:

1. **Create New Integration:**
   - Go to: https://www.notion.so/my-integrations
   - Click **"+ New integration"**
   - Name: "Therapair Research"
   - Type: **Internal integration**
   - Click **"Submit"**

2. **Copy New Token:**
   - Copy the token (starts with `secret_` or `ntn_`)

3. **Share Databases:**
   - For each database, add the new integration

4. **Update Credentials:**
   - Update both `.env` and `config.php`

## Current Token (for reference)
```
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
```

**Status:** ❌ Invalid (401 Unauthorized)

## Test Token Manually
```bash
curl -H "Authorization: Bearer YOUR_TOKEN" \
     -H "Notion-Version: 2022-06-28" \
     "https://api.notion.com/v1/users/me"
```

Should return user info, not an error.


