# Database Integration Analysis & Best Practices

## 📊 Current Database Architecture

### Database Overview

| Database | ID | Purpose | Primary Use |
|----------|-----|---------|--------------|
| **VIC Therapist DB** | `28c5c25944da80a48d85fd43119f4ec1` | User Testing & Directory | Research participants, therapist profiles |
| **Research DB** | `2995c25944da80a5b5d1f0eb9db74a36` | Survey Responses | User research survey submissions |
| **Feedback DB** | `2a75c25944da804cbd87d4daac0ae901` | User Feedback | Widget feedback, sandbox feedback |
| **EOI DB** | `2875c25944da80c0b14afbbdf2510bb0` | Expression of Interest | Form submissions, new therapist interest |

---

## 🔄 Current Data Flow & Tracking

### 1. Email Campaign Tracking (VIC Therapist DB)

**Trigger:** Resend webhook → `api/research/email-event.php`

**Properties Updated:**
- `Research Email Opened` (checkbox)
- `Research Email Opened Date` (date)
- `Research Email Opens Count` (number)
- `Research Survey Clicked` (checkbox)
- `Research Survey Clicked Date` (date)
- `Research Survey Clicks Count` (number)
- `Research Sandbox Clicked` (checkbox)
- `Research Sandbox Clicked Date` (date)
- `Research Status` (select) - "Opened (email 1)", "Clicked Survey", etc.
- `Latest Survey Date` (date)
- `Research Source Notes` (rich_text) - UTM parameters

**How It Works:**
1. Email sent with UTM parameters (`utm_email`, `utm_content`, `utm_variant`)
2. User opens/clicks email
3. Resend webhook fires → `email-event.php`
4. Finds therapist by email in VIC Therapist DB
5. Updates engagement properties

---

### 2. Survey Submission Tracking

**Trigger:** Survey completion → `api/research/response.php`

**Research DB (Primary):**
- Creates new entry with all survey responses
- Properties: Profession, Years Practice, Client Types, Modalities, etc.
- **Pricing** (rich_text) - Fee per match + Monthly subscription
- **Comments** (rich_text) - Free-text feedback
- **Submitted** (date) - Submission timestamp

**VIC Therapist DB (Secondary Update):**
- `Research Survey Completed` (checkbox) = true
- `Research Survey Completed Date` (date)
- `Research Status` (select) = "Completed Survey"
- `Research Variant` (select) - A/B test variant

**How It Works:**
1. User completes survey with token
2. Token decoded to get email
3. Survey saved to Research DB
4. VIC Therapist DB entry updated via email lookup

---

### 3. Feedback Submission Tracking

**Trigger:** Feedback widget → `api/research/feedback.php`

**Feedback DB (Primary):**
- Creates new entry with rating, comment, tags
- Properties: Name, Rating, Feedback, Submission Date, Audience Type

**VIC Therapist DB (Secondary Update - if therapist found):**
- `Sandbox Feedback` (rich_text) - Appends feedback entry
- Links via email lookup

**How It Works:**
1. User submits feedback (may include email/tracking ID)
2. Feedback saved to Feedback DB
3. If email found, VIC Therapist DB updated with feedback summary

---

### 4. A/B Testing Tracking

**Properties in VIC Therapist DB:**
- `Research Variant` (select) - "A" or "B"
- `Research Invite Sent` (checkbox)
- `Research Invite Sent Date` (date)
- `Research Experiment ID` (rich_text) - "EXP-01"

**How It Works:**
1. Batch script assigns variant (A/B)
2. Email sent with variant in UTM parameters
3. All engagement events track variant
4. Dashboard views filter by variant

---

## ❓ Questions for Clarification

### 1. Follow-up Status Tracking
**Question:** How should follow-up status be tracked?
- **Option A:** Add `Research Follow-up Status` (select) with values: "No Follow-up", "Follow-up 1 Sent", "Follow-up 2 Sent", "Follow-up Complete"
- **Option B:** Use `Research Status` to include follow-up info: "Completed Survey - Follow-up 1 Sent"
- **Option C:** Separate `Research Follow-up Count` (number) + `Research Last Follow-up Date` (date)

**Recommendation:** Option C (separate count + date) for flexibility

---

### 2. Cross-Database Communication
**Question:** How should databases communicate?

**Current State:**
- Research DB entries are standalone (no link to VIC Therapist DB)
- Feedback DB entries are standalone (no link to VIC Therapist DB)
- EOI DB entries are standalone (no link to VIC Therapist DB)

**Proposed Options:**

**Option A: Relation Fields**
- Add "Related Therapist" (relation) to Research DB → links to VIC Therapist DB
- Add "Related Survey Response" (relation) to VIC Therapist DB → links to Research DB
- Add "Related Feedback" (relation) to VIC Therapist DB → links to Feedback DB
- Add "Related EOI" (relation) to VIC Therapist DB → links to EOI DB

