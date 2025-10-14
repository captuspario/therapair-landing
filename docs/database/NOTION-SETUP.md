# Complete Notion Setup Guide

## 🎯 **Quick Start (5 Minutes)**

This guide combines everything you need to set up and use Notion for Therapair.

---

## ⚡ **5-Minute Setup**

### **Step 1: Create Notion Integration (2 min)**

1. Go to: https://www.notion.so/my-integrations
2. Click **"+ New integration"**
3. Fill in:
   - **Name**: `Therapair Form Sync`
   - **Workspace**: Select yours
4. Click **"Submit"**
5. **Copy the token** (starts with `secret_...` or `ntn_...`)

### **Step 2: Create or Share Databases (2 min)**

You need TWO databases:

**Database 1: Form Submissions**
- For tracking interest from website forms
- Create: New page → `/database` → Table
- Columns: Name, Email, Audience Type, Status, etc.

**Database 2: Victorian Therapists** (Already created!)
- Your imported CSV database
- Already has 202 therapists

**Share both with your integration:**
1. Open each database
2. Click **"Share"** (top right)
3. Click **"Invite"**
4. Select **"Therapair Form Sync"**

### **Step 3: Get Database IDs (1 min)**

For each database:
1. Copy the URL: `notion.so/workspace/DATABASE_ID?v=...`
2. Extract the 32-character `DATABASE_ID`

Your Victorian Therapists ID: `28c5c25944da80a48d85fd43119f4ec1`

---

## 🔧 **Configuration**

### **Update config.php on Server:**

```php
// Notion Configuration
define('USE_NOTION_SYNC', true);
define('NOTION_TOKEN', 'your_token_here');
define('NOTION_DATABASE_ID', 'form_submissions_db_id');
```

### **Update .env for Local Scripts:**

```bash
NOTION_TOKEN=your_token_here
THERAPISTS_DATABASE_ID=28c5c25944da80a48d85fd43119f4ec1
```

---

## 📊 **Database Strategy: Why Two Databases**

### **Database 1: Form Submissions**
**Purpose:** Marketing & lead tracking

**For:**
- New interest from website
- Email campaigns
- Initial conversations
- Converting interest → applications

**Workflow:**
```
New Submission → Contacted → Interested → Invited → Converted
```

**Fields (~15):**
- Name, Email, Audience Type
- Therapy Interests
- Status, Email Preferences
- Submission Date

---

### **Database 2: Therapist Directory**
**Purpose:** Actual therapist profiles

**For:**
- Victorian therapists (CSV import)
- Complete profiles
- Onboarding process
- Published directory

**Workflow:**
```
Import → Pending Review → Verified → Published → Active
```

**Fields (~50):**
- Full identity & contact (7 fields)
- Professional info (10 fields)
- Location & service (11 fields)
- Social media (4 fields)
- Expertise (7 fields)
- Compliance (3 fields)
- System & admin (10 fields)

---

## 🔗 **Linking the Two Databases**

Add Relation properties to connect them:

**In Form Submissions:**
- Add: "Therapist Profile" (Relation to Therapist Directory)

**In Therapist Directory:**
- Add: "Form Submission" (Relation to Form Submissions)

**Benefits:**
- Track full journey from inquiry → profile
- See original interest form
- Maintain audit trail

---

## 🛠️ **Using the Notion API**

### **Read Database Entries:**

```bash
cd /Users/tino/Projects/therapair-landing-page
node scripts/notion/read-notion-therapists.js
```

### **Available Scripts:**

Located in `/scripts/notion/`:
- `read-notion-therapists.js` - View all entries
- `cleanup-notion-database.js` - Clean up data
- (More scripts in local folder, not in git for security)

---

## 📊 **Database Views**

Create these filtered views for workflow management:

### **View 1: Active Pipeline**
- **Filter:** Status = "Pending Review" OR "Verified"
- **Sort:** By Last Contacted (oldest first)
- **Show:** Name, Email, Status, Last Contacted
- **Use:** Daily workflow, who to contact next

### **View 2: Published Therapists**
- **Filter:** Status = "Published" AND Published = ✓
- **Sort:** By Name (A-Z)
- **Show:** All client-facing fields
- **Use:** What's live on website

### **View 3: By Region**
- **Group by:** Region
- **Filter:** Status ≠ "Archived"
- **Sort:** By Suburbs
- **Use:** Geographic overview

