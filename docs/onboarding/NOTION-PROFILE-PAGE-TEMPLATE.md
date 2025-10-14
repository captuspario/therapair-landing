# Notion Profile Page Template - One-Click Therapist View

## 🎯 **Objective**

Create a beautiful, professional profile view that displays when you click any therapist entry in Notion - perfect for:
- Admin review and verification
- Quick therapist overview
- Client-facing preview
- Team collaboration

---

## 🎨 **How to Set Up Profile Page View**

### **Step 1: Configure Database Page Layout**

1. Open your Victorian Therapists database
2. Click **"••• "** (database menu) → **"Customize page"**
3. Enable **"Show as page"** for all entries
4. This allows each row to open as a full page when clicked

### **Step 2: Create Page Template**

1. Open any therapist entry (click on their name)
2. You'll see a blank page - this is where we build the template
3. Click **"•••"** → **"New template"**
4. Name it: **"Therapist Profile Template"**
5. Set as default template for all new entries

---

## 📄 **Recommended Profile Page Structure**

### **Layout Design:**

```
┌─────────────────────────────────────────────────────────────────┐
│                    [Profile Photo - Cover Image]                 │
│                                                                   │
│  👤 [Icon]  Full Name                                            │
│             Preferred Name | Profession | Location               │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│  📋 QUICK OVERVIEW                                               │
│  ┌─────────────┬─────────────┬─────────────┬─────────────┐      │
│  │ ✅ Status   │ 📍 Region   │ 📞 Contact  │ 🌐 Online   │      │
│  │ Verified    │ Inner North │ Available   │ Sessions    │      │
│  └─────────────┴─────────────┴─────────────┴─────────────┘      │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│  👤 PERSONAL & CONTACT                                           │
│  • Full Name: [Auto-filled]                                      │
│  • Preferred Name: [Auto-filled]                                 │
│  • Email: [Auto-filled]                                          │
│  • Phone: [Auto-filled]                                          │
│  • Gender: [Auto-filled]                                         │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│  💼 PROFESSIONAL                                                 │
│  • Profession: [Auto-filled]                                     │
│  • Business: [Auto-filled]                                       │
│  • Website: [Auto-filled]                                        │
│  • Regulatory Body: [Auto-filled]                                │
│  • Registration Number: [Auto-filled]                            │
│  • Modalities: [Auto-filled badges]                              │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│  📱 SOCIAL MEDIA                                                 │
│  • Instagram: [Auto-filled]                                      │
│  • Facebook: [Auto-filled]                                       │
│  • Twitter/X: [Auto-filled]                                      │
│  • LinkedIn: [Auto-filled]                                       │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│  📍 LOCATION & SERVICE                                           │
│  • State: [Auto-filled]                                          │
│  • Region: [Auto-filled]                                         │
│  • Suburbs: [Auto-filled]                                        │
│  • Service Type: [Auto-filled badges]                            │
│  • Accessibility: [Auto-filled badges]                           │
│  • Languages: [Auto-filled]                                      │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│  🎯 EXPERTISE & SPECIALIZATIONS                                  │
│  • Primary Expertise: [Auto-filled badges]                       │
│  • Client Age Groups: [Auto-filled badges]                       │
│  • Modalities: [Auto-filled badges]                              │
│  • Special Services: [Auto-filled]                               │
│  • Lived Experience: [Auto-filled badges]                        │
│  • Also Works With: [Auto-filled]                                │
│  • Does Not Work With: [Auto-filled]                             │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│  💰 PRICING & AVAILABILITY                                       │
│  • Session Fee: [Auto-filled]                                    │
│  • Bulk Billing: [Auto-filled]                                   │
│  • Rebates: [Auto-filled badges]                                 │
│  • Accepting New Clients: [Auto-filled]                          │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│  📝 BIO & ADDITIONAL INFO                                        │
│  [Rich text content area]                                        │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│  🔧 ADMIN & SYSTEM                                               │
│  • Status: [Auto-filled]                                         │
│  • Published: [Auto-filled]                                      │
│  • Profile URL: [Auto-filled]                                    │
│  • Onboarding Token: [Auto-filled]                               │
│  • Token Expiry: [Auto-filled]                                   │
│  • Last Contacted: [Auto-filled]                                 │
│  • Admin Notes: [Editable text area]                             │
└─────────────────────────────────────────────────────────────────┘
```

---

## 🛠️ **How to Build This Template**

### **Step 1: Open Template Editor**

1. Click any therapist name to open their page
2. Click **"•••"** → **"New template"**
3. Name: "Therapist Profile Template"
4. Start building...

### **Step 2: Add Heading Sections**

Type these in order:

```
## 📋 Quick Overview

## 👤 Personal & Contact

## 💼 Professional

## 📱 Social Media

## 📍 Location & Service

## 🎯 Expertise & Specializations

## 💰 Pricing & Availability

## 📝 Bio & Additional Information

## 🔧 Admin & System
```

### **Step 3: Add Property References**

Under each section, type `@` and select the property name:

**Example for "Personal & Contact":**
```
## 👤 Personal & Contact
@Fullname
@Preferred Name
@Email Address
@Phone
@Gender
```

Notion will automatically display the values from the database!

### **Step 4: Add Callout Boxes**

For important info, use callouts:

Type: `/callout` then customize:

```
💡 Quick Links
Website: @Website
Profile URL: @Profile URL
Instagram: @Instagram
```

### **Step 5: Add Dividers**

Between sections, type: `/divider`

This creates visual separation

---

## 🎨 **Advanced: Use Columns for Better Layout**

### **Create 2-Column Layout:**

Type: `/columns` then add properties side-by-side:

```
┌─────────────────────┬─────────────────────┐
│  Left Column        │  Right Column       │
│  @Email Address     │  @Phone             │
│  @Business Name     │  @Website           │
│  @State             │  @Region            │
└─────────────────────┴─────────────────────┘
```

---

## 🎯 **Optimized Template (Step-by-Step Build)**

### **Section 1: Header (Top of Page)**

```
[Add cover image placeholder]
[Add icon - use 👤 or therapist emoji]

## @Fullname

Callout (💡 Blue):
@Preferred Name | @Primary Profession | @Region, @State
```

### **Section 2: Status Bar**

```
Callout (✅ Green):
Status: @Status | Published: @Published | Accepting: @Accepting New Clients
```

### **Section 3: Contact Grid (2 columns)**

```
Column 1:
📧 Email
@Email Address

📞 Phone  
@Phone

Column 2:
🌐 Website
@Website

📍 Location
@Region, @State
```

### **Section 4: Professional**

```
## 💼 Professional Information

Toggle (collapsed by default):
@Primary Profession
@Profession/Key Qualification/s
@Business Name
@My relevant body for handling complaints/queries
@Registration Number
@Modalities
```

### **Section 5: Expertise**

```
## 🎯 Expertise & Specializations

@Client Age Groups
@Primary Expertise (Types of People I see...)
@Lived Experience
@Special Services
```

### **Section 6: Social Media (Linked Database)**

```
## 📱 Social Media

Create a simple table:
| Platform | Handle/Link |
|----------|-------------|
| Instagram | @Instagram |
| Facebook | @Facebook |
| Twitter/X | @Twitter/X |
| LinkedIn | @LinkedIn |
```

### **Section 7: Location Details**

```
## 📍 Location & Service Details

Toggle:
@State
@Region  
@Suburb/s of practice
@Postcode
@Service Type
@What accessibility options does your practice have?
@Languages spoken other than English
```

### **Section 8: Pricing**

```
## 💰 Pricing & Rebates

Callout (💵 Yellow):
Session Fee: @Session Fee
Bulk Billing: @Bulk Billing
Rebates: @Do you offer rebates or other funding models?
```

### **Section 9: Bio**

```
## 📝 About This Therapist

@Anything else brief that you'd like clients to know...

[Add rich text area for bio content]
```

### **Section 10: Admin Section**

```
## 🔧 Admin Only

Toggle (collapsed):
@Status
@Published
@Profile URL
@Onboarding Token
@Token Expiry
@Last Contacted
@Import Date
@Admin Notes
```

---

## 💡 **Pro Tips for Better Profile Pages**

### **1. Use Synced Blocks**

Create a "Profile Card Preview" synced block that shows:
- Profile photo
- Name
- Key expertise (3-5 tags)
- Accepting new clients status

Sync this block to use in other pages/dashboards

### **2. Add Quick Action Buttons**

Use button blocks:
```
[Send Onboarding Email] → Link to email template
[View Website] → @Website
[Mark as Verified] → Changes status
```

### **3. Use Toggle Lists**

Collapse long sections by default:
- Full qualifications
- Detailed expertise lists
- Admin notes

### **4. Color Code Sections**

Use callout backgrounds:
- 🟦 Blue = Contact info
- 🟩 Green = Verified/Published status
- 🟨 Yellow = Pending review
- 🟥 Red = Issues/missing data

### **5. Add Linked Database Views**

If you have other databases (form submissions, etc.):
- Add linked database showing related entries
- Filter by email match
- See their journey from form → profile

---

## 🚀 **Auto-Populate Using Formulas**

Some fields can be auto-generated:

### **Profile URL Formula:**
```
concat("/therapist/", 
  replaceAll(
    replaceAll(
      lower(prop("Fullname")), 
      " ", "-"
    ), 
    "[^a-z0-9-]", ""
  )
)
```

