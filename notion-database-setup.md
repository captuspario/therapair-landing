# Notion Database Setup Guide - Therapair Submissions

## 🎯 **Step-by-Step Setup Instructions**

### **Step 1: Create the Database**

1. **Open Notion** and create a new page
2. **Name it**: "Therapair Submissions"
3. **Create a database** by typing `/database` and selecting "Table"

### **Step 2: Add Core Properties**

#### **Essential Properties (Add these first):**

| Property Name | Type | Options/Description |
|---------------|------|-------------------|
| **Name** | Title | Auto-generated from form data |
| **Email** | Email | Primary contact method |
| **Audience Type** | Select | Individual, Therapist, Organization, Supporter |
| **Submission Date** | Created time | When they submitted (auto-filled) |
| **Status** | Select | New, Contacted, Engaged, Unsubscribed |
| **Email Preferences** | Multi-select | Product Updates, Launch News, Therapist Opportunities, Partnership News, Research & Feedback, Event Invitations, Investment Updates |
| **Unsubscribed** | Checkbox | Has this person opted out? |
| **Last Contacted** | Date | When we last reached out |
| **Notes** | Long text | Internal notes and observations |

### **Step 3: Add Audience-Specific Properties**

#### **For Individual Audience:**
| Property Name | Type | Description |
|---------------|------|-------------|
| **Therapy Interests** | Multi-select | LGBTQ+ affirming care, Neurodiversity support, Cultural competency, Trauma-informed care, Anxiety & depression, Relationship issues |
| **Additional Thoughts** | Long text | Free-text feedback they provided |
| **Interest Level** | Select | High, Medium, Low |
| **Launch Priority** | Select | Early Access, General Launch, Waitlist |

#### **For Therapist Audience:**
| Property Name | Type | Description |
|---------------|------|-------------|
| **Professional Title** | Text | Dr., Psychologist, Counselor, etc. |
| **Organization** | Text | Where they work |
| **Specializations** | Long text | Their areas of expertise |
| **Verification Status** | Select | Pending, Verified, Rejected |
| **Onboarding Stage** | Select | Interest, Application, Interview, Onboarded |

#### **For Organization Audience:**
| Property Name | Type | Description |
|---------------|------|-------------|
| **Contact Name** | Text | Person who submitted |
| **Position** | Text | Their role/title |
| **Organization Name** | Text | Company/organization |
| **Partnership Interest** | Long text | What they're interested in |
| **Partnership Type** | Select | Referral, Integration, Collaboration |
| **Organization Size** | Select | Small, Medium, Large, Enterprise |

#### **For Supporter Audience:**
| Property Name | Type | Description |
|---------------|------|-------------|
| **Name** | Text | Supporter name |
| **Support Interest** | Long text | How they want to help |
| **Support Type** | Select | Investor, Advisor, Advocate, Volunteer |
| **Investment Level** | Select | Seed, Series A, Angel, Advisor |
| **Engagement Level** | Select | High, Medium, Low |

### **Step 4: Create Database Views**

#### **View 1: All Submissions**
- **Name**: "All Submissions"
- **Filter**: None
- **Sort**: Submission Date (Newest first)
- **Group by**: Audience Type

#### **View 2: By Audience Type**
- **Name**: "By Audience Type"
- **Filter**: None
- **Sort**: Submission Date (Newest first)
- **Group by**: Audience Type

#### **View 3: New Submissions**
- **Name**: "New Submissions"
- **Filter**: Status = New
- **Sort**: Submission Date (Newest first)

#### **View 4: Contacted**
- **Name**: "Contacted"
- **Filter**: Status = Contacted
- **Sort**: Last Contacted (Newest first)

#### **View 5: Engaged Users**
- **Name**: "Engaged Users"
- **Filter**: Status = Engaged
- **Sort**: Last Contacted (Newest first)

#### **View 6: Unsubscribed**
- **Name**: "Unsubscribed"
- **Filter**: Unsubscribed = Yes
- **Sort**: Submission Date (Newest first)

### **Step 5: Set Up Templates**

#### **Template 1: Individual Submission**
- **Name**: "Individual Submission"
- **Default Values**:
  - Audience Type: Individual
  - Status: New
  - Email Preferences: All selected
  - Unsubscribed: No

#### **Template 2: Therapist Application**
- **Name**: "Therapist Application"
- **Default Values**:
  - Audience Type: Therapist
  - Status: New
  - Verification Status: Pending
  - Onboarding Stage: Interest

#### **Template 3: Organization Partnership**
- **Name**: "Organization Partnership"
- **Default Values**:
  - Audience Type: Organization
  - Status: New
  - Partnership Type: Collaboration

