# Column Reordering Guide - Victorian Therapists Database

## 📋 **Recommended Column Order (Best Practice)**

After running the cleanup script, manually reorder your columns in Notion to follow this logical structure:

---

### **🔷 Section 1: Identity & Contact (Columns 1-6)**

```
1.  Name (Title) ← Keep as-is
2.  First Name
3.  Last Name ← NEW
4.  Email Address
5.  Phone ← NEW (extracted from contact details)
6.  Gender
```

---

### **🔷 Section 2: Professional (Columns 7-12)**

```
7.  Profession/Key Qualification/s
8.  Business Name
9.  Website
10. Social Media ← NEW (extracted from contact details)
11. My relevant body for handling complaints/queries
12. Timestamp (original submission date)
```

---

### **🔷 Section 3: Location & Service Delivery (Columns 13-18)**

```
13. Suburb/s of practice
14. Region ← NEW (standardized from Area)
15. Area predominantly work in (can hide later)
16. What accessibility options does your practice have?
17. Languages spoken other than English
18. Do you offer rebates or other funding models?
```

---

### **🔷 Section 4: Expertise & Services (Columns 19-24)**

```
19. Types of People I see and am trained in working with...
20. If there are groups who you don't have specific training in...
21. Clients I don't work with
22. Special services I deliver
23. Aspects of MY OWN lived experience
24. Anything else brief that you'd like clients to know
```

---

### **🔷 Section 5: Compliance & Consent (Columns 25-27)**

```
25. I confirm that I have sufficient professional indemnity insurance... 
26. I confirm that I am the practitioner whose details are given above...
27. Please consider linking to https://www.vicinclusivepractitioners.com/...
```

---

### **🔷 Section 6: System & Admin (Columns 28-36)**

```
28. Status ← NEW (Pending Review, Verified, Published, Archived)
29. Published ← NEW (Checkbox - controls website visibility)
30. Profile URL ← NEW (e.g., /therapist/sarah-johnson)
31. Onboarding Token ← NEW (for magic link access)
32. Token Expiry ← NEW (30 days from import)
33. Last Contacted ← NEW (track follow-ups)
34. Admin Notes ← NEW (internal use only)
35. Import Date ← NEW (when CSV was imported)
36. [Hide old "First Name" and "Surname" if keeping the new ones]
```

---

## 🎨 **How to Reorder in Notion**

1. Open your "Victorian Inclusive Therapists (Demo)" database
2. Click on any column header
3. Click the **⋮⋮** (six dots) icon
4. Drag columns to reorder them
5. Follow the order listed above

---

## ✂️ **Columns to Consider Hiding**

Once cleanup is complete, you can **hide** (not delete) these columns:

- ❌ **"First Name"** (old) - if you're using the NEW "First Name"
- ❌ **"Surname"** (old) - replaced by "Last Name"
- ❌ **"Other contact details"** - split into Phone & Social Media
- ❌ **"Area predominantly work in"** (old) - replaced by "Region"
- ❌ **"Please consider linking..."** - not needed for active use

To hide: Click column header → **Hide in view**

---

## 🏷️ **Columns to Rename (Optional)**

For even cleaner structure, consider these renames:

| Old Name | New Name |
|----------|----------|
| "Profession/Key Qualification/s" | "Profession & Qualifications" |
| "My relevant body for handling complaints/queries" | "Regulatory Body" |
| "Suburb/s of practice (if online only, write ONLINE)" | "Suburbs" |
| "Area predominantly work in" | "Region" (already added as new) |
| "Do you offer rebates or other funding models?" | "Rebates & Funding" |
| "Languages spoken other than English" | "Languages" |
| "Types of People I see and am trained in working with..." | "Primary Expertise" |
| "If there are groups who you don't have specific training in..." | "Also Works With" |
| "Clients I don't work with" | "Does Not Work With" |
| "Special services I deliver" | "Special Services" |
| "Aspects of MY OWN lived experience" | "Lived Experience" |
| "Anything else brief..." | "Additional Information" |
| "What accessibility options..." | "Accessibility" |
| "I confirm that I have sufficient..." | "Insurance Confirmed" |
| "I confirm that I am the practitioner..." | "Consent to List" |
| "Please consider linking..." | "Website Link Requested" |

To rename: Click column header → **Edit property** → Change name

---

## 📊 **Create Organized Views**

After reordering, create these filtered views:

### **View 1: Active Pipeline**
- Filter: Status = "Pending Review" OR "Verified"
- Show: Identity, Contact, Professional, Status columns
- Sort: By Last Contacted (oldest first)

### **View 2: Published Therapists**
- Filter: Status = "Published" AND Published = ✓
- Show: All columns except System/Admin
- Sort: By Name

### **View 3: By Region**
- Group by: Region
- Filter: Status ≠ "Archived"
- Show: Identity, Location, Professional

### **View 4: Onboarding Required**
- Filter: Status = "Pending Review" AND Onboarding Token is not empty
- Show: Name, Email, Onboarding Token, Token Expiry
- Use: For sending invitation emails

---

## ✅ **Final Checklist**

After cleanup script runs:

- [ ] Review all entries for accuracy
- [ ] Reorder columns following the guide above
- [ ] Rename long column names
- [ ] Hide unnecessary columns
- [ ] Create filtered views
- [ ] Add column descriptions (hover tip)
- [ ] Set up database templates (optional)
- [ ] Export backup to CSV

---

## 🎯 **Result**

Your database will go from this:
```
[Messy alphabetical order with long names]
```

To this:
```
Section 1: Identity & Contact (Who they are)
Section 2: Professional (What they do)
Section 3: Location (Where they work)
Section 4: Expertise (Who they help)
Section 5: Compliance (Legal stuff)
Section 6: System (Admin/backend)
```

**Clean, logical, and professional!** ✨