### **Display Name Formula:**
```
if(prop("Preferred Name"), 
  prop("Preferred Name"), 
  prop("First Name")
)
```

### **Status Badge Formula:**
```
if(prop("Published"), 
  "🟢 Live", 
  if(prop("Status") == "Verified", 
    "🟡 Ready to Publish", 
    "⚪ Pending"
  )
)
```

---

## 📱 **Mobile-Optimized View**

The page template works great on mobile too:
- Clean, vertical stack
- Touch-friendly buttons
- Easy to review on the go
- Perfect for quick approvals

---

## 🎯 **Example: What Clicking a Therapist Shows**

```
When you click "Sarah Johnson" in the database table:

┌──────────────────────────────────────────────────────┐
│            [Cover Image - Calming Background]        │
│                                                      │
│  👤  Dr. Sarah Johnson                              │
│      Sarah | Clinical Psychologist | Inner North    │
└──────────────────────────────────────────────────────┘

💡 Status: ✅ Verified | Published: ✓ | Accepting: Yes

┌──────────────────┬──────────────────┐
│ 📧 Email         │ 📞 Phone         │
│ sarah@email.com  │ 0412 345 678     │
│                  │                  │
│ 🌐 Website       │ 📍 Location      │
│ sarahcounselling │ Carlton, VIC     │
└──────────────────┴──────────────────┘

💼 Professional
  Profession: Clinical Psychologist
  Business: Sarah Johnson Psychology
  Regulatory: AHPRA
  Registration: PSY0012345
  Modalities: CBT, ACT, EMDR

📱 Social Media
  📷 @sarahjohnsonpsych
  📘 facebook.com/sarahjohnsonpsychology

🎯 Expertise
  • LGBTQ+ Affirming Care
  • Trauma-Informed Practice
  • Anxiety & Depression
  • Couples Therapy
  
  Age Groups: Adults, Couples
  Lived Experience: LGBTQ+, Neurodiversity

📍 Service Details
  In-Person: Carlton, Fitzroy
  Online: Australia-wide
  Languages: English, Mandarin
  Accessibility: Wheelchair access, Online sessions

💰 Pricing
  Session Fee: $180
  Medicare Rebate: ✓
  NDIS: ✓
  Bulk Billing: Available for concession

📝 Bio
  [Rich text area for therapist bio]

🔧 Admin
  Token: therapist_abc123...
  Expiry: 2025-11-14
  Last Contacted: 2025-10-10
  Notes: [Admin comments]
```

---

## 🛠️ **Step-by-Step Template Build**

### **Copy This Into Your Template:**

```
---
[Add cover image - use calming colors]
---

# @Fullname

> **@Preferred Name** | **@Primary Profession** | **@Region**, **@State**

---

## 💡 Quick Status

> ✅ **Status:** @Status
> 🌐 **Published:** @Published  
> 👥 **Accepting:** @Accepting New Clients

---

## 👤 Personal & Contact

| Field | Value |
|-------|-------|
| **Full Name** | @Fullname |
| **Preferred Name** | @Preferred Name |
| **Email** | @Email Address |
| **Phone** | @Phone |
| **Gender** | @Gender |

---

## 💼 Professional Information

**Profession:** @Primary Profession

**Business:** @Business Name

**Website:** @Website

**Registration:**
- Body: @My relevant body for handling complaints/queries
- Number: @Registration Number

**Modalities:** @Modalities

**Qualifications:** @Profession/Key Qualification/s

---

## 📱 Social Media

| Platform | Link |
|----------|------|
| 📷 Instagram | @Instagram |
| 📘 Facebook | @Facebook |
| 🐦 Twitter/X | @Twitter/X |
| 💼 LinkedIn | @LinkedIn |

---

## 📍 Location & Service

**Location:**
- **State:** @State
- **Region:** @Region
- **Suburbs:** @Suburb/s of practice
- **Postcode:** @Postcode

**Service Delivery:**
- **Types:** @Service Type
- **Accessibility:** @What accessibility options does your practice have?
- **Languages:** @Languages spoken other than English

---

## 🎯 Expertise & Specializations

**Client Age Groups:** @Client Age Groups

**Primary Expertise (Trained In):**
@Types of People I see and am trained in working with...

**Also Works With (Some Knowledge):**
@If there are groups who you don't have specific training in...

**Does Not Work With:**
@Clients I don't work with

**Special Services:**
@Special services I deliver

**Lived Experience:**
@Aspects of MY OWN lived experience

---

## 💰 Pricing & Rebates

**Session Fee:** @Session Fee

**Bulk Billing:** @Bulk Billing

**Rebates & Funding:**
@Do you offer rebates or other funding models?

---

## 📝 Bio & Additional Information

@Anything else brief that you'd like clients to know...

[Add rich text area for expanded bio]

---

## 🔧 Admin & System

> 🔒 **Internal Use Only**

**Onboarding:**
- Token: @Onboarding Token
- Expiry: @Token Expiry
- Last Contacted: @Last Contacted

**Publishing:**
- Status: @Status
- Published: @Published
- Profile URL: @Profile URL

**Compliance:**
- Insurance: @I confirm that I have sufficient professional indemnity insurance...
- Consent: @I confirm that I am the practitioner whose details are given above...

**Tracking:**
- Import Date: @Import Date
- Original Timestamp: @Timestamp

**Notes:**
@Admin Notes

---
```

