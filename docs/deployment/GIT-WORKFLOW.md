# Git Workflow & Deployment Process

## Overview

This document outlines the version control and deployment workflow for the Therapair Landing Page.

---

## 🔄 Git Workflow

### Repository Structure
- **Remote:** `https://github.com/captuspario/therapair-landing.git`
- **Main Branch:** `main` (production-ready code)
- **Local Path:** `/Users/tino/Projects/Therapair V2/products/landing-page`

### Standard Workflow

1. **Make Changes Locally**
   ```bash
   cd "/Users/tino/Projects/Therapair V2/products/landing-page"
   # Edit files...
   ```

2. **Stage Changes**
   ```bash
   git add <files>
   # Or for all changes:
   git add .
   ```

3. **Commit with Descriptive Message**
   ```bash
   git commit -m "feat: Add mobile responsive improvements"
   # Or:
   git commit -m "fix: Correct token validation logic"
   ```

4. **Push to GitHub**
   ```bash
   git push origin main
   ```

5. **Deploy to Hostinger**
   ```bash
   ./scripts/deployment/deploy-to-hostinger.sh
   ```

---

## 🚀 Deployment Process

### Automated Deployment Script

The deployment script (`scripts/deployment/deploy-to-hostinger.sh`) performs:

1. **Push to GitHub** - Ensures all local commits are backed up remotely
2. **Pull on Hostinger** - Updates the live site from GitHub

```bash
#!/bin/bash
# 1. Push local commits to GitHub
git push origin main

# 2. Pull latest changes on Hostinger
ssh u549396201@45.87.81.159 -p 65002 \
  'cd domains/therapair.com.au/public_html && git pull origin main'
```

### Manual Deployment Steps

If you need to deploy manually:

```bash
# 1. Push to GitHub
cd "/Users/tino/Projects/Therapair V2/products/landing-page"
git push origin main

# 2. SSH into Hostinger and pull
ssh -p 65002 u549396201@45.87.81.159
cd domains/therapair.com.au/public_html
git pull origin main
exit
```

---

## 🔐 Security & Secrets Management

### What's Gitignored

The following files are **never committed** to git:

- `config.php` - Contains API keys and secrets
- `.env` files - Environment variables
- `node_modules/` - Dependencies
- Test results and temporary files

### Secret Management Best Practices

1. **Never hardcode secrets** in code files
2. **Use environment variables** or `config.php` (gitignored)
3. **Scripts should fail** if required secrets are missing
4. **Review commits** before pushing to catch accidental secret commits

### Example: Safe Script Pattern

```javascript
// ✅ GOOD: Require environment variable
const NOTION_TOKEN = process.env.NOTION_TOKEN;
if (!NOTION_TOKEN) {
  console.error('❌ Error: NOTION_TOKEN required');
  process.exit(1);
}

// ❌ BAD: Hardcoded fallback
const NOTION_TOKEN = process.env.NOTION_TOKEN || 'hardcoded-secret';
```

---

## 📋 Commit Message Conventions

Use clear, descriptive commit messages:

```
feat: Add A/B testing system for research emails
fix: Correct token signature encoding
docs: Update deployment workflow documentation
refactor: Simplify Notion API request helper
style: Improve mobile responsive layout
test: Add survey submission smoke tests
chore: Update dependencies
```

---

## 🔍 Pre-Deployment Checklist

Before deploying, verify:

- [ ] All changes are committed locally
- [ ] No secrets are hardcoded in code
- [ ] `config.php` is gitignored (not committed)
- [ ] Tests pass (if applicable)
- [ ] Changes are pushed to GitHub
- [ ] Deployment script has correct SSH credentials

---

## 🐛 Troubleshooting

### Push Fails: "Repository rule violations"

**Issue:** GitHub secret scanning detected secrets in commit history

**Solution:**
1. Remove secrets from current code
2. Visit GitHub's unblock URL (one-time) or clean git history
3. Ensure secrets are in `.gitignore` and `config.php`

### Deployment Fails: "Permission denied"

**Issue:** SSH key not configured or incorrect permissions

**Solution:**
1. Verify SSH key is added to Hostinger account
2. Test SSH connection: `ssh -p 65002 u549396201@45.87.81.159`
3. Check Hostinger Git repository permissions

### Local Changes Not Syncing

**Issue:** Local commits not pushed to GitHub

**Solution:**
```bash
# Check status
git status

# Push to GitHub
git push origin main

# Verify sync
git log origin/main..HEAD  # Should show no commits
```

---

## 📊 Version Control Status

### Check Current Status

```bash
# See uncommitted changes
git status

# See commits not yet pushed
git log origin/main..HEAD --oneline

# See all branches
git branch -a

# See remote repositories
git remote -v
```

### Backup Strategy

- **GitHub** - Primary remote backup (all commits)
- **Hostinger** - Production deployment (pulls from GitHub)
- **Local** - Development workspace

---

## 🔄 Sync Process Summary

1. **Local → GitHub:** `git push origin main`
2. **GitHub → Hostinger:** Automatic via deployment script
3. **Backup:** All code is in GitHub (version controlled)

---

## 📝 Notes

- Always push to GitHub before deploying to ensure backups
- The deployment script automatically handles the GitHub → Hostinger sync
- `config.php` must be manually uploaded to Hostinger (not in git)
- Test changes locally before committing and deploying

