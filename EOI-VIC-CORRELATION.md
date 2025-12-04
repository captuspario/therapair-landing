# EOI Database ↔ VIC Therapist Database Correlation

## 🎯 Overview

**EOI Database** (Expression of Interest) and **VIC Therapist Database** serve different purposes but work together in the therapist onboarding workflow.

---

## 📊 Database Purposes

### EOI Database
**Purpose:** Capture initial interest from potential therapists
- **When:** User submits "Expression of Interest" form on website
- **What:** Initial contact information, interest level, basic details
- **Status:** Lead qualification, initial engagement
- **Workflow Stage:** Pre-profile creation

### VIC Therapist Database  
**Purpose:** Active therapist profiles ready for matching
- **When:** After EOI is qualified and profile is created
- **What:** Complete therapist profile, verified information, published status
- **Status:** Active profiles, searchable, matchable
- **Workflow Stage:** Post-profile creation

---

## 🔄 Correlation & Workflow

### Current State
- **EOI entries** are standalone submissions
- **VIC Therapist entries** are existing therapists (200+ imported)
- **No explicit link** between them (yet)

### Proposed Integration

```
EOI Entry Created
    ↓
Qualification Review
    ↓
Profile Creation (Manual or Automated)
    ↓
VIC Therapist Profile Created
    ↓
Relation Established (Bidirectional)
```

---

## 🔗 How They Correlate

### 1. **Email-Based Matching**
- Both databases have `Email` field
- Can find EOI entry by email
- Can find VIC Therapist entry by email
- **Current method:** Email lookup (works but not explicit)

### 2. **Relation Fields (Proposed)**
- **EOI DB** → `Related Therapist Profile` (relation) → Links to VIC Therapist DB
- **VIC Therapist DB** → `Original EOI Entry` (relation) → Links back to EOI DB
- **Benefit:** Explicit bidirectional link, easy querying

### 3. **Status Tracking**
- **EOI DB** → `Profile Creation Status` (select):
  - "Pending" - Not yet reviewed
  - "In Progress" - Being processed
  - "Created" - Profile created in VIC Therapist DB
  - "Rejected" - Not qualified
  - "Not Applicable" - Not a therapist EOI

### 4. **Profile Creation Source**
- **VIC Therapist DB** → `Profile Creation Source` (select):
  - "VIC Import" - From original CSV import
  - "EOI Submission" - Created from EOI entry
  - "Manual" - Manually created
  - "Research Participant" - From research survey

---

## 📋 Data Mapping: EOI → VIC Therapist

### Identity & Contact
| EOI Property | VIC Therapist Property | Notes |
|-------------|------------------------|-------|
| `Name` (Title) | `First Name` (Title) | May need parsing if full name |
| `Email` | `Email Address` | Direct mapping |
| `Full Name` (if exists) | `Fullname` (rich_text) | If separate field exists |

### Professional Information
| EOI Property | VIC Therapist Property | Notes |
|-------------|------------------------|-------|
| `Professional Title` | `Profession` (select) | May need mapping to select options |
| `Organisation` | `Organisation` (rich_text) | Direct mapping |
| `Specialisations` | `Specialisations` (rich_text) | Direct mapping |

### Metadata
| EOI Property | VIC Therapist Property | Notes |
|-------------|------------------------|-------|
| `Submission Date` | `Created At` (date) | When EOI was submitted |
| `Profile Creation Status` | N/A | Status in EOI DB only |
| `Related Therapist Profile` | `Original EOI Entry` | Bidirectional relation |

---

## 🛠️ Profile Creation Workflow

### Manual Process (Recommended)
1. **Review EOI Entry** in Notion
2. **Qualify Entry** (check if suitable for profile creation)
3. **Run Script:** `php create-profile-from-eoi.php <EOI_PAGE_ID> [admin_name]`
4. **Script Actions:**
   - Extracts data from EOI entry
   - Checks for existing profile (by email)
   - Creates/updates VIC Therapist profile
   - Sets relation fields
   - Updates EOI status to "Created"

### Automated Process (Future)
- Could be triggered by status change in EOI DB
- Requires qualification rules
- Not recommended initially (manual review is better)

---

## 📊 Use Cases

### Use Case 1: New Therapist from EOI
```
1. User submits EOI form → EOI DB entry created
2. Admin reviews EOI entry
3. Admin runs profile creation script
4. VIC Therapist profile created
5. Relations set (EOI ↔ VIC Therapist)
6. Profile ready for onboarding
```

### Use Case 2: Existing Therapist Updates Profile
```
1. Therapist already in VIC Therapist DB
2. Submits EOI form (maybe updating info)
3. EOI entry created
4. Admin links EOI to existing profile
5. Updates VIC Therapist profile with new info
6. Relation set for audit trail
```

### Use Case 3: Research Participant Becomes Therapist
```
1. Therapist in VIC Therapist DB (research participant)
2. Completes survey → Research DB entry
3. Expresses interest → EOI entry created
4. Admin creates/updates profile from EOI
5. All databases linked via relations
```

---

## 🔍 Querying Examples

### Find EOI Entry for a Therapist
```notion
Filter: Related Therapist Profile = [Therapist Page]
```

### Find All Profiles Created from EOI
```notion
Filter: Profile Creation Source = "EOI Submission"
```

### Find EOI Entries Ready for Profile Creation
```notion
Filter: Profile Creation Status = "Pending"
Filter: Audience Type = "Therapist"
```

### Find Therapist's Original EOI Entry
```notion
Filter: Original EOI Entry = [EOI Page]
```

---

## ✅ Benefits of Integration

1. **Audit Trail:** Track full journey from interest → profile
2. **Data Consistency:** Single source of truth for each therapist
3. **Easy Querying:** Find related entries across databases
4. **Workflow Management:** Status tracking for profile creation
5. **Data Enrichment:** Combine EOI data with survey data for complete profile

---

## 🚀 Next Steps

1. ✅ Add relation fields to both databases
2. ✅ Add profile creation status to EOI DB
3. ✅ Add profile creation source to VIC Therapist DB
4. ✅ Create profile creation script
5. ⏳ Test workflow with sample EOI entry
6. ⏳ Document data mapping rules
7. ⏳ Create Notion views for workflow management

---

## 📝 Notes

- **EOI entries** can be for different audience types (Therapist, Individual, Organisation, Supporter)
- Only **Therapist** EOI entries should create VIC Therapist profiles
- **Email is the primary identifier** for matching
- **Relations are bidirectional** for easy navigation
- **Status tracking** helps manage workflow