#### **Template 4: Supporter Interest**
- **Name**: "Supporter Interest"
- **Default Values**:
  - Audience Type: Supporter
  - Status: New
  - Support Type: Advocate

### **Step 6: Basic Automation Setup**

#### **Automation 1: Status Update**
- **Trigger**: When "Last Contacted" is updated
- **Action**: Set Status to "Contacted"

#### **Automation 2: Engagement Tracking**
- **Trigger**: When "Last Contacted" is updated multiple times
- **Action**: Set Status to "Engaged"

#### **Automation 3: Unsubscribe Handling**
- **Trigger**: When "Unsubscribed" is checked
- **Action**: Set Status to "Unsubscribed" and clear all Email Preferences

### **Step 7: Create Useful Formulas**

#### **Formula 1: Days Since Submission**
```
dateBetween(prop("Submission Date"), now(), "days")
```

#### **Formula 2: Days Since Last Contact**
```
dateBetween(prop("Last Contacted"), now(), "days")
```

#### **Formula 3: Email Preference Count**
```
length(prop("Email Preferences"))
```

#### **Formula 4: Display Name**
```
if(prop("Audience Type") == "Organization", prop("Contact Name"), if(prop("Audience Type") == "Supporter", prop("Name"), if(prop("Audience Type") == "Therapist", "Professional", "Individual")))
```

### **Step 8: Add Sample Data**

#### **Sample Individual Entry:**
- Name: "Individual Submission"
- Email: "test@example.com"
- Audience Type: Individual
- Therapy Interests: LGBTQ+ affirming care, Trauma-informed care, Cultural competency
- Additional Thoughts: "Looking for someone who understands intersectionality and cultural differences"
- Status: New
- Email Preferences: Product Updates, Launch News, Research & Feedback

#### **Sample Therapist Entry:**
- Name: "Therapist Application"
- Email: "sarah@therapy.com"
- Audience Type: Therapist
- Professional Title: "Licensed Clinical Psychologist"
- Organization: "Private Practice"
- Specializations: "Trauma therapy, LGBTQ+ affirming care, anxiety disorders"
- Status: New
- Verification Status: Pending

#### **Sample Organization Entry:**
- Name: "Organization Partnership"
- Email: "partnerships@company.com"
- Audience Type: Organization
- Contact Name: "Sarah Johnson"
- Position: "Mental Health Program Manager"
- Organization Name: "Community Health Center"
- Partnership Interest: "We'd like to explore how Therapair can support our diverse client base"
- Status: New
- Partnership Type: Collaboration

#### **Sample Supporter Entry:**
- Name: "John Smith"
- Email: "john@investor.com"
- Audience Type: Supporter
- Support Interest: "I'm passionate about inclusive mental health and would love to support your mission"
- Status: New
- Support Type: Investor

### **Step 9: Create Dashboard Views**

#### **Dashboard 1: Overview**
- **Cards**: Total submissions, New submissions, Contacted, Engaged
- **Charts**: Submissions by audience type, Submissions over time
- **Filters**: Last 30 days, All time

#### **Dashboard 2: Audience Breakdown**
- **Individual**: Count, Interest level distribution
- **Therapist**: Count, Verification status
- **Organization**: Count, Partnership type
- **Supporter**: Count, Support type

### **Step 10: Integration Preparation**

#### **For Future API Integration:**
1. **Note the Database ID** (found in the URL)
2. **Set up API access** (when ready)
3. **Create webhook endpoints** (for real-time updates)
4. **Test data sync** (form submissions → Notion)

## 🔧 **Maintenance Tasks**

### **Daily:**
- Check "New Submissions" view
- Update status when contacting users
- Add notes about interactions

### **Weekly:**
- Review "Contacted" entries
- Move engaged users to "Engaged" status
- Update "Last Contacted" dates

### **Monthly:**
- Review unsubscribed users
- Analyze engagement patterns
- Update email preferences based on feedback

## 📊 **Reporting & Analytics**

### **Key Metrics to Track:**
1. **Total submissions** by audience type
2. **Conversion rates** (New → Contacted → Engaged)
3. **Email preference** distribution
4. **Unsubscribe rates**
5. **Response rates** by audience type

### **Monthly Reports:**
- Submission trends
- Audience engagement
- Email preference changes
- Unsubscribe analysis

## 🚀 **Next Steps**

1. **Set up the database** using this guide
2. **Test with sample data**
3. **Create your first views and templates**
4. **Integrate with your form** (when ready)
5. **Set up basic automations**
6. **Train your team** on the system

---

## 📞 **Need Help?**

- **Notion Help Center**: https://www.notion.so/help
- **Database Templates**: Search "CRM" or "Contact Management"
- **Automation Guide**: https://www.notion.so/help/automations

**This database structure will scale with your growth and provide powerful insights into your user engagement!** 🎯📊
