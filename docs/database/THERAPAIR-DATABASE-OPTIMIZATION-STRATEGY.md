# 🎯 Therapair Database Optimization Strategy
**Complete Guide for National-Scale Therapist Matching Platform**

---

## 📋 Table of Contents
1. [Executive Summary](#executive-summary)
2. [Database Structure Optimization](#database-structure-optimization)
3. [Therapist Profile Standards](#therapist-profile-standards)
4. [Search & Matching Strategy](#search--matching-strategy)
5. [Location Architecture](#location-architecture)
6. [Questionnaire Design](#questionnaire-design)
7. [National Scalability](#national-scalability)
8. [Competitor Analysis](#competitor-analysis)
9. [Implementation Roadmap](#implementation-roadmap)

---

## 1. Executive Summary

### Current State Analysis
- **Database:** 202 therapists (VIC only)
- **Data Quality:** 98% overall quality score
- **Coverage:** Email 100%, Phone 15%, Social 21%
- **Status:** Production-ready for Victoria

### Target State (National Platform)
- **Scale:** 2,000-10,000+ therapists across Australia
- **Coverage:** All states/territories
- **Match Quality:** 85%+ client satisfaction with matches
- **Response Time:** <2 seconds for search results
- **Conversion:** 25%+ questionnaire completion to booking

### Key Optimization Areas
1. ✅ **Standardize profile data** for consistent card display
2. ✅ **Optimize location architecture** for national search
3. ✅ **Design minimal questionnaire** (3-5 questions max)
4. ✅ **Create therapist bio guidelines** for self-service updates
5. ✅ **Build scalable matching algorithm** using weighted criteria

---

## 2. Database Structure Optimization

### 2.1 Core Profile Fields (Required)

#### **Identity & Contact**
```
┌─────────────────────────────────────────────────────────┐
│ FIELD NAME           │ TYPE        │ DISPLAY ON CARD    │
├─────────────────────────────────────────────────────────┤
│ Full Name            │ Title       │ ✅ Primary heading │
│ First Name           │ Rich Text   │ ❌ Internal only   │
│ Last Name            │ Rich Text   │ ❌ Internal only   │
│ Preferred Name       │ Rich Text   │ ✅ If different    │
│ Professional Title   │ Select      │ ✅ Subtitle        │
│ Pronouns             │ Rich Text   │ ✅ Next to name    │
│ Email                │ Email       │ ❌ Admin only      │
│ Phone                │ Rich Text   │ ❌ After click     │
│ Website              │ URL         │ ✅ Link button     │
└─────────────────────────────────────────────────────────┘
```

#### **Profile Content (User-Facing)**
```
┌──────────────────────────────────────────────────────────┐
│ FIELD NAME           │ TYPE        │ MAX LENGTH         │
├──────────────────────────────────────────────────────────┤
│ Mini Bio             │ Rich Text   │ 150 chars (card)   │
│ Full Bio             │ Rich Text   │ 500 chars (page)   │
│ Profile Photo        │ File        │ 800x800px min      │
│ Specializations      │ Multi       │ 5-7 tags           │
│ Modalities           │ Multi       │ 3-5 tags           │
│ Languages            │ Multi       │ All spoken         │
└──────────────────────────────────────────────────────────┘
```

**Mini Bio Guidelines** (For therapists to self-update):
```
✅ GOOD EXAMPLE (145 chars):
"Trauma-informed psychologist with 10+ years supporting LGBTQI+ 
individuals. I use EMDR and somatic approaches in a warm, 
non-judgmental space."

❌ BAD EXAMPLE (too long, vague):
"I am a registered psychologist who has been practicing for many 
years and I work with a wide range of issues including anxiety, 
depression, relationships, trauma..."

📝 TEMPLATE FOR THERAPISTS:
"[Your approach] [qualification] with [X years] supporting 
[who you help best]. I use [methods] in a [your space/style]."
```

### 2.2 Location Fields (National Architecture)

```
┌──────────────────────────────────────────────────────────┐
│ FIELD NAME         │ TYPE     │ PURPOSE                 │
├──────────────────────────────────────────────────────────┤
│ State/Territory    │ Select   │ NSW,VIC,QLD,SA,WA,TAS   │
│                    │          │ NT,ACT                   │
│ Primary Region     │ Select   │ Metro/Regional/Remote   │
│ Service Area       │ Select   │ City/Suburb name        │
│ Postcode           │ Text     │ For proximity search    │
│ Service Type       │ Multi    │ In-person/Online/Phone  │
│ Accepts Clients    │ Multi    │ States served (online)  │
│ From                                                      │
└──────────────────────────────────────────────────────────┘
```

**Location Logic:**
- **In-person:** Match by State → Region → Postcode proximity
- **Online:** Match by State preference → Availability
- **Hybrid:** Show both, prioritize in-person if close

### 2.3 Matching Criteria Fields

```
┌──────────────────────────────────────────────────────────┐
│ FIELD NAME           │ TYPE        │ MATCHING WEIGHT    │
├──────────────────────────────────────────────────────────┤
│ Primary Expertise    │ Multi       │ 40% (CRITICAL)     │
│ Client Age Groups    │ Multi       │ 30% (HIGH)         │
│ Modalities           │ Multi       │ 15% (MEDIUM)       │
│ Gender               │ Select      │ 10% (PREFERENCE)   │
│ Lived Experience     │ Multi       │ 5% (BONUS)         │
│ Accessibility        │ Multi       │ 0% (FILTER)        │
│ Languages            │ Multi       │ 0% (FILTER)        │
│ Bulk Billing         │ Checkbox    │ 0% (FILTER)        │
│ Accepting New        │ Checkbox    │ 0% (FILTER)        │
│ Clients                                                   │
└──────────────────────────────────────────────────────────┘
```

**Matching Algorithm (Simplified):**
```javascript
function calculateMatch(client, therapist) {
  let score = 0;
  
  // 40% - Primary Expertise Match
  if (hasOverlap(client.concerns, therapist.expertise)) {
    score += 40;
  }
  
  // 30% - Age Group Match
  if (therapist.ageGroups.includes(client.ageGroup)) {
    score += 30;
  }
  
  // 15% - Modality Preference
  if (client.modality && therapist.modalities.includes(client.modality)) {
    score += 15;
  }
  
  // 10% - Gender Preference
  if (!client.genderPref || therapist.gender === client.genderPref) {
    score += 10;
  }
  
  // 5% - Lived Experience Bonus
  if (hasOverlap(client.identity, therapist.livedExperience)) {
    score += 5;
  }
  
  return score; // 0-100
}
```

### 2.4 Administrative Fields

```
┌──────────────────────────────────────────────────────────┐
│ FIELD NAME           │ TYPE        │ PURPOSE            │
├──────────────────────────────────────────────────────────┤
│ Status               │ Select      │ Pending/Verified/  │
│                      │             │ Published/Archived │
│ Published            │ Checkbox    │ Public visibility  │
│ Profile URL          │ Rich Text   │ /therapist/slug    │
│ Onboarding Token     │ Rich Text   │ Magic link auth    │
│ Token Expiry         │ Date        │ Security           │
│ Last Updated         │ Date        │ Freshness tracking │
│ Admin Notes          │ Rich Text   │ Internal only      │
│ Registration Number  │ Rich Text   │ AHPRA verification │
│ Insurance Confirmed  │ Checkbox    │ Compliance         │
└──────────────────────────────────────────────────────────┘
```

---

## 3. Therapist Profile Standards

### 3.1 Card Display Template

**Visual Structure:**
```
┌─────────────────────────────────────────────────┐
│ [Profile Photo]    Dr. Sarah Chen (she/her)     │
│  150x150px         Clinical Psychologist        │
│                                                  │
│  ⭐⭐⭐⭐⭐ 15 years experience                    │
│                                                  │
│  "Trauma-informed care for LGBTQI+ individuals │
│   using EMDR and somatic approaches."           │
│                                                  │
│  🏷️ Trauma  🏷️ LGBTQI+  🏷️ EMDR                │
│                                                  │
│  📍 Melbourne CBD  •  💻 Online  •  💰 $$       │
│                                                  │
│  [View Profile]          [Book Now]             │
└─────────────────────────────────────────────────┘
```

**Required Elements:**
1. ✅ Professional photo (800x800px minimum)
2. ✅ Full name + pronouns
3. ✅ Professional title/qualification
4. ✅ Mini bio (150 chars max)
5. ✅ Top 3-5 specialization tags
6. ✅ Location + service type
7. ✅ Price indicator ($-$$$)
8. ✅ Availability status

### 3.2 Full Profile Page Template

```markdown
# Dr. Sarah Chen, Clinical Psychologist

## About
[Full Bio - 500 chars]
"I'm a trauma-informed clinical psychologist with 15 years of 
experience supporting LGBTQI+ individuals, trauma survivors, and 
those exploring their identity. I use EMDR, somatic experiencing, 
and parts work to help clients heal and thrive. My practice is a 
warm, non-judgmental space where you can be yourself."

## What I Can Help With
• Trauma & PTSD
• LGBTQI+ identity exploration
• Relationship challenges
• Anxiety & depression
• Sexual assault recovery

## How I Work
**Approach:** Trauma-informed, person-centered
**Modalities:** EMDR, Somatic Experiencing, IFS
**Session Length:** 50 minutes
**Languages:** English, Mandarin

## Practical Details
**Location:** Melbourne CBD (In-person)
**Also Available:** Online (all of VIC)
**Fee:** $180/session
**Rebates:** Medicare rebate available
**Accepting New Clients:** ✅ Yes

## Qualifications
• M.Psych (Clinical), University of Melbourne
• AHPRA Registration: PSY0001234567
• Member: APS, EMDRAA

## Contact
[Book Appointment] [Send Message] [Visit Website]
```

### 3.3 Bio Writing Guidelines (For Therapists)

**Template for Self-Service Updates:**
```
Step 1: Your Approach & Credentials (Who you are)
"I'm a [approach]-focused [qualification]..."
Examples:
✅ "trauma-informed clinical psychologist"
✅ "person-centered counsellor"
✅ "integrative psychotherapist"

Step 2: Experience & Who You Help (Your specialty)
"...with [X years] supporting [specific groups]..."
Examples:
✅ "with 10+ years supporting LGBTQI+ individuals"
✅ "with 5 years specializing in perinatal mental health"

Step 3: Methods (How you work)
"I use [modalities]..."
Examples:
✅ "I use EMDR and somatic approaches"
✅ "I integrate CBT, ACT, and mindfulness"

Step 4: Environment (Your therapeutic space)
"...in a [describe your style/space]."
Examples:
✅ "in a warm, non-judgmental space"
✅ "with a down-to-earth, collaborative approach"

FINAL EXAMPLE:
"I'm a trauma-informed clinical psychologist with 10+ years 
supporting LGBTQI+ individuals and trauma survivors. I use EMDR 
and somatic approaches in a warm, non-judgmental space where you 
can be yourself."
```

---

## 4. Search & Matching Strategy

### 4.1 Questionnaire Design (Minimal Friction)

**CRITICAL PRINCIPLE:** Ask only what significantly improves match quality

**Optimal Flow: 3-5 Questions Maximum**

```
┌─────────────────────────────────────────────────────────┐
│ QUESTION SEQUENCE (Based on Elimination & Weighting)   │
└─────────────────────────────────────────────────────────┘

Q1: LOCATION (Eliminates 70-90% immediately)
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
"Where are you looking for support?"

[Dropdown: State/Territory]
└─> If VIC/NSW/QLD: [Dropdown: Metro region or Online]
└─> If Other: "Online only" (auto-set)

🎯 WHY FIRST: Immediately filters to relevant therapists
⚡ ELIMINATES: ~80% of database if location-specific
💡 SMART: Auto-detect location, pre-fill, allow change

Q2: WHO & AGE (Essential Filtering - 30% weight)
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
"Who needs support?"

[ ] Just me (adult 18+)
[ ] Me and my partner(s)
[ ] My child/teen (under 18)
[ ] Someone else

🎯 WHY: Therapists specialized in different age groups
⚡ ELIMINATES: ~40-60% depending on selection
💡 CONDITIONAL: If "child/teen" → ask age range

Q3: MAIN CONCERN (Primary Matching - 40% weight)
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
"What brings you to therapy?" (Multi-select OK)

[ ] Anxiety or depression
[ ] Trauma or PTSD
[ ] Relationship challenges
[ ] LGBTQI+ identity support
[ ] Neurodiversity (ADHD, autism)
[ ] Addiction or recovery
[ ] Grief and loss
[ ] Life transitions
[ ] Not sure / Exploring

🎯 WHY: Core matching criteria - expertise alignment
⚡ MATCHING: Weighted by therapist primary expertise
💡 ALLOW: Multiple selections (realistic)

Q4: PREFERENCE (OPTIONAL - 10% weight)
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
"Any preferences? (Optional - we'll still find great matches)"

Therapist Gender:
( ) No preference  ( ) Female  ( ) Male  ( ) Non-binary

Approach:
[ ] CBT/skills-focused
[ ] Trauma-specific (EMDR, SE)
[ ] Psychodynamic/talk therapy
[ ] Creative/art-based
[ ] No preference

🎯 WHY: Refinement only - not eliminatory
⚡ SKIPPABLE: Clearly marked as optional
💡 DEFAULT: "No preference" if skipped

Q5: PRACTICAL (Filters Only)
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
"Anything else we should know?"

[ ] Bulk billing / Medicare preferred
[ ] Wheelchair accessible required
[ ] Need language: [Dropdown]
[ ] Telehealth only

🎯 WHY: Hard requirements, not preferences
⚡ FILTERS: Shows/hides therapists binary
💡 OPTIONAL: Only show if checked
```

### 4.2 Alternative: Progressive Disclosure

**For users who want more control:**

```
TIER 1: Essential (Always ask)
├─ Location
├─ Who needs support
└─ Main concern

TIER 2: Helpful (Show 3 results, then offer refinement)
"We found 12 therapists who might be a good fit!
 Want to refine your search?"

├─ [Refine] → Shows Q4 & Q5
└─ [See matches] → Show results

TIER 3: Browse (Results page filters)
"Filter results" sidebar:
├─ Gender preference
├─ Modalities
├─ Price range
├─ Availability
└─ Languages
```

### 4.3 Results Display Strategy

**Prioritization Algorithm:**
```
1. FILTER (Binary - show/hide)
   ├─ Location match
   ├─ Age group match
   ├─ Accepting new clients
   └─ Hard requirements (language, accessibility)

2. SCORE (0-100 points)
   ├─ Primary concern match (40 pts)
   ├─ Age group fit (30 pts)
   ├─ Modality preference (15 pts)
   ├─ Gender preference (10 pts)
   └─ Lived experience bonus (5 pts)

3. SORT
   ├─ Primary: Match score (desc)
   ├─ Secondary: Recently updated (desc)
   └─ Tertiary: Alphabetical

4. DISPLAY
   ├─ Show top 3-5 immediately
   ├─ "See more matches" for 6-20
   └─ "Browse all therapists" for full directory
```

**Result Card Display:**
```
[Score: 92/100] ⭐⭐⭐⭐⭐ Excellent match

Dr. Sarah Chen (she/her)
Clinical Psychologist • 15 years experience

"Trauma-informed care for LGBTQI+ individuals using EMDR..."

✓ Specializes in: Trauma, LGBTQI+, Identity
✓ Available: In-person (Melbourne) + Online
✓ Accepts: Medicare rebates

[View Full Profile]  [Book Consultation]

Why we matched you:
• ✅ Expertise in trauma & LGBTQI+ support
• ✅ Uses EMDR (your preference)
• ✅ Melbourne-based (in-person available)
```

---

## 5. Location Architecture (National Scale)

### 5.1 State/Territory Structure

```
Database Structure:
┌─────────────────────────────────────────────────────────┐
│ State/Territory (Select - Single Choice)                │
├─────────────────────────────────────────────────────────┤
│ • New South Wales (NSW)                                 │
│ • Victoria (VIC)                                        │
│ • Queensland (QLD)                                      │
│ • South Australia (SA)                                  │
│ • Western Australia (WA)                                │
│ • Tasmania (TAS)                                        │
│ • Northern Territory (NT)                               │
│ • Australian Capital Territory (ACT)                    │
│ • Online (National) - for telehealth-only practitioners │
└─────────────────────────────────────────────────────────┘
```

### 5.2 Regional Classification

**Metro vs. Regional Smart Categorization:**

```
METRO AREAS (Auto-categorize by postcode):
┌────────────┬──────────────────────────────────────────┐
│ State      │ Metro Postcodes                          │
├────────────┼──────────────────────────────────────────┤
│ NSW        │ 2000-2234 (Sydney)                       │
│            │ 2280-2304 (Newcastle)                    │
│            │ 2500-2534 (Wollongong)                   │
│ VIC        │ 3000-3207 (Melbourne)                    │
│            │ 3212-3220 (Geelong)                      │
│ QLD        │ 4000-4179 (Brisbane)                     │
│            │ 4810-4815 (Townsville)                   │
│            │ 4870-4879 (Cairns)                       │
│ SA         │ 5000-5199 (Adelaide)                     │
│ WA         │ 6000-6199 (Perth)                        │
│ TAS        │ 7000-7019 (Hobart)                       │
│ ACT        │ 2600-2620 (Canberra)                     │
│ NT         │ 0800-0822 (Darwin)                       │
└────────────┴──────────────────────────────────────────┘

REGIONAL/REMOTE: Everything else
```

### 5.3 Proximity Search (For In-Person)

**Algorithm:**
```javascript
function findNearbyTherapists(clientPostcode, maxDistance = 25) {
  // Step 1: Get client coordinates
  const clientCoords = geocodePostcode(clientPostcode);
  
  // Step 2: Filter therapists offering in-person
  const inPersonTherapists = therapists.filter(t => 
    t.serviceType.includes('In-person') &&
    t.acceptingClients === true
  );
  
  // Step 3: Calculate distances
  const withDistance = inPersonTherapists.map(therapist => ({
    ...therapist,
    distance: calculateDistance(
      clientCoords,
      geocodePostcode(therapist.postcode)
    )
  }));
  
  // Step 4: Filter by max distance
  const nearby = withDistance.filter(t => t.distance <= maxDistance);
  
  // Step 5: Sort by distance
  return nearby.sort((a, b) => a.distance - b.distance);
}
```

**User-Friendly Display:**
```
📍 Within 5km:   3 therapists
📍 Within 10km:  8 therapists
📍 Within 25km:  15 therapists
💻 Online (VIC):  47 therapists

[Expand search radius] [Show online therapists]
```

### 5.4 Online/Telehealth Handling

**State Licensing Logic:**
```
IF therapist.serviceType includes "Online":
  therapist.canServe = therapist.state  // Only their registered state
  
  EXCEPTION: Multi-state registration
  IF therapist.multiStateRegistration === true:
    therapist.canServe = therapist.registeredStates  // Array
```

**Client Search:**
```
IF client.preference === "Online":
  SHOW: therapists where:
    - serviceType includes "Online"
    - canServe includes client.state
  SORT BY: Match score, not distance
```

---

## 6. National Scalability Framework

### 6.1 Database Growth Strategy

**Phase 1: Victoria (CURRENT)**
```
Target: 200-500 therapists
Focus: Melbourne metro + regional VIC
Timeline: Q4 2025
```

**Phase 2: East Coast Expansion**
```
Target: 1,000-2,000 therapists
States: VIC, NSW, QLD
Focus: Major metros (Sydney, Brisbane, Melbourne)
Timeline: Q1-Q2 2026
```

**Phase 3: National Coverage**
```
Target: 3,000-5,000 therapists
States: All Australian states/territories
Focus: Metro + major regional centers
Timeline: Q3-Q4 2026
```

**Phase 4: Comprehensive**
```
Target: 10,000+ therapists
Coverage: Urban, regional, remote (telehealth)
Timeline: 2027+
```

### 6.2 Performance Optimization (At Scale)

**Database Architecture:**
```
PRIMARY DATABASE: Notion (Admin, Therapist Management)
├─ Therapist onboarding
├─ Profile updates
├─ Verification workflow
└─ Admin notes & compliance

SEARCH INDEX: Algolia/Typesense (Public Search)
├─ Sync from Notion via API
├─ Real-time search
├─ Faceted filtering
├─ Geo-search
└─ Match scoring

CACHING LAYER: Redis (Performance)
├─ Popular searches
├─ Location data
├─ Match results (15 min TTL)
└─ Profile pages (5 min TTL)
```

**Sync Strategy:**
```
┌──────────────────────────────────────────────────────────┐
│ WHEN                  │ ACTION                           │
├──────────────────────────────────────────────────────────┤
│ Therapist updates     │ Immediate sync to search index   │
│ profile               │ (webhook trigger)                │
│ Admin publishes       │ Immediate sync + cache clear     │
│ Nightly (3am)         │ Full database sync (backup)      │
│ Weekly (Sunday)       │ Reindex everything (data integrity)│
└──────────────────────────────────────────────────────────┘
```

### 6.3 Data Quality at Scale

**Automated Quality Checks:**
```
DAILY CHECKS (Automated):
├─ Email validity
├─ Website availability (200 status)
├─ Profile photo exists
├─ Mini bio length (50-150 chars)
├─ Full bio length (200-500 chars)
├─ Required fields complete
└─ Last updated < 180 days

WEEKLY CHECKS (Flagged for review):
├─ AHPRA registration status
├─ Insurance expiry dates
├─ Broken social media links
└─ Duplicate profiles

MONTHLY CHECKS (Admin review):
├─ Inactive therapists (no bookings 90+ days)
├─ Outdated information
└─ Client feedback patterns
```

**Therapist Self-Service Portal:**
```
Features:
✅ Update bio & photo anytime
✅ Toggle "accepting new clients"
✅ Update availability
✅ Add/remove specializations
✅ View analytics (profile views, clicks)
✅ Manage booking preferences

Admin approval required for:
❌ Changing name or qualification
❌ Adding new modalities
❌ Changing registration number
❌ Major expertise changes
```

---

## 7. Competitor Analysis

### 7.1 BetterHelp (Global Leader)

**What They Do Well:**
- ✅ Simple 5-question onboarding
- ✅ Instant matching (algorithm-based)
- ✅ Standardized therapist bios
- ✅ Built-in messaging platform
- ✅ Subscription model (predictable pricing)

**What We Do Better:**
- ✅ Local, in-person options (not just online)
- ✅ Specialized LGBTQI+/neurodiversity focus
- ✅ Australian context & Medicare integration
- ✅ Therapist independence (not employees)
- ✅ Transparent pricing (no subscriptions)

### 7.2 Psychology Today (Directory Leader)

**What They Do Well:**
- ✅ Comprehensive filtering (20+ criteria)
- ✅ Self-service therapist profiles
- ✅ Location-based search with maps
- ✅ Insurance/payment filtering
- ✅ Detailed therapist bios

**What We Do Better:**
- ✅ Guided matching (vs. overwhelming choice)
- ✅ Inclusive language & categories
- ✅ Mini bios (vs. walls of text)
- ✅ Match scoring (vs. just browse)
- ✅ Modern UX (their UI is dated)

### 7.3 Headspace/Beyond Blue (Au-focused)

**What They Do Well:**
- ✅ Trusted Australian brand
- ✅ Mental health education
- ✅ Crisis support integration
- ✅ Government partnerships
- ✅ Free/low-cost options

**What We Do Better:**
- ✅ Actual therapist matching (not just referrals)
- ✅ Private practice network (more specialized)
- ✅ Inclusive/affirming focus
- ✅ Transparent therapist profiles
- ✅ Choice & agency for clients

---

## 8. Implementation Roadmap

### Phase 1: Immediate (This Month)

**Week 1-2: Database Refinement**
- [ ] Add "Mini Bio" field (rich text, 150 char max)
- [ ] Add "Pronouns" field (rich text)
- [ ] Create "Service Area" select with Melbourne regions
- [ ] Create therapist bio writing guide
- [ ] Update profile template page in Notion

**Week 3-4: Matching Foundation**
- [ ] Map existing expertise to standardized taxonomy
- [ ] Create weighted matching formula
- [ ] Build simple match calculator (spreadsheet/script)
- [ ] Test with 10 sample client scenarios
- [ ] Document match algorithm

### Phase 2: Next Quarter (Jan-Mar 2026)

**Month 1: Search Infrastructure**
- [ ] Set up Algolia/Typesense account
- [ ] Sync Notion database to search index
- [ ] Implement location-based filtering
- [ ] Build search API endpoint
- [ ] Test search performance

**Month 2: Questionnaire Build**
- [ ] Design 3-question MVP quiz
- [ ] Build quiz UI component
- [ ] Implement match scoring logic
- [ ] A/B test question variations
- [ ] Optimize for completion rate

**Month 3: Profile Pages**
- [ ] Design profile page template
- [ ] Build dynamic profile generator
- [ ] Implement booking integration
- [ ] Launch therapist self-service portal
- [ ] Beta test with 20 therapists

### Phase 3: National Expansion (Apr-Dec 2026)

**Q2: NSW Launch**
- [ ] Recruit 200+ NSW therapists
- [ ] Add Sydney metro regions to database
- [ ] Update search to handle multi-state
- [ ] Marketing campaign (NSW)

**Q3: QLD Launch**
- [ ] Recruit 150+ QLD therapists
- [ ] Add Brisbane regions
- [ ] Build state-specific landing pages
- [ ] SEO optimization per state

**Q4: National Coverage**
- [ ] Add remaining states (SA, WA, TAS, NT, ACT)
- [ ] Target 3,000+ therapists nationally
- [ ] Launch national marketing campaign
- [ ] Partnership with mental health orgs

---

## 9. Questions for Clarification

### Business Model
1. **Pricing Structure:**
   - Will therapists pay to be listed? (Directory fee)
   - Will clients pay for matching? (Service fee)
   - Commission on bookings?
   - Freemium model?

2. **Booking Integration:**
   - Build in-platform booking system?
   - Link to therapist's existing booking system?
   - Email introduction only?

### Technical
3. **Search Infrastructure:**
   - Budget for Algolia/Typesense? (~$50-200/month)
   - Comfort level with API integrations?
   - Need real-time updates or daily sync OK?

4. **Profile Photos:**
   - Therapist uploads directly to Notion?
   - External hosting (Cloudinary, S3)?
   - Photo requirements (headshot only, candid OK)?

### Scope
5. **National Expansion Timeline:**
   - Must have all states by end of 2026?
   - Or progressive rollout 1-2 states at a time?
   - International 2027 confirmed?

6. **Features Priority:**
   - Most important: Search quality, booking ease, therapist bios?
   - Can we launch MVP without all features?
   - What's the minimum for first launch?

---

## 10. Appendix: Field Definitions

### A. Primary Expertise (Standardized Taxonomy)

**Mental Health Conditions:**
- Anxiety & Panic Disorders
- Depression & Mood Disorders
- Trauma & PTSD
- OCD & Related Disorders
- Eating Disorders
- Addiction & Substance Use
- Psychosis & Bipolar
- Personality Disorders

**Life Stages & Transitions:**
- Perinatal & Postpartum
- Parenting & Family
- Adolescence & Teen Issues
- Mid-life Transitions
- Aging & Elder Care
- Grief & Loss
- Career & Burnout

**Identity & Relationships:**
- LGBTQI+ Identity & Coming Out
- Gender Exploration & Transition
- Relationship Counseling (Individual)
- Couples Therapy
- Non-monogamy & ENM
- Sexuality & Intimacy
- Cultural Identity

**Neurodiversity & Disability:**
- ADHD Support
- Autism Spectrum
- Learning Differences
- Chronic Illness & Pain
- Disability Adjustment

**Specialized Areas:**
- Sexual Assault & DV Recovery
- Workplace Issues
- Sports Psychology
- Creative Blocks
- Religious/Spiritual
- Migration & Settlement

### B. Modalities (Therapeutic Approaches)

**Cognitive & Behavioral:**
- CBT (Cognitive Behavioral Therapy)
- DBT (Dialectical Behavior Therapy)
- ACT (Acceptance & Commitment Therapy)
- Exposure Therapy
- Motivational Interviewing

**Trauma-Focused:**
- EMDR (Eye Movement Desensitization)
- Somatic Experiencing
- Sensorimotor Psychotherapy
- Trauma-Focused CBT
- CPT (Cognitive Processing Therapy)

**Psychodynamic:**
- Psychoanalytic Therapy
- Attachment-Based Therapy
- Jungian Therapy
- Transactional Analysis

**Integrative & Holistic:**
- IFS (Internal Family Systems)
- Gestalt Therapy
- Existential Therapy
- Narrative Therapy
- Solution-Focused Brief Therapy

**Creative & Body-Based:**
- Art Therapy
- Music Therapy
- Play Therapy (for children)
- Dance/Movement Therapy
- Equine Therapy

**Culturally Responsive:**
- Culturally Adapted Therapy
- Indigenous Healing Practices
- Feminist Therapy
- Queer-Affirmative Therapy

---

## 11. Success Metrics & KPIs

### User Experience Metrics
```
Questionnaire:
├─ Completion Rate: Target 65%+
├─ Time to Complete: Target <90 seconds
├─ Drop-off Point: Monitor each question
└─ Refinement Rate: % who use filters

Search Quality:
├─ Click-Through Rate: Target 40%+ on top 3 results
├─ Profile Views per Search: Target 3-5
├─ Booking Rate: Target 15%+ from profile view
└─ Match Satisfaction: Survey target 8.5/10

Performance:
├─ Search Response Time: <500ms
├─ Page Load Time: <2s
├─ Mobile Usability Score: 95+
└─ Uptime: 99.9%
```

### Business Metrics
```
Therapist Growth:
├─ Monthly New Therapists: Track trend
├─ Profile Completion Rate: Target 90%+
├─ Active Profiles: % accepting clients
└─ Churn Rate: Target <5%/year

Client Engagement:
├─ Monthly Searches: Track growth
├─ Return Visitors: Target 30%+
├─ Referral Rate: Target 25%+
└─ Newsletter Signups: Track conversion

Revenue (if applicable):
├─ Therapist Subscriptions: Monthly recurring
├─ Booking Commissions: Per transaction
├─ Average Revenue Per Therapist: Monthly
└─ Customer Acquisition Cost: Per therapist/client
```

---

## 12. Next Steps & Action Items

### Immediate Actions (This Week)
1. **Review this document** with stakeholders
2. **Answer clarification questions** (Section 9)
3. **Prioritize features** for MVP vs. future
4. **Set timeline** for national expansion
5. **Assign ownership** of each phase

### Database Tasks (Next 2 Weeks)
1. Add "Mini Bio" and "Pronouns" fields
2. Create bio writing guide for therapists
3. Audit existing profiles for completeness
4. Test match scoring with sample scenarios
5. Document current state vs. target state

### Technical Planning (Next Month)
1. Research search infrastructure (Algolia vs. Typesense)
2. Design questionnaire UI/UX
3. Plan API architecture
4. Scope booking integration options
5. Create technical specifications document

---

**Document Version:** 1.0  
**Last Updated:** October 14, 2025  
**Next Review:** January 1, 2026  
**Owner:** Therapair Product Team

---

*This is a living document. Update as platform evolves and new insights emerge.*

