# Notion Database Strategy: Two Databases vs One

## 🎯 **Quick Answer: Use TWO Separate Databases**

**Recommendation:** Create **separate** Notion databases for:
1. **Form Submissions Database** (existing) - For marketing/interest tracking
2. **Therapist Directory Database** (new) - For actual therapist profiles

---

## 🤔 **Why Separate Databases?**

### **Different Purposes**
| Form Submissions | Therapist Directory |
|------------------|---------------------|
| **Purpose**: Track interest & leads | **Purpose**: Actual therapist profiles |
| **Status**: New → Contacted → Converted | **Status**: Pending → Verified → Published |
| **Data**: Minimal (email, interests) | **Data**: Comprehensive (bio, specialties, photos) |
| **Workflow**: Sales/marketing pipeline | **Workflow**: Onboarding & verification |
| **Audience**: Mixed (individuals, therapists, orgs) | **Audience**: Therapists only |
| **Updates**: Rarely after initial contact | **Updates**: Frequent (profile edits, status changes) |

### **Different Data Structures**

**Form Submissions Database** collects:
- ✅ Email
- ✅ Audience Type (Individual, Therapist, Org, Supporter)
- ✅ Therapy Interests (for individuals)
- ✅ Professional Title (basic)
- ✅ Specializations (text field)
- ✅ Status (New, Contacted, Converted)

**Therapist Directory Database** needs:
- ✅ Full Name
- ✅ Profession/Key Qualification
- ✅ Business Name
- ✅ Website
- ✅ Email
- ✅ Contact Details
- ✅ Complaint Body (AHPRA, APS, etc.)
- ✅ Gender (Woman/Female, Man/Male, Nonbinary, Other)
- ✅ Suburb/s of practice
- ✅ Area (CBD, Inner North, etc.)
- ✅ Rebates/Funding (Medicare, NDIS, etc.)
- ✅ Languages spoken
- ✅ Types of People I see (expertise)
- ✅ Willing to work with (some knowledge)
- ✅ Clients I don't work with
- ✅ Special services
- ✅ Lived experience
- ✅ Accessibility options
- ✅ Additional information
- ✅ Insurance confirmed
- ✅ Consent confirmed
- ✅ Photo/Image URL
- ✅ Bio (rich text)
- ✅ Verification Status (Pending, Verified, Rejected)
- ✅ Published Status (Draft, Published, Archived)
- ✅ Profile URL (auto-generated)

---

## 📊 **Field Comparison**

### **Form Submissions Fields (Current)**
```
Core:
- Name (Title) - Auto-generated
- Email (Email)
- Audience Type (Select)
- Submission Date (Date)
- Status (Status)
- Email Preferences (Multi-select)

Therapist Specific:
- Full Name (Text)
- Professional Title (Text)
- Organisation (Text)
- Specialisations (Long text)
- Verification Status (Select)
- Onboarding Stage (Select)
```

### **Victorian Therapists CSV Fields**
```
Personal:
- First Name
- Surname
- Full Name
- Gender
- Lived Experience

Business:
- Business Name
- Website
- Email Address
- Other Contact Details
- Profession/Key Qualification

Location:
- Suburb/s of practice
- Area predominantly work in

Services:
- Rebates/Funding Models
- Languages spoken
- Types of People I see (expertise)
- Willing to work with
- Clients I don't work with
- Special services

Accessibility:
- Accessibility options

Compliance:
- Complaint Body (AHPRA, etc.)
- Insurance Confirmed
- Consent Confirmed
```

### **Missing Fields in Form Submissions Database**
- ❌ Gender
- ❌ Business Name
- ❌ Website
- ❌ Detailed Contact Info
- ❌ Complaint Body
- ❌ Suburb/Location
- ❌ Area
- ❌ Rebates/Funding
- ❌ Languages
- ❌ Detailed Client Types (expertise)
- ❌ Willing to work with
- ❌ Clients excluded
- ❌ Special services
- ❌ Lived experience
- ❌ Accessibility options
- ❌ Insurance/Consent confirmations
- ❌ Photo/Image

---

## ✅ **Recommended Approach: Two-Database Strategy**

### **Database 1: Form Submissions (Existing)**
**Purpose:** Marketing & Lead Tracking

**Keep it for:**
- Initial interest tracking
- Marketing pipeline
- Email campaigns
- Early conversations
- Converting interest → applications

**Workflow:**
```
New Submission → Contacted → Interested → Invited to Onboard → Converted
```

---

### **Database 2: Therapist Directory (New)**
**Purpose:** Actual Therapist Profiles

**Use it for:**
- Complete therapist profiles
- Victorian therapists import
- Onboarding process
- Verification workflow
- Published directory

**Workflow:**
```
CSV Import → Pending Review → Verification → Published → Active Directory
```

---

## 🔄 **How They Work Together**

### **Scenario 1: New Therapist via Form**
```
1. Therapist fills form → Saved to "Form Submissions" database
2. Status: New → Contacted
3. You invite them to full onboarding
4. They complete full profile → Create entry in "Therapist Directory" database
5. Link the two entries via Relation property
6. Form Submission status: Converted
7. Therapist Directory status: Pending → Verified → Published
```

### **Scenario 2: Victorian Therapist Import**
```
1. Import CSV directly to "Therapist Directory" database
2. Status: Pending Review
3. Send invitation email with magic link
4. They edit/confirm their profile
5. Admin verifies
6. Status: Published
7. Profile appears on website
```

---

## 🏗️ **Setup: Therapist Directory Database**

