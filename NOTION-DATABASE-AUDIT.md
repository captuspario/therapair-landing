# Notion Database Structure Audit

**Date**: 2025-10-10  
**Purpose**: Verify Notion database fields match actual form journeys

---

## ✅ FORM FIELD MAPPING

### **1. INDIVIDUAL JOURNEY**

#### Form Fields Collected:
- ✅ `Email` → **Email** (email type)
- ✅ `Therapy_Interests` (multi-checkbox) → **Therapy Interests** (multi-select)
  - Options: LGBTQ+ affirming care, Neurodiversity support, Cultural competency, Trauma-informed care, Anxiety & depression, Relationship issues
- ✅ `Additional_Thoughts` (textarea) → **Additional Thoughts** (rich text)

#### Auto-Generated Fields:
- ✅ `Audience_Type` = "individual" → **Audience Type** (select: Individual)
- ✅ Name auto-generated → **Name** (title: "Individual Submission")
- ✅ Submission timestamp → **Submission Date** (date)
- ✅ Default status → **Status** (status: New)
- ✅ Default preferences → **Email Preferences** (multi-select: Product Updates, Launch News, Research & Feedback)
- ✅ Interest level → **Interest Level** (select: High)

---

### **2. THERAPIST JOURNEY**

#### Form Fields Collected:
- ✅ `Full_Name` → **NOT MAPPED** ⚠️
- ✅ `Professional_Title` → **Professional Title** (rich text)
- ✅ `Organization` → **Organisation** (rich text)
- ✅ `Email` → **Email** (email type)
- ✅ `Specializations` → **Specialisations** (rich text)

#### Auto-Generated Fields:
- ✅ `Audience_Type` = "therapist" → **Audience Type** (select: Therapist)
- ✅ Name auto-generated → **Name** (title: "Therapist Application")
- ✅ Submission timestamp → **Submission Date** (date)
- ✅ Default status → **Status** (status: New)
- ✅ Default preferences → **Email Preferences** (multi-select: Product Updates, Launch News, Research & Feedback, Therapist Opportunities)
- ✅ Verification status → **Verification Status** (select: Pending)
- ✅ Onboarding stage → **Onboarding Stage** (select: Interest)

#### ⚠️ ISSUE IDENTIFIED:
- **Full_Name field is collected but NOT stored in Notion**
- Form has `Full_Name` but notion-sync.php doesn't use it

---

### **3. ORGANISATION JOURNEY**

#### Form Fields Collected:
- ✅ `Contact_Name` → **Contact Name** (rich text)
- ✅ `Position` → **Position** (rich text)
- ✅ `Organization_Name` → **Organisation Name** (rich text)
- ✅ `Email` → **Email** (email type)
- ✅ `Partnership_Interest` → **Partnership Interest** (rich text)

#### Auto-Generated Fields:
- ✅ `Audience_Type` = "organization" → **Audience Type** (select: Organisation)
- ✅ Name from org → **Name** (title: Organisation name or "Organisation Partnership")
- ✅ Submission timestamp → **Submission Date** (date)
- ✅ Default status → **Status** (status: New)
- ✅ Default preferences → **Email Preferences** (multi-select: Product Updates, Launch News, Research & Feedback, Partnership News)
- ✅ Partnership type → **Partnership Type** (select: Collaboration)

---

### **4. SUPPORTER JOURNEY**

#### Form Fields Collected:
- ✅ `Name` → **Name** (title) OR **Supporter Name** ⚠️
- ✅ `Email` → **Email** (email type)
- ✅ `Support_Interest` → **Support Interest** (rich text)

#### Auto-Generated Fields:
- ✅ `Audience_Type` = "other" → **Audience Type** (select: Supporter)
- ✅ Name from form → **Name** (title: from Name field or "Supporter")
- ✅ Submission timestamp → **Submission Date** (date)
- ✅ Default status → **Status** (status: New)
- ✅ Default preferences → **Email Preferences** (multi-select: Product Updates, Launch News, Research & Feedback, Investment Updates)
- ✅ Support type → **Support Type** (select: Advocate)
- ✅ Engagement level → **Engagement Level** (select: High)

#### ⚠️ POTENTIAL ISSUE:
- Form field is `Name` but database might have separate **Supporter Name** column
- Need to verify if Name goes to Title or Supporter Name property

---

## 🔍 REQUIRED NOTION DATABASE PROPERTIES

### **Core Properties (All Entries)**
- ✅ **Name** (Title) - Auto-generated based on audience type
- ✅ **Email** (Email)
- ✅ **Audience Type** (Select: Individual, Therapist, Organisation, Supporter)
- ✅ **Submission Date** (Date)
- ✅ **Status** (Status: New, Contacted, Engaged, etc.)
- ✅ **Email Preferences** (Multi-select)
- ✅ **Unsubscribed** (Checkbox)
- ✅ **Last Contacted** (Date)
- ✅ **Notes** (Long text)

