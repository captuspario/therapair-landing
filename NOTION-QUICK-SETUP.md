# ✅ Notion Quick Setup (5 Minutes)

## **You Already Have Everything Set Up!**

Your code is **already Notion-ready**. You just need to:
1. Create the Notion integration (2 min)
2. Create the database (2 min)
3. Update config.php (1 min)

---

## 📝 **Step 1: Create Notion Integration**

1. Go to: https://www.notion.so/my-integrations
2. Click **"+ New integration"**
3. Fill in:
   - **Name**: `Therapair Form Sync`
   - **Workspace**: Select yours
4. Click **"Submit"**
5. **Copy the token** (starts with `secret_...`)
   - Keep this safe - you'll paste it in Step 3

---

## 📊 **Step 2: Create Notion Database**

1. Open Notion
2. Create new page: **"Therapair Form Submissions"**
3. Type `/database` → Select **"Table - Inline"**
4. Create these columns:

```
Name                  | Title        | (auto)
Email                 | Email        | (auto)
Audience Type         | Select       | Individual, Therapist, Organization, Other
Status                | Status       | New, Contacted, Converted, Archived
Submission Date       | Date         | (auto)
Email Preferences     | Multi-select | Product Updates, Launch News, etc.

--- For Individuals ---
Therapy Interests     | Multi-select | LGBTQ+, Neurodiversity, Trauma, etc.
Additional Thoughts   | Text         | Long-form
Interest Level        | Select       | High, Medium, Low

--- For Therapists ---
Full Name             | Text         |
Professional Title    | Text         |
Organisation          | Text         |
Specialisations       | Text         |
Verification Status   | Select       | Pending, Verified, Declined
Onboarding Stage      | Select       | Interest, Invited, Active

--- For Organizations ---
Contact Name          | Text         |
Position              | Text         |
Organisation Name     | Text         |
Partnership Interest  | Text         |
Partnership Type      | Select       | Collaboration, Referral, Integration

--- For Supporters ---
Support Interest      | Text         |
Support Type          | Select       | Advocate, Investor, Advisor
Engagement Level      | Select       | High, Medium, Low
```

5. **Share with your integration:**
   - Click **"Share"** (top right)
   - Click **"Invite"**
   - Select **"Therapair Form Sync"**

6. **Copy Database ID:**
   - URL looks like: `notion.so/username/DATABASE_ID?v=...`
   - Copy the 32-character `DATABASE_ID`

---

## ⚙️ **Step 3: Update config.php**

Edit this file on your server:
```
/home/u549396201/domains/therapair.com.au/public_html/config.php
```

Find these lines and update:
```php
// Notion Configuration
define('USE_NOTION_SYNC', true);                          // Change to TRUE
define('NOTION_TOKEN', 'secret_YOUR_TOKEN_HERE');         // Paste your token
define('NOTION_DATABASE_ID', 'YOUR_DATABASE_ID_HERE');    // Paste your database ID
```

**Important:** Your config.php might use `NOTION_TOKEN` or `NOTION_API_KEY`. Check which one and update accordingly!

---

## ✅ **Step 4: Test It**

1. Visit: https://therapair.com.au
2. Scroll to "Request Early Access" form
3. Fill it out and submit
4. Check your Notion database
5. **You should see a new entry!** 🎉

---

## 🔍 **How to View Your Entries**

### **In Notion (Recommended)**
- Open your "Therapair Form Submissions" database
- See all entries in a beautiful table
- Filter by Status, Audience Type, Date
- Add notes and comments
- Update statuses as you follow up

### **Create Views**
1. **New Submissions** - Filter: Status = "New"
2. **Individuals** - Filter: Audience Type = "Individual"
3. **Therapists** - Filter: Audience Type = "Therapist"
4. **This Week** - Filter: Submission Date = This week

---

## 🎯 **Quick Workflow**

1. **New submission arrives** → See it in Notion instantly
2. **Review details** → All form data is there
3. **Update status** → Change from "New" to "Contacted"
4. **Add notes** → Comments and follow-up reminders
5. **Export if needed** → CSV export anytime

---

## 🚨 **Troubleshooting**

### **No entries appearing?**
1. Check Hostinger error logs
2. Verify config.php has correct credentials
3. Make sure you shared database with integration
4. Test with a simple submission

### **Check your config.php location**
```bash
# SSH into Hostinger
ssh u549396201@therapair.com.au

# Find config.php
ls -la /home/u549396201/domains/therapair.com.au/public_html/config.php

# View current settings (without exposing secrets)
grep -E "USE_NOTION|NOTION_TOKEN|NOTION_DATABASE" config.php
```

### **Check if submissions are being sent**
Check Hostinger PHP error logs for:
```
"Notion sync failed" or "Notion API error"
```

---

## 💡 **Pro Tips**

1. **Create a dashboard** - Linked database with stats
2. **Set up automation** - Notion can send Slack notifications
3. **Use templates** - Create response templates in Notion
4. **Timeline view** - Visualize submission trends
5. **Share with team** - Give access to specific views only

---

## 📈 **Next Steps After Setup**

1. ✅ Test with a few submissions
2. ✅ Create filtered views for each audience type
3. ✅ Set up status workflow (New → Contacted → Converted)
4. ✅ Add follow-up reminders using Notion's date features
5. ✅ Export to CSV weekly for backup

---

## 🆚 **Notion vs Database Comparison**

| Feature | Notion (Now) | Database (Later) |
|---------|-------------|------------------|
| Setup | 5 minutes | 2-4 hours |
| View entries | ✅ Beautiful UI | Need to build |
| Team access | ✅ Easy | Need to configure |
| Mobile app | ✅ Native | Need to build |
| Cost | Free | Hosting fees |
| Scale | Up to 10,000 | Millions |
| When to use | **MVP stage** | Production scale |

**Recommendation:** Start with Notion now, migrate to database when you hit 1000+ entries or need complex queries.

---

## ✅ **You're All Set!**

Your Notion integration is just 3 steps away:
1. Create integration (2 min)
2. Create database (2 min)  
3. Update config.php (1 min)

Then you can immediately see all form submissions in Notion! 🎉
