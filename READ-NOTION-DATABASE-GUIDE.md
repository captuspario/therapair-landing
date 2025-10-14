# How to Read Your Victorian Therapists Notion Database

## 🎯 **Quick Setup (2 minutes)**

### **Step 1: Get Your Database ID**

1. Open your **"Victorian Inclusive Therapists (Demo)"** database in Notion
2. Look at the URL, it looks like:
   ```
   https://www.notion.so/your-workspace/DATABASE_ID?v=...
   ```
3. Copy the 32-character **DATABASE_ID** (the part between your workspace name and the `?`)
   - Example: `a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6`

### **Step 2: Add to Your .env File**

Open or create `.env` file in `/Users/tino/Projects/therapair-landing-page/`:

```bash
# Existing Notion config
NOTION_TOKEN=secret_your_token_here
NOTION_DATABASE_ID=your_form_submissions_db_id

# Add this new line for Victorian Therapists
THERAPISTS_DATABASE_ID=paste_your_database_id_here
```

### **Step 3: Install Notion SDK**

```bash
cd /Users/tino/Projects/therapair-landing-page
npm install @notionhq/client dotenv
```

### **Step 4: Run the Script**

```bash
node read-notion-therapists.js
```

---

## 📊 **What You'll See**

The script will show you:

1. **Database Name** - Confirms it's the right database
2. **All Columns (Properties)** - Every field in your database
3. **First 10 Therapists** - Sample data with key fields
4. **Total Count** - How many therapists imported

Example output:
```
═══════════════════════════════════════════════════════
  Victorian Therapists Database Reader
═══════════════════════════════════════════════════════

🔍 Reading Victorian Therapists database...

📊 Database Name: Victorian Inclusive Therapists (Demo)
🆔 Database ID: a1b2c3d4e5f6...

📋 Properties (Columns):
────────────────────────────────────────────────────────
1. Fullname (title)
2. Email Address (email)
3. Profession (text)
4. Gender (select)
5. Suburb/s of practice (text)
...

📄 Fetching therapist entries...

✅ Found 10 entries (showing first 10)
────────────────────────────────────────────────────────

1. Therapist Entry:
   Fullname: Aaron Howearth
   Email Address: info@HowearthPsychology.com.au
   Profession: Psychologist
   Gender: Man/Male
   Suburb/s of practice: St Kilda, Online
   Area: Melbourne CBD

2. Therapist Entry:
   Fullname: Abby Draper
   ...
```

---

## 🔧 **Troubleshooting**

### **Error: "object_not_found"**
**Problem:** Database not shared with integration

**Fix:**
1. Open your database in Notion
2. Click **"Share"** (top right)
3. Click **"Invite"**
4. Select your integration (same one used for form submissions)
5. Click **"Invite"**

### **Error: "THERAPISTS_DATABASE_ID not found"**
**Problem:** Missing environment variable

**Fix:**
1. Create/edit `.env` file
2. Add: `THERAPISTS_DATABASE_ID=your_database_id_here`
3. Make sure there are no spaces around the `=`

### **Error: "NOTION_TOKEN not found"**
**Problem:** Missing Notion token

**Fix:**
Check your config.php or .env for the token and add it to .env:
```bash
NOTION_TOKEN=secret_your_token_here
```

---

## 🎯 **What's Next?**

Once the script reads your database, I can:

1. **Show you the exact structure** - All columns and their types
2. **Count therapists** - How many imported successfully
3. **Identify missing data** - Which fields need cleanup
4. **Generate tokens** - Create onboarding links for each therapist
5. **Create email list** - Export for invitation campaign

---

## 💡 **Quick Alternative: Manual Check**

If you prefer, you can also tell me:

1. **How many therapists** are in the database?
2. **What column names** do you see? (screenshot or list)
3. **Any data issues** you notice?

Then I can create the right scripts for you!

---

## 🚀 **Run It Now**

```bash
# Navigate to project
cd /Users/tino/Projects/therapair-landing-page

# Install dependencies (if not already)
npm install @notionhq/client dotenv

# Add your database ID to .env
echo "THERAPISTS_DATABASE_ID=YOUR_DB_ID_HERE" >> .env

# Run the script
node read-notion-therapists.js
```

This will show you exactly what's in your database! 📊