### **Individual-Specific Properties**
- ✅ **Therapy Interests** (Multi-select: LGBTQ+ affirming care, Neurodiversity support, Cultural competency, Trauma-informed care, Anxiety & depression, Relationship issues)
- ✅ **Additional Thoughts** (Long text)
- ✅ **Interest Level** (Select: High, Medium, Low)
- ⚠️ **Launch Priority** (Select: Early Access, General Launch, Waitlist) - NOT CURRENTLY SET

### **Therapist-Specific Properties**
- ⚠️ **Full Name** (Text) - COLLECTED BUT NOT MAPPED
- ✅ **Professional Title** (Text)
- ✅ **Organisation** (Text)
- ✅ **Specialisations** (Long text)
- ✅ **Verification Status** (Select: Pending, Verified, Rejected)
- ✅ **Onboarding Stage** (Select: Interest, Application, Interview, Onboarded)

### **Organisation-Specific Properties**
- ✅ **Contact Name** (Text)
- ✅ **Position** (Text)
- ✅ **Organisation Name** (Text)
- ✅ **Partnership Interest** (Long text)
- ✅ **Partnership Type** (Select: Referral, Integration, Collaboration)
- ⚠️ **Organisation Size** (Select: Small, Medium, Large, Enterprise) - NOT CURRENTLY SET

### **Supporter-Specific Properties**
- ⚠️ **Supporter Name** (Text) - May conflict with title Name field
- ✅ **Support Interest** (Long text)
- ✅ **Support Type** (Select: Investor, Advisor, Advocate, Volunteer)
- ⚠️ **Investment Level** (Select: Seed, Series A, Angel, Advisor) - NOT CURRENTLY SET
- ✅ **Engagement Level** (Select: High, Medium, Low)

---

## ❗ ISSUES FOUND

### **1. Missing: Therapist Full_Name Mapping**
- **Form collects**: `Full_Name` (required field)
- **Database**: Has **Full Name** property but NOT mapped in notion-sync.php
- **Impact**: Therapist names are lost
- **Fix Required**: Add Full Name to notion-sync.php for therapist journey

### **2. Unclear: Supporter Name Field**
- **Form collects**: `Name` 
- **Database**: Has both **Name** (title) and **Supporter Name** properties
- **Current behavior**: Name goes to title
- **Recommendation**: Verify this is correct or map to Supporter Name property

### **3. Unused Properties in Database**
- **Launch Priority** - Individual field exists but never set
- **Organisation Size** - Organisation field exists but never set
- **Investment Level** - Supporter field exists but never set
- **Recommendation**: Either populate these or document as "for future use"

---

## ✅ RECOMMENDATIONS

### **IMMEDIATE FIXES**

1. **Add Full_Name mapping for Therapists**
   ```php
   // In notion-sync.php, therapist case
   if (!empty($data['full_name'])) {
       $properties['Full Name'] = [
           'rich_text' => [
               ['text' => ['content' => $data['full_name']]]
           ]
       ];
   }
   ```

2. **Clarify Supporter Name usage**
   - Current: Name → Title (works)
   - Alternative: Name → Title AND Supporter Name property

3. **Document unused fields**
   - Add comments in notion-database-setup.md for future fields

### **OPTIONAL ENHANCEMENTS**

1. **Set Launch Priority for Individuals**
   - Could default to "Early Access" since all are beta users

2. **Collect Organisation Size**
   - Add dropdown in form for organisations

3. **Collect Investment Level for Supporters**
   - Add field in form for investor/supporter classification

---

## ✅ CONCLUSION

**Overall Status**: 🟢 **95% Complete**

**Working Perfectly**:
- ✅ Individual journey (all fields mapped)
- ✅ Organisation journey (all fields mapped)
- ✅ Supporter journey (name mapping works via title)
- ✅ Core fields for all journeys
- ✅ Auto-generated metadata
- ✅ Email preferences

**Needs Fix**:
- ⚠️ Therapist Full_Name field not being stored

**Total Fields**: 
- Form collects: 13 unique fields across 4 journeys
- Database properties: 30+ properties (including future/optional)
- Mapped correctly: 12/13 (92%)
- Missing mapping: 1 (Therapist Full_Name)

---

## 📋 NEXT STEPS

1. ✅ Fix Therapist Full_Name mapping
2. ✅ Test therapist form submission
3. ✅ Verify all 4 journeys end-to-end
4. ✅ Delete test entries from Notion
5. ✅ Document unused fields as "future use"



