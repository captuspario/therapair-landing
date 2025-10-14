# Victorian Therapists Notion Database - Next Steps

## ✅ **Great! You've Imported the CSV**

Your database: **"Victorian Inclusive Therapists (Demo)"**

---

## 📋 **Immediate Next Steps**

### **Step 1: Clean Up the Import**

The CSV likely imported with some formatting issues. Check:

1. **Remove header rows**
   - The CSV has instruction rows at the top
   - Delete any rows that say "Select Area or Gender Dropdown"
   - Keep only actual therapist data

2. **Check column mapping**
   - Verify all fields imported to correct columns
   - Some columns might have merged or split

3. **Fix data types**
   - Change "Gender" to Select dropdown
   - Change "Area" to Select dropdown
   - Change "Rebates" to Multi-select
   - Change "Languages" to Multi-select
   - Change "Client Types" to Multi-select

---

## 🔧 **Step 2: Add Essential Properties**

Your imported database needs these additional fields:

### **System Fields** (Add these)
```
✅ Status (Select)
   - Options: Pending Review, Verified, Published, Archived

✅ Published (Checkbox)
   - Controls if shown on website

✅ Profile URL (URL)
   - Auto-generated slug (e.g., therapair.com.au/therapist/aaron-howearth)

✅ Onboarding Token (Text)
   - For secure magic link access

✅ Token Expiry (Date)
   - When token expires

✅ Consent Status (Select)
   - Options: Not Contacted, Invited, Confirmed, Declined

✅ Last Contacted (Date)
   - Track follow-ups

✅ Admin Notes (Text - Long)
   - Internal notes only
```

### **Missing Fields from CSV** (Add if needed)
```
✅ Profile Photo (Files & media)
   - Upload therapist photos

✅ Bio (Text - Long)
   - Rich bio for website

✅ Session Type (Multi-select)
   - Options: In-person, Online, Phone, Home visits

✅ Booking Link (URL)
   - Direct booking URL

✅ Social Media (Text)
   - Instagram, LinkedIn, etc.
```

---

## 📊 **Step 3: Create Views**

Set up these views for easy management:

### **View 1: Pending Review**
- Filter: Status = "Pending Review"
- Sort: By Full Name
- Use: Initial review of all imported therapists

### **View 2: Published Therapists**
- Filter: Status = "Published" AND Published = Checked
- Sort: By Full Name
- Use: See who's live on website

### **View 3: By Area**
- Group by: Area
- Sort: By Suburb
- Use: Geographic overview

### **View 4: By Expertise**
- Filter: Has "Types of People I see"
- Use: Find therapists by specialty

### **View 5: Onboarding Pipeline**
- Filter: Status = "Pending Review" OR Status = "Verified"
- Sort: By Last Contacted
- Use: Track onboarding progress

---

## 📧 **Step 4: Plan Email Invitations**

### **Email Template for Victorian Therapists**

```
Subject: Invitation to join Therapair - Confirm your therapist profile

Hi [Full Name],

I hope this email finds you well. I'm reaching out from Therapair, a new smart therapy matching platform focused on inclusive mental health care in Australia.

We came across your profile through the Victorian Inclusive Practice directory and were impressed by your commitment to inclusive, affirming practice.

We're inviting you to be part of Therapair's initial directory. We've pre-filled your profile based on publicly available information, but we'd love for you to review and enhance it.

🔗 Your secure profile link: https://therapair.com.au/onboarding/[UNIQUE_TOKEN]

What this means for you:
✅ Free listing on our platform (no fees)
✅ Increased visibility to clients seeking inclusive practitioners
✅ Control over your profile information
✅ Option to opt-out at any time

Your profile will only be published with your explicit consent.

The link above expires in 7 days. If you have any questions, feel free to reply to this email.

Warm regards,
Tino
Therapair Team
https://therapair.com.au
```

---

## 🔐 **Step 5: Generate Onboarding Tokens**

You need to generate unique tokens for each therapist:

### **Option A: Use Notion Formula**

Add a formula column called "Onboarding Token":
```
concat("therapist_", id())
```

This creates unique IDs like: `therapist_abc123`

### **Option B: Use Script** (Recommended)

I can create a script that:
1. Reads your Notion database
2. Generates secure random tokens for each therapist
3. Updates the "Onboarding Token" field
4. Sets expiry date (7 days from now)
5. Exports email list with personalized links

---

## 📊 **Step 6: Data Cleanup Checklist**

Go through each therapist entry and check:

### **Essential Fields**
- [ ] Full Name is complete
- [ ] Email is valid format
- [ ] Profession is clear
- [ ] Suburb/Area is filled
- [ ] Client Types are selected
- [ ] Consent is marked (from CSV)

### **Fix Common Issues**
- [ ] Remove duplicate entries
- [ ] Standardize suburb names (e.g., "St Kilda" vs "St. Kilda")
- [ ] Fix email typos
- [ ] Merge multi-line entries
- [ ] Remove test/incomplete entries

### **Add New Fields**
- [ ] Set all Status to "Pending Review"
- [ ] Set Published to unchecked
- [ ] Add Profile URL slug (lowercase, hyphens)

---

## 🎯 **Step 7: Prioritize for MVP**

Start with a small group:

### **Pilot Group (10-20 therapists)**
1. Select therapists with:
   - ✅ Complete information
   - ✅ Good LGBTQ+/neurodiversity expertise
   - ✅ Mix of locations
   - ✅ Active websites

2. Manually verify:
   - Website still active?
   - Email still valid?
   - Still practicing?

3. Send first batch of invitations

4. Test the onboarding process

5. Refine before full rollout

---

## 🔄 **Workflow: From CSV to Published**

```
1. CSV Import
   ↓
2. Clean up data (Status: Imported)
   ↓
3. Admin review (Status: Pending Review)
   ↓
4. Generate tokens
   ↓
5. Send invitation emails (Consent Status: Invited)
   ↓
6. Therapist confirms/edits profile (Consent Status: Confirmed)
   ↓
7. Admin verifies changes (Status: Verified)
   ↓
8. Publish to website (Status: Published, Published: ✓)
```

---

## 🛠️ **Tools You Need**

### **1. Token Generator Script**
I can create a Node.js script that:
- Connects to your Notion database
- Generates secure tokens
- Updates records
- Creates email list

### **2. Email Sending Script**
Using NodeMailer to:
- Send personalized invitations
- Track email opens
- Log responses

### **3. Profile URL Generator**
Auto-generate slugs like:
- aaron-howearth
- abby-draper
- etc.

---

## 📋 **Sample Notion Properties Setup**

Here's what your final database should look like:

```
Core Identity:
├── Full Name (Title)
├── Email (Email)
├── Gender (Select)
└── Profile Photo (Files)

Professional:
├── Profession (Select)
├── Business Name (Text)
├── Website (URL)
├── Professional Title (Text)
└── Complaint Body (Select: AHPRA, APS, OTA)

Location:
├── Suburbs (Multi-select)
├── Area (Select)
└── Session Type (Multi-select)

Services:
├── Rebates (Multi-select: Medicare, NDIS)
├── Languages (Multi-select)
├── Client Types (Multi-select)
├── Specializations (Multi-select)
└── Special Services (Multi-select)

Content:
├── Bio (Text - Long)
├── Lived Experience (Multi-select)
├── Accessibility (Multi-select)
└── Additional Info (Text)

System:
├── Status (Select)
├── Published (Checkbox)
├── Profile URL (URL)
├── Onboarding Token (Text)
├── Token Expiry (Date)
├── Consent Status (Select)
├── Last Contacted (Date)
├── Created Date (Created time)
└── Last Updated (Last edited time)
```

---

## ✅ **Immediate Action Items**

### **Today:**
1. [ ] Clean up header rows
2. [ ] Add "Status" column (set all to "Pending Review")
3. [ ] Add "Published" checkbox (set all to unchecked)
4. [ ] Create "Pending Review" view

### **This Week:**
1. [ ] Review all entries for completeness
2. [ ] Fix data type issues (Select vs Text)
3. [ ] Add missing properties
4. [ ] Create views for different workflows

### **Next Week:**
1. [ ] Generate onboarding tokens
2. [ ] Select pilot group (10-20 therapists)
3. [ ] Send first invitation batch
4. [ ] Monitor responses

---

## 🚀 **What I Can Help With**

I can create scripts for:

1. **Token Generator** - Generate secure tokens for all therapists
2. **Email Sender** - Send personalized invitations
3. **Profile URL Generator** - Create clean URLs
4. **Data Validator** - Check for missing/invalid data
5. **Notion API Integration** - Update records programmatically

**Want me to create any of these scripts?**

---

## 📞 **Next Steps**

1. Clean up the imported data
2. Add the system fields (Status, Published, etc.)
3. Let me know when ready, and I'll create:
   - Token generation script
   - Email invitation system
   - Profile management tools

The goal is to have 10-20 verified, published therapists within 2-3 weeks! 🎉