---

## 🎨 **Visual Enhancements**

### **1. Add Emoji Icons**

At the start of each section heading:
- 👤 Personal
- 💼 Professional
- 📱 Social Media
- 📍 Location
- 🎯 Expertise
- 💰 Pricing
- 📝 Bio
- 🔧 Admin

### **2. Use Callout Boxes**

For important info, type `/callout`:

```
💡 Quick Info (Blue background)
✅ Status badges (Green background)
⚠️ Missing data warnings (Yellow background)
❌ Issues (Red background)
```

### **3. Add Buttons**

Type `/button` to add action buttons:
- "📧 Send Onboarding Email"
- "✅ Mark as Verified"
- "🌐 View Website"
- "📋 Copy Profile URL"

### **4. Use Toggles**

Collapse long sections:
```
/toggle "Full Qualifications & Training"
  @Profession/Key Qualification/s
  [Additional details]
```

---

## 🔄 **Apply Template to All Entries**

### **Option 1: Set as Default Template**

1. In template editor, click **"•••"**
2. Select **"Set as default"**
3. All new entries use this template
4. Existing entries keep their blank pages

### **Option 2: Bulk Apply to Existing**

Unfortunately, Notion doesn't have bulk template application.

**Workaround:**
1. Select multiple therapists in table view
2. Duplicate them (they inherit template)
3. Delete originals
4. Or manually copy template to important entries

---

## 💡 **Smart Features to Add**

### **1. Verification Checklist**

Add a checklist in the Admin section:
```
Verification Checklist:
- [ ] Email verified
- [ ] Website checked
- [ ] AHPRA registration confirmed
- [ ] Profile photo received
- [ ] Bio reviewed
- [ ] Social media verified
- [ ] Ready to publish
```

### **2. Quick Stats**

Add a table at the top:
```
| Metric | Value |
|--------|-------|
| Profile Completeness | 85% |
| Data Quality | High |
| Social Media | ✓ |
| Ready to Publish | Yes |
```

### **3. Related Content**

Add linked databases:
- Form submissions (if they submitted interest)
- Email logs (communication history)
- Profile edits (version history)

---

## 📊 **Benefits of This Approach**

### **For Admin:**
✅ **One-click access** to full therapist profile  
✅ **All data visible** in organized layout  
✅ **Easy verification** - checkboxes and status  
✅ **Quick decisions** - Approve/reject at a glance  

### **For Team:**
✅ **Consistent format** - Everyone sees same layout  
✅ **Professional presentation** - Looks polished  
✅ **Mobile-friendly** - Review anywhere  
✅ **Collaborative** - Add comments inline  

### **For Preview:**
✅ **Client-facing view** - How it will look on website  
✅ **Missing data** - Easy to spot gaps  
✅ **Social proof** - See social media presence  
✅ **Complete picture** - All info in one place  

---

## 🎯 **After Template is Set Up:**

1. **Click any therapist** → Opens beautiful profile page
2. **Review at a glance** → All info organized
3. **Make decisions** → Verify, publish, or request changes
4. **Collaborate** → Add comments, assign tasks
5. **Export** → Can share or print if needed

---

## 📋 **Recommended Next Steps:**

1. [ ] Create the template using the structure above
2. [ ] Set as default for all entries
3. [ ] Test with 2-3 therapist entries
4. [ ] Refine based on what you need to see
5. [ ] Apply to all 202 therapists
6. [ ] Use for verification workflow

---

## 🚀 **Result:**

With this template, you'll have:
- ✨ **Professional profile pages** for each therapist
- ✨ **One-click access** to complete information
- ✨ **Beautiful presentation** for team review
- ✨ **Client preview** - See how it will look
- ✨ **Easy verification** - All data at a glance

**Time to set up:** ~15-20 minutes  
**Time saved per therapist review:** ~5 minutes  
**Total time saved (202 therapists):** ~16 hours! 🎉

---

Would you like me to create a script that generates this template programmatically via the Notion API?