**Option B: Email-Based Linking (Current)**
- Keep current email-based lookup
- No explicit relations
- Simpler but less robust

**Option C: Hybrid**
- Use email for initial linking
- Add relation fields for explicit connections
- Best of both worlds

**Recommendation:** Option C (Hybrid) - email for quick lookup, relations for explicit connections

---

### 3. Therapist Profile Creation Workflow
**Question:** How should profiles be created from VIC + EOI data?

**Current State:**
- VIC Therapist DB: Existing therapists (200+ entries)
- EOI DB: New expressions of interest

**Proposed Workflow:**

```
EOI Entry → Qualification → Profile Creation → VIC Therapist DB
```

**Questions:**
1. Should EOI entries automatically create VIC Therapist entries?
2. Or should it be a manual process with status tracking?
3. What properties should be copied from EOI → VIC Therapist?
4. Should there be a "Profile Creation Status" field in EOI DB?

**Recommendation:**
- Add `Profile Creation Status` (select) to EOI DB: "Pending", "In Progress", "Created", "Rejected"
- Add `Related Therapist Profile` (relation) to EOI DB → links to VIC Therapist DB when created
- Add `Original EOI Entry` (relation) to VIC Therapist DB → links back to EOI DB
- Manual process with status tracking (not automatic)

---

### 4. Feedback Linking
**Question:** How should feedback be linked to therapists?

**Current State:**
- Feedback saved to Feedback DB
- If email found, appended to VIC Therapist DB `Sandbox Feedback` field

**Proposed Options:**

**Option A: Relation Field (Recommended)**
- Add `Related Therapist` (relation) to Feedback DB → links to VIC Therapist DB
- Keep `Sandbox Feedback` in VIC Therapist DB for quick reference
- Best for querying all feedback for a therapist

**Option B: Email-Based Only (Current)**
- Keep current email-based lookup
- Simpler but harder to query

**Recommendation:** Option A (Relation Field) for better querying

---

### 5. Research Response Linking
**Question:** How should survey responses be linked to therapists?

**Current State:**
- Survey saved to Research DB
- VIC Therapist DB updated with completion status
- No explicit relation

**Proposed:**
- Add `Related Therapist` (relation) to Research DB → links to VIC Therapist DB
- Add `Related Survey Response` (relation) to VIC Therapist DB → links to Research DB
- Enables: "Show all survey responses for this therapist"

---

## 🎯 Recommended Database Integration Plan

### Phase 1: Fix Current Issues ✅
- [x] Remove pricing from Notes field (keep only in Pricing)
- [ ] Add relation fields for cross-database linking
- [ ] Standardize property names across databases

### Phase 2: Enhance Tracking
- [ ] Add follow-up status tracking
- [ ] Add profile creation workflow tracking
- [ ] Add explicit relations between databases

### Phase 3: Prepare for Profile Creation
- [ ] Add EOI → VIC Therapist workflow
- [ ] Add status tracking for profile creation
- [ ] Add data mapping from EOI to VIC Therapist properties

---

## 📋 Property Mapping Reference

### VIC Therapist DB → Research DB
**Link:** Email address (current) + Relation field (proposed)

**Properties Synced:**
- Survey completion status
- Survey completion date
- A/B test variant
- Email engagement (opens, clicks)

### VIC Therapist DB → Feedback DB
**Link:** Email address (current) + Relation field (proposed)

**Properties Synced:**
- Feedback summary in `Sandbox Feedback` field

### EOI DB → VIC Therapist DB
**Link:** Relation field (proposed)

**Workflow:**
- EOI entry created
- Profile creation status tracked
- When profile created, relation added
- Properties copied from EOI to VIC Therapist DB

---

## 🔧 Implementation Checklist

### Immediate Fixes
- [x] Remove pricing from Notes field
- [ ] Test survey submission
- [ ] Test feedback submission
- [ ] Test email tracking

### Database Schema Updates
- [ ] Add relation fields to Research DB
- [ ] Add relation fields to Feedback DB
- [ ] Add relation fields to EOI DB
- [ ] Add relation fields to VIC Therapist DB
- [ ] Add follow-up status properties
- [ ] Add profile creation status to EOI DB

### Code Updates
- [ ] Update `response.php` to set relation when saving survey
- [ ] Update `feedback.php` to set relation when saving feedback
- [ ] Update `email-event.php` to maintain relations
- [ ] Create script to backfill relations for existing entries

---

## 📝 Next Steps

1. **Answer clarifying questions** (see above)
2. **Create database schema update script** (add relation fields)
3. **Update code to use relations** (in addition to email lookup)
4. **Create profile creation workflow** (EOI → VIC Therapist)
5. **Test end-to-end** (email → survey → feedback → profile creation)

