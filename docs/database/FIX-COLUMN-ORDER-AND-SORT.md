# Fix Column Order and Sorting in Notion

## ✅ **Confirmed: 202 Therapists Processed!**

All entries have been cleaned up with proper names, tokens, and regions.

---

## 🔧 **Issue 1: Columns are Alphabetical**

**Why:** Notion API cannot reorder columns - only you can do this manually in the UI.

### **Quick Fix (5 minutes):**

1. **Open your database** in Notion
2. **Click and drag** any column header to reorder
3. **Follow this order** (drag to match):

```
📋 Recommended Order:

1.  Name (Title)
2.  First Name
3.  Last Name
4.  Email Address
5.  Phone
6.  Gender

7.  Profession/Key Qualification/s
8.  Business Name
9.  Website
10. Social Media
11. My relevant body for handling complaints/queries

12. Suburb/s of practice
13. Region (NEW)
14. Area predominantly work in
15. What accessibility options does your practice have?
16. Languages spoken other than English
17. Do you offer rebates or other funding models?

18. Types of People I see and am trained in...
19. If there are groups who you don't have specific...
20. Clients I don't work with
21. Special services I deliver
22. Aspects of MY OWN lived experience
23. Anything else brief...

24. I confirm insurance...
25. I confirm consent...
26. Please consider linking...

27. Status (NEW)
28. Published (NEW)
29. Profile URL (NEW)
30. Onboarding Token (NEW)
31. Token Expiry (NEW)
32. Last Contacted (NEW)
33. Admin Notes (NEW)
34. Import Date (NEW)
35. Timestamp
```

### **Pro Tip: Group Columns Visually**

After reordering, you can add **divider properties** or use **empty columns** to visually separate sections.

---

## 🔧 **Issue 2: Rows Sorted Z-A (Should be A-Z)**

**Why:** Your current view has a sort applied in reverse order.

### **Quick Fix (10 seconds):**

1. **Open your database** in Notion
2. Click the **`Name`** column header
3. Look for **Sort** option in the menu
4. Click **"Sort ascending"** (A → Z)
   - If it says "Remove sort", click it first, then click column again and choose "Sort ascending"

**Alternative Method:**
1. Click the **"•••"** menu at top right of database
2. Click **"Sort"**
3. Remove any existing sorts
4. Add new sort: **Name → Ascending (A-Z)**

---

## 📸 **Visual Guide**

### **Sorting:**
```
Click Name column header
    ↓
Choose "Sort ascending"
    ↓
Done! Now A-Z ✓
```

### **Column Reordering:**
```
Click column header
    ↓
Drag to new position
    ↓
Repeat for all columns
    ↓
Done! ✓
```

---

## 🎯 **After You Fix These:**

Your database will look like this:

```
┌──────────────────────────────────────────────────────────────┐
│ Name ↑ A-Z        │ Email              │ Region    │ Status  │
├───────────────────┼────────────────────┼───────────┼─────────┤
│ Aaron Howearth    │ info@howearth...   │ CBD       │ Pending │
│ Abby Draper       │ abby@...           │ North     │ Pending │
│ Adelle Kent       │ adelle@...         │ Online    │ Pending │
│ ...               │                    │           │         │
│ Yuki (Xue) Mei    │ yuki@...           │ South     │ Pending │
│ [202 total]       │                    │           │         │
└───────────────────┴────────────────────┴───────────┴─────────┘
```

---

## ⚠️ **Important Notes**

### **Columns I CAN'T Change via API:**
- ❌ Column order (you must drag manually)
- ❌ Column names (you must rename manually)
- ❌ Column types (already correct)

### **Columns I DID Change via API:**
- ✅ Added new properties (Status, Published, Region, etc.)
- ✅ Populated all data (names, tokens, URLs)
- ✅ Set all values (dates, checkboxes, selects)

### **Sorting:**
- ✅ API can set default sort, but your view overrides it
- ✅ You need to fix the sort in the view settings

---

## 🚀 **After Manual Fixes:**

You'll have:
- ✅ 202 therapists (all entries)
- ✅ Logical column order (identity → professional → location → system)
- ✅ A-Z sorting (easy to find therapists)
- ✅ Clean, professional database
- ✅ Ready for onboarding

---

## 📹 **Video Alternative**

If you prefer, you can:
1. **Record your screen** while fixing one column
2. **Send me the video**
3. I'll create more specific instructions

Or just follow the steps above - it should take about 5-7 minutes total! 🎯

---

## ✅ **Summary**

**What's Done:**
- ✓ All 202 therapists processed
- ✓ All data cleaned and formatted
- ✓ All new properties added
- ✓ All tokens generated
- ✓ All regions standardized

**What You Need to Do (Manual):**
- [ ] Drag columns to reorder (5 min)
- [ ] Set sort to A-Z (10 sec)

That's it! Then you're fully ready to go! 🎉
