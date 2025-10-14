# One-Click Profile View - Quick Setup Guide

## 🎯 **Goal: Click Any Therapist → See Beautiful Profile Page**

---

## ⚡ **Quick Setup (15 minutes)**

### **Step 1: Open Any Therapist (2 min)**
1. Go to your "Victorian Inclusive Therapists (Demo)" database
2. Click on any therapist name (e.g., "Sarah Johnson")
3. You'll see a mostly blank page

### **Step 2: Create Template (10 min)**
1. Click **"•••"** (top right) → **"New template"**
2. Name it: **"Therapist Profile View"**
3. Build the template (see below)
4. Click **"•••"** → **"Set as default template"**

### **Step 3: Test (3 min)**
1. Close the page
2. Create a new test entry
3. It should automatically use your template
4. Click any existing therapist to see their data

---

## 📄 **Simple Template (Copy & Paste)**

Copy this structure into your template:

```
# @Fullname

> @Preferred Name | @Primary Profession | @Region

---

### 💡 Quick Status
@Status | Published: @Published | Clients: @Accepting New Clients

---

### 👤 Contact
- Email: @Email Address
- Phone: @Phone  
- Website: @Website

### 📱 Social
- Instagram: @Instagram
- Facebook: @Facebook

### 📍 Location
- @State | @Region | @Postcode
- Service: @Service Type

### 🎯 Expertise
@Client Age Groups
@Modalities
@Types of People I see and am trained in working with...

### 💰 Pricing
- Fee: @Session Fee
- Rebates: @Do you offer rebates or other funding models?

### 📝 Bio
@Anything else brief that you'd like clients to know...

### 🔧 Admin
- Token: @Onboarding Token
- Expires: @Token Expiry
- Notes: @Admin Notes
```

---

## 🎨 **How to Add Properties to Template**

1. **Type `@`** in the template
2. **Select the property** from dropdown
3. Notion automatically displays the value
4. Repeat for all fields you want to show

**Example:**
- Type: `Email: @`
- Select: `Email Address` from dropdown
- Result: Shows `Email: sarah@example.com`

---

## 🔷 **Advanced: Use Callouts**

Make sections stand out:

1. **Type:** `/callout`
2. **Choose color** (Blue, Green, Yellow, Red)
3. **Add emoji** (💡, ✅, ⚠️, ❌)
4. **Add properties** inside

**Example:**
```
/callout (Green background)
✅ Verified Therapist
Status: @Status
Published: @Published
Profile: @Profile URL
```

---

## 📊 **Best Practice Layout Order**

### **Top (Hero Section):**
1. Fullname (large heading)
2. Subtitle: Preferred Name | Profession | Location
3. Status callout (Green if verified)

### **Middle (Key Info):**
4. Contact details (2-column grid)
5. Professional info (toggle - collapsed)
6. Social media (simple list)
7. Location & service (toggle)
8. Expertise & modalities (expandable)
9. Pricing (callout - yellow)

### **Bottom (Content & Admin):**
10. Bio (rich text area)
11. Admin section (toggle - collapsed, internal only)

---

## 🚀 **Benefits**

### **Before (Without Template):**
```
Click therapist → Blank page
Need to scroll through database columns
Hard to see full picture
Copy/paste data manually
```

### **After (With Template):**
```
Click therapist → Beautiful profile page
All data organized in sections
Easy to review and verify
Professional presentation
```

---

## 💡 **Pro Tips**

### **1. Use Toggles for Long Sections**
Collapse by default:
```
/toggle "Full Qualifications"
  @Profession/Key Qualification/s
  [Long text hidden until clicked]
```

### **2. Add Visual Dividers**
Between sections:
```
/divider
---
• • •
```

### **3. Color Code Status**
```
🟢 Green callout = Verified/Published
🟡 Yellow callout = Pending Review
🔴 Red callout = Issues/Missing Data
⚪ Gray callout = Archived
```

### **4. Add Action Buttons**
```
/button "Send Onboarding Email"
/button "Mark as Verified"
/button "Publish Profile"
```

### **5. Use Tables for Grids**
```
| Contact | Details |
|---------|---------|
| Email | @Email Address |
| Phone | @Phone |
| Website | @Website |
```

---

## 📱 **Mobile View**

The template automatically works on mobile:
- Stack vertically
- Touch-friendly
- Easy scrolling
- Perfect for approvals on-the-go

---

## ✅ **After Setup, You Can:**

- **Click any therapist** → Instant professional profile view
- **Review quickly** → All data at a glance
- **Verify easily** → Checkboxes and status
- **Approve fast** → One-click publishing decisions
- **Collaborate** → Comment inline, assign tasks
- **Export** → Share with team or print

---

## 🎯 **Time Investment:**

**Setup:** 15-20 minutes (one time)  
**Per therapist review:** 2-3 minutes (vs 10+ without template)  
**Total time saved:** ~14 hours for 202 therapists!

---

## 📋 **Quick Checklist:**

- [ ] Open database
- [ ] Click any therapist
- [ ] Create new template
- [ ] Copy structure from above
- [ ] Add @ properties
- [ ] Set as default
- [ ] Test with 2-3 entries
- [ ] Done! 🎉

---

## 🚀 **Result:**

You'll have a **professional, one-click profile view** for every therapist that:
- Looks beautiful
- Shows all key data
- Supports your verification workflow
- Scales to 1000+ therapists
- Works on any device

**Perfect for team collaboration and quick decisions!** ✨