### **View 4: Onboarding Required**
- **Filter:** Status = "Pending Review" AND Onboarding Token ≠ empty
- **Show:** Name, Email, Onboarding Token, Token Expiry
- **Use:** Sending invitation emails

### **View 5: Missing Data**
- **Filter:** Phone = empty OR Instagram = empty
- **Show:** Name, Email, Phone, Instagram, Admin Notes
- **Use:** Data completion tasks

### **View 6: Expiring Soon**
- **Filter:** Token Expiry is within 7 days
- **Sort:** By Token Expiry (soonest first)
- **Use:** Follow-up reminders

---

## 🎨 **Profile Page Template**

See `/docs/onboarding/NOTION-PROFILE-PAGE-TEMPLATE.md` for complete guide.

**Quick setup:**
1. Click any therapist
2. Create template with organized sections
3. Add all @ properties
4. Set as default
5. Beautiful one-click profile view!

---

## 🔍 **Common Operations**

### **Find a Therapist:**
```
1. Use search bar at top
2. Type name or email
3. Click to open profile
```

### **Update Status:**
```
1. Click therapist name
2. Change Status dropdown
3. Check Published checkbox if ready
4. Add Admin Notes
```

### **Export to CSV:**
```
1. Click "•••" menu
2. Select "Export"
3. Choose CSV format
4. Use for email campaigns
```

### **Bulk Operations:**
```
1. Select multiple entries (checkbox)
2. Click "•••" above table
3. Choose action (Delete, Archive, Edit properties)
```

---

## 📧 **Email Integration**

Your website forms automatically sync to Notion via `notion-sync.php`:

**When someone submits form:**
1. PHP processes form data
2. Sends email to admin
3. Sends email to user
4. **Syncs to Notion** (if configured)
5. Entry appears in Form Submissions database

**To enable:**
- Set `USE_NOTION_SYNC = true` in config.php
- Add your token and database ID
- Test with a form submission

---

## 🚨 **Troubleshooting**

### **No entries appearing in Notion?**

**Check:**
1. Database is shared with integration
2. Token is correct in config.php
3. Database ID is correct
4. Check PHP error logs on server

**Test connection:**
```bash
curl -X POST "https://api.notion.com/v1/databases/YOUR_DB_ID/query" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Notion-Version: 2022-06-28"
```

### **Can't read database via scripts?**

**Check:**
1. `.env` file has correct NOTION_TOKEN
2. `.env` file has correct THERAPISTS_DATABASE_ID
3. Node packages installed: `npm install @notionhq/client dotenv`

---

## 📊 **Notion vs Traditional Database**

| Feature | Notion (MVP) | SQL Database (Later) |
|---------|--------------|----------------------|
| **Setup** | 5 minutes ✅ | 2-4 hours |
| **UI** | Beautiful ✅ | Need to build |
| **Team Access** | Easy sharing ✅ | Complex permissions |
| **Mobile** | Native app ✅ | Need to build |
| **Views** | Drag & drop ✅ | Write SQL queries |
| **Cost** | Free tier ✅ | Hosting fees |
| **Scale** | Up to 10,000 | Millions |
| **Complex Queries** | Limited | Full SQL ✅ |
| **Best for** | MVP & Growth | Enterprise scale |

**Recommendation:** Use Notion until you hit 1,000+ therapists or need complex queries

---

## 🎯 **When to Migrate to SQL Database**

Migrate when you need:
- [ ] 10,000+ entries
- [ ] Complex multi-table joins
- [ ] Sub-second query performance
- [ ] Real-time matching algorithms
- [ ] Custom analytics dashboard
- [ ] API rate limits become an issue

For your current stage (202 therapists, growing to 500-1000), **Notion is perfect!**

---

## ✅ **Summary**

Your Notion setup provides:
- ✨ **Beautiful visual interface** - No coding required
- ✨ **Two organized databases** - Form leads + Therapist profiles
- ✨ **202 therapists ready** - All cleaned and optimized
- ✨ **Team collaboration** - Easy sharing and comments
- ✨ **Mobile access** - Review anywhere
- ✨ **Scalable structure** - Ready for national growth
- ✨ **Professional workflow** - Track from interest → published

**Total setup time:** ~5-10 minutes  
**Ongoing management:** Simple drag & drop  
**Perfect for your MVP stage!** 🚀