### **Core Properties**

```
┌─────────────────────────┬────────────┬──────────────────────────┐
│ Property Name           │ Type       │ Options/Description      │
├─────────────────────────┼────────────┼──────────────────────────┤
│ Full Name               │ Title      │ Auto from CSV            │
│ Email                   │ Email      │ Primary contact          │
│ Status                  │ Select     │ Pending, Verified,       │
│                         │            │ Published, Archived      │
│ Published               │ Checkbox   │ Show on website?         │
│ Profile URL             │ URL        │ Auto-generated           │
│ Photo                   │ Files      │ Profile image            │
│ Last Updated            │ Date       │ Auto                     │
│ Source                  │ Select     │ CSV Import, Form,        │
│                         │            │ Direct Onboard           │
└─────────────────────────┴────────────┴──────────────────────────┘
```

### **Professional Info**
```
- Profession (Text)
- Business Name (Text)
- Website (URL)
- Professional Title (Text)
- Complaint Body (Select: AHPRA, APS, OTA, etc.)
- Insurance Confirmed (Checkbox)
- Consent Confirmed (Checkbox)
```

### **Location & Accessibility**
```
- Suburbs (Multi-select)
- Area (Select: CBD, Inner North, Online, etc.)
- Languages (Multi-select)
- Accessibility Options (Multi-select)
- Online Sessions (Checkbox)
```

### **Services & Expertise**
```
- Rebates (Multi-select: Medicare, NDIS, WorkCover)
- Client Types (Multi-select: LGBTQ+, Neurodiversity, etc.)
- Specializations (Multi-select)
- Special Services (Multi-select)
- Willing to Work With (Text)
- Does Not Work With (Text)
```

### **Personal**
```
- Gender (Select)
- Lived Experience (Multi-select)
- Bio (Long text)
- Additional Info (Long text)
```

### **System Fields**
```
- Onboarding Token (Text) - Secure magic link
- Token Expiry (Date)
- Last Login (Date)
- Verification Date (Date)
- Verified By (Person)
- Notes (Long text) - Admin only
```

---

## 💡 **Best Practice: Link the Databases**

### **Add Relation Property**

In **Form Submissions** database:
- Add Relation property: "Therapist Profile"
- Links to: Therapist Directory database

In **Therapist Directory** database:
- Add Relation property: "Form Submission"
- Links to: Form Submissions database

**Benefits:**
- Track original inquiry
- See full journey
- Maintain audit trail
- Easy cross-reference

---

## 🚀 **Implementation Steps**

### **Phase 1: Keep Current Setup (Week 1)**
1. ✅ Use existing "Form Submissions" database for leads
2. ✅ Track interest and initial contacts
3. ✅ Build email list

### **Phase 2: Create Therapist Directory (Week 2)**
1. Create new "Therapist Directory" database
2. Set up all properties (see structure above)
3. Import Victorian therapists CSV
4. Add relation between databases

### **Phase 3: Onboarding Flow (Week 3-4)**
1. When therapist shows interest in Form Submissions
2. Send onboarding invitation
3. Create profile in Therapist Directory
4. Link the two entries
5. Track through verification

### **Phase 4: Go Live (Week 5+)**
1. Published therapists appear on website
2. Form submissions continue for leads
3. Two systems work together seamlessly

---

## 📋 **CSV Import Template**

For importing Victorian therapists to Therapist Directory:

```csv
Full Name,Email,Profession,Business Name,Website,Gender,Suburbs,Area,Rebates,Languages,Client Types,Specializations,Lived Experience,Accessibility,Status,Published
Aaron Howearth,info@example.com,Psychologist,Howearth Psychology,www.example.com,Man/Male,"St Kilda, Online",Melbourne CBD,"Medicare, NDIS",,"LGBTQ+, Transgender, Neurodiversity","Couples, WPATH assessments","LGBTQ+, Nonbinary","Wheelchair accessible, Online",Pending,FALSE
```

---

## ✅ **Summary: Why Two Databases**

| Aspect | Two Databases ✅ | One Database ❌ |
|--------|-----------------|-----------------|
| **Clarity** | Clear separation | Mixed purposes |
| **Workflow** | Distinct pipelines | Confusing statuses |
| **Data** | Right fields for each | Too many optional fields |
| **Views** | Clean filtering | Complex filters needed |
| **Permissions** | Can share therapist directory publicly | Can't share (has private leads) |
| **CSV Import** | Easy direct import | Need to map mixed fields |
| **Scale** | Easy to manage | Gets messy |
| **Future** | Can export therapists separately | Hard to separate later |

---

## 🎯 **Final Recommendation**

**Create TWO databases:**

1. **Form Submissions** (existing)
   - Keep for marketing leads
   - Simple, focused on conversion
   - 10-15 fields max

2. **Therapist Directory** (new)
   - Full therapist profiles
   - 30-40 fields
   - CSV import ready
   - Public-facing data
   - Verification workflow

**Link them together** with Relation properties for complete tracking.

This gives you:
- ✅ Clean data structure
- ✅ Clear workflows
- ✅ Easy CSV import
- ✅ Scalable for future
- ✅ Can share therapist directory safely
- ✅ Marketing and operations separated

---

## 📞 **Next Steps**

1. ✅ Keep using Form Submissions database as-is
2. ✅ Create new Therapist Directory database
3. ✅ Import Victorian therapists CSV
4. ✅ Link databases with Relations
5. ✅ Set up verification workflow

Would you like me to create the detailed setup guide for the Therapist Directory database?
