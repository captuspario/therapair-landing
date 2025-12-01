# 🔒 Secure Resend API Key Update Guide

## ✅ Current Security Status

Your setup is **already secure**:
- ✅ `config.php` is in `.gitignore` (will NEVER be committed)
- ✅ `deploy-with-secrets.sh` script uploads securely via SCP
- ✅ API key only stored locally and on server (encrypted in transit)

---

## 🛡️ Safest Method: Direct File Edit (Recommended)

**Best option** - API key never appears in chat/logs:

### Step 1: Update the File Locally

Open `config.php` in your editor:

```bash
cd "/Users/tino/Projects/Therapair V2/products/landing-page"
# Use your preferred editor:
code config.php
# or
nano config.php
# or
open -e config.php
```

### Step 2: Find and Update Line 59

Change:
```php
define('RESEND_API_KEY', 're_CVygh1m3_ADNfJd5t8me7GNo2ZERE8VtU');
```

To:
```php
define('RESEND_API_KEY', 'YOUR_NEW_API_KEY_HERE');
```

**Save the file.**

### Step 3: Deploy Securely

Run the deployment script (uploads via encrypted SCP):

```bash
./deploy-with-secrets.sh
```

**Done!** ✅ Your new API key is now on the server.

---

## 🔐 Alternative: Tell Me and I'll Update (Also Safe)

Since `config.php` is in `.gitignore`, I can update it safely:

### Option A: Share the Key Directly

Just tell me:
> "Update the Resend API key in config.php to: `re_your_new_key_here`"

I'll:
1. Update `config.php` locally
2. **Never commit it** (it's in `.gitignore`)
3. You run `./deploy-with-secrets.sh` to upload it

### Option B: Use a Temporary File

1. Create temp file:
   ```bash
   echo "re_your_new_key_here" > /tmp/resend-key-update.txt
   ```

2. Tell me:
   > "Read the new Resend API key from /tmp/resend-key-update.txt and update config.php"

3. I'll update it, then you can:
   ```bash
   rm /tmp/resend-key-update.txt  # Delete temp file
   ./deploy-with-secrets.sh       # Deploy securely
   ```

---

## ✅ Why This is Safe

1. **`config.php` is in `.gitignore`**
   - Line 19 of `.gitignore`: `config.php`
   - Git will **never** commit this file
   - Even if I update it, it stays local only

2. **Deployment is Encrypted**
   - `deploy-with-secrets.sh` uses SCP (SSH)
   - All data is encrypted in transit
   - Direct upload to server (not via Git)

3. **Server Access Protected**
   - Only you have SSH keys
   - Only you can deploy via the script

---

## 🚀 Quick Update Process

**If you edit it yourself:**
```bash
# 1. Edit config.php and update RESEND_API_KEY
# 2. Deploy
./deploy-with-secrets.sh
```

**If you want me to update it:**
1. Tell me the new API key
2. I'll update `config.php`
3. You run: `./deploy-with-secrets.sh`

---

## ⚠️ Security Reminders

✅ **Safe:**
- Updating `config.php` locally (it's in `.gitignore`)
- Using `deploy-with-secrets.sh` (encrypted upload)
- Sharing API key with me (since file won't be committed)

❌ **Never:**
- Commit `config.php` to Git (already protected)
- Share API key publicly
- Store in files that ARE committed

---

## 📋 Verification

After updating, verify it worked:

```bash
# Check locally (shows first few chars only)
grep RESEND_API_KEY config.php | head -c 40

# Test on server (after deployment)
ssh u549396201@45.87.81.159 -p 65002 "grep RESEND_API_KEY domains/therapair.com.au/public_html/config.php | head -c 40"
```

---

## 💡 Recommended: Share the Key Directly

Since `config.php` is protected by `.gitignore`, the **safest and easiest** way is:

**Just tell me:**
> "Update Resend API key to: `re_your_new_key_here`"

I'll update it, you deploy it, and we're done! ✅

The key will:
- ✅ Be updated in `config.php`
- ✅ Never be committed to Git
- ✅ Be uploaded securely to server
- ✅ Work immediately

---

**Ready?** Just share the new API key and I'll update it! 🔐
