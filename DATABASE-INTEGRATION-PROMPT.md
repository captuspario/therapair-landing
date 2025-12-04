# Database Integration Prompt - Best Practices

## 🎯 Objective

Design and implement a comprehensive database integration system for Therapair's user research and therapist management platform, ensuring seamless data flow, cross-database communication, and preparation for future therapist profile creation workflows.

---

## 📊 Current System Architecture

### Databases
1. **VIC Therapist DB** (`28c5c25944da80a48d85fd43119f4ec1`)
   - Purpose: User testing participants, therapist directory
   - Contains: 200+ therapist entries with research tracking properties

2. **Research DB** (`2995c25944da80a5b5d1f0eb9db74a36`)
   - Purpose: User research survey responses
   - Contains: Survey submissions with pricing, preferences, feedback

3. **Feedback DB** (`2a75c25944da804cbd87d4daac0ae901`)
   - Purpose: User feedback from widgets and sandbox
   - Contains: Ratings, comments, tags, page context

4. **EOI DB** (`2875c25944da80c0b14afbbdf2510bb0`)
   - Purpose: Expression of Interest submissions
   - Contains: New therapist interest forms

### Current Integration Points
- **Email Tracking:** Resend webhook → `api/research/email-event.php` → Updates VIC Therapist DB
- **Survey Submission:** `api/research/response.php` → Saves to Research DB + Updates VIC Therapist DB
- **Feedback Submission:** `api/research/feedback.php` → Saves to Feedback DB + Updates VIC Therapist DB (if email found)
- **Linking Method:** Email-based lookup (no explicit relations)

---

## 🎯 Requirements

### 1. Fix Data Duplication
- ✅ Remove pricing from Notes field (keep only in Pricing field)
- Ensure no duplicate data across databases

### 2. User Behavior Tracking
**Track the following user behaviors:**
- Email opens (via Resend webhook)
- Email clicks (via Resend webhook)
- Survey completion (via survey submission)
- Feedback submission (via feedback widget)
- Sandbox visits (via tracking script)
- Follow-up email status (manual or automated)

**For each behavior, track:**
- When it happened (timestamp)
- Which user (email/therapist ID)
- Context (UTM parameters, variant, experiment ID)
- Status (completed, pending, failed)

### 3. Cross-Database Communication
**Ensure databases can:**
- Link related entries (survey → therapist, feedback → therapist, EOI → therapist)
- Query related data (e.g., "Show all feedback for this therapist")
- Maintain data consistency (no orphaned entries)
- Support bidirectional relationships

### 4. Follow-up Status Tracking
**Track email follow-up status:**
- Which follow-up emails have been sent
- When they were sent
- Response status (opened, clicked, completed)
- Next follow-up action needed

### 5. Profile Creation Workflow
**Prepare for creating therapist profiles from:**
- Existing VIC Therapist DB entries (enhance with survey data)
- New EOI DB entries (convert to therapist profiles)
- Hybrid approach (combine data from both sources)

**Workflow should support:**
- Status tracking (pending, in progress, created, rejected)
- Data mapping (EOI properties → VIC Therapist properties)
- Relation tracking (EOI entry → created profile)
- Audit trail (who created, when, from what source)

---

## 🏗️ Design Principles

### 1. Single Source of Truth
- Each piece of data should have one primary location
- Other databases reference, don't duplicate
- Use relations for linking, not copying data

### 2. Explicit Relations
- Use Notion relation fields for explicit connections
- Maintain email-based lookup as fallback
- Support bidirectional queries

### 3. Event-Driven Updates
- Each user action triggers database updates
- Updates happen in real-time (webhooks, API calls)
- Maintain audit trail with timestamps

### 4. Scalability
- Design for growth (more therapists, more surveys, more feedback)
- Support filtering and segmentation
- Enable analytics and reporting

### 5. Data Integrity
- Validate data before saving
- Handle errors gracefully
- Maintain consistency across databases

---

## 🔧 Technical Requirements

### Database Schema Updates

#### VIC Therapist DB
**Add/Update Properties:**
- `Research Survey Response` (relation) → Links to Research DB
- `Related Feedback` (relation) → Links to Feedback DB
- `Related EOI Entry` (relation) → Links to EOI DB
- `Research Follow-up Status` (select) → "No Follow-up", "Follow-up 1 Sent", "Follow-up 2 Sent", "Complete"
- `Research Follow-up Count` (number) → Number of follow-ups sent
- `Research Last Follow-up Date` (date) → Date of last follow-up
- `Profile Creation Source` (select) → "VIC Import", "EOI Submission", "Manual"
- `Original EOI Entry` (relation) → Links to EOI DB if created from EOI

#### Research DB
**Add/Update Properties:**
- `Related Therapist` (relation) → Links to VIC Therapist DB
- `Pricing` (rich_text) → Fee per match + Monthly subscription (ONLY here, not in Notes)
- Remove pricing from `29. Notes` field

#### Feedback DB
**Add/Update Properties:**
- `Related Therapist` (relation) → Links to VIC Therapist DB
- `Related Survey Response` (relation) → Links to Research DB (if applicable)

#### EOI DB
**Add/Update Properties:**
- `Profile Creation Status` (select) → "Pending", "In Progress", "Created", "Rejected"
- `Related Therapist Profile` (relation) → Links to VIC Therapist DB when created
- `Profile Creation Date` (date) → When profile was created
- `Profile Created By` (rich_text) → Who created the profile

### Code Updates

#### `api/research/response.php`
- Set `Related Therapist` relation when saving survey
- Remove pricing from Notes field
- Ensure pricing only in Pricing field

#### `api/research/feedback.php`
- Set `Related Therapist` relation when saving feedback
- Optionally set `Related Survey Response` if survey token present

#### `api/research/email-event.php`
- Update follow-up status properties
- Maintain relations when updating engagement

#### New Scripts Needed
- `scripts/create-profile-from-eoi.php` - Convert EOI entry to therapist profile
- `scripts/backfill-relations.php` - Add relations to existing entries
- `scripts/sync-survey-to-therapist.php` - Sync survey data to therapist profile

---

## 📋 Implementation Checklist

### Phase 1: Fixes & Cleanup ✅
- [x] Remove pricing from Notes field
- [ ] Test all database connections
- [ ] Verify no data duplication

### Phase 2: Schema Updates
- [ ] Add relation fields to all databases
- [ ] Add follow-up status properties
- [ ] Add profile creation properties
- [ ] Update property names for consistency

### Phase 3: Code Updates
- [ ] Update survey submission to set relations
- [ ] Update feedback submission to set relations
- [ ] Update email tracking to maintain relations
- [ ] Create profile creation workflow

### Phase 4: Data Migration
- [ ] Backfill relations for existing entries
- [ ] Migrate pricing data (remove from Notes)
- [ ] Verify data integrity

### Phase 5: Testing & Validation
- [ ] Test email tracking → database updates
- [ ] Test survey submission → relations set
- [ ] Test feedback submission → relations set
- [ ] Test profile creation workflow
- [ ] Test cross-database queries

---

## ❓ Clarifying Questions

### 1. Follow-up Status
**Question:** How should follow-up status be tracked?
- [ ] Option A: Single `Research Follow-up Status` (select) field
- [ ] Option B: Separate count + date fields
- [ ] Option C: Multiple checkbox fields (Follow-up 1 Sent, Follow-up 2 Sent, etc.)

**Recommendation:** Option B (count + date) for flexibility

### 2. Profile Creation Workflow
**Question:** Should profile creation be:
- [ ] Automatic (EOI entry → automatic profile creation)
- [ ] Manual (EOI entry → manual review → profile creation)
- [ ] Hybrid (automatic for qualified, manual for others)

**Recommendation:** Manual with status tracking for quality control

### 3. Data Mapping
**Question:** Which EOI properties should map to VIC Therapist properties?
- [ ] All properties
- [ ] Specific subset (list which ones)
- [ ] Custom mapping per use case

**Recommendation:** Provide mapping table for review

### 4. Feedback Linking
**Question:** Should feedback always link to a therapist, or can it be anonymous?
- [ ] Always require therapist link
- [ ] Allow anonymous feedback
- [ ] Link when email/token available, anonymous otherwise

**Recommendation:** Link when available, allow anonymous otherwise

---

## 🎯 Success Criteria

### Functional Requirements
- ✅ No duplicate pricing data
- ✅ All user behaviors tracked
- ✅ Cross-database relations working
- ✅ Follow-up status tracked
- ✅ Profile creation workflow functional

### Technical Requirements
- ✅ All databases accessible
- ✅ Relations set correctly
- ✅ Data integrity maintained
- ✅ Error handling robust
- ✅ Performance acceptable

### User Experience
- ✅ Survey submissions work
- ✅ Feedback submissions work
- ✅ Email tracking accurate
- ✅ Follow-up status visible
- ✅ Profile creation smooth

---

## 📚 Best Practices Applied

1. **Single Source of Truth** - Each data point has one primary location
2. **Explicit Relations** - Use Notion relations for linking
3. **Event-Driven** - Real-time updates via webhooks/API
4. **Audit Trail** - Timestamps and status tracking
5. **Scalability** - Design for growth
6. **Data Integrity** - Validation and error handling
7. **Maintainability** - Clear structure and documentation

---

## 🚀 Next Steps

1. **Review and answer clarifying questions**
2. **Approve database schema updates**
3. **Implement fixes and updates**
4. **Test end-to-end workflows**
5. **Deploy and monitor**

---

## 📝 Notes

- All database IDs are in `config.php`
- All API endpoints use `bootstrap.php` for shared functions
- Email tracking requires Resend webhook configuration
- Relations require Notion database schema updates
- Profile creation workflow is new and needs design approval

