# Therapair: Product Roadmap
**Building the Future of Therapy Matching**

---

## Vision

**Transform Therapair from an MVP widget into a global AI-native platform that matches millions of people with their ideal therapist, continuously learns from outcomes, and becomes the standard for intelligent mental health care access.**

---

## Roadmap Overview

```
2025 Q4          2026 Q1-Q2       2026 Q3-Q4       2027-2028        2029+
──────────────────────────────────────────────────────────────────────
   MVP           Validation        Pilot          Scale          Platform
   ✓             ►                              
   
   Demo          User Testing      5-10 Clinics   100+ Clinics   Global
   Widget        + Refinement      Paying         1000+ Therapists
```

---

## Phase 0: MVP (Q4 2025) ✅ COMPLETE

### Goal
**Prove the matching concept works with a live partnership**

### Deliverables
- [x] Core matching algorithm (9-question quiz)
- [x] Unison Mental Health partnership widget
- [x] Landing page with interest capture
- [x] AI-powered confirmation emails
- [x] Notion CRM integration
- [x] Professional email templates
- [x] Mobile-responsive design
- [x] Australian English localisation

### Metrics
- ✅ Widget live on 1 partner site
- ✅ Landing page capturing 4 audience types
- ✅ 8 therapists in matching database
- ✅ 100% email delivery rate
- ✅ Professional brand established

---

## Phase 1: Validation & Refinement (Nov 2025 - Mar 2026)

### Goal
**Gather user data, refine matching quality, secure pilot clinic partners**

### Key Initiatives

#### 1. User Research & Testing
- [ ] Recruit 50+ users to complete matching quiz
- [ ] Conduct 10 user interviews (recorded, transcribed)
- [ ] Track completion rates, drop-off points
- [ ] Gather qualitative feedback on:
  - Quiz question clarity
  - Match relevance
  - Booking process friction
- [ ] Iterate on quiz questions based on feedback

**Metrics:**
- 80%+ quiz completion rate
- 4+ average satisfaction rating (out of 5)
- 60%+ users say matches felt "very relevant"

#### 2. Therapist Feedback
- [ ] Interview 20 therapists (Unison + external)
- [ ] Understand their ideal client profile
- [ ] Identify what factors they consider most important
- [ ] Validate matching logic against clinical expertise
- [ ] Gather pricing feedback (what would they pay?)

**Metrics:**
- 15+ therapists expressing interest in joining platform
- 3+ clinics requesting pilot program info

#### 3. Match Quality Tracking
- [ ] Add post-booking feedback form
- [ ] Track:
  - Did user book with recommended therapist?
  - First session no-show rate
  - Client satisfaction after first session
  - Therapist satisfaction with match
- [ ] Build simple dashboard (Google Sheets → Later: Mixpanel)

**Metrics:**
- 50%+ booking conversion from match results
- <10% first-session no-show rate
- 80%+ mutual satisfaction (client + therapist)

#### 4. Clinic Partnership Development
- [ ] Create clinic pitch deck
- [ ] Identify 20 target clinics (Melbourne, Sydney, Brisbane)
- [ ] Outreach campaign (email + LinkedIn)
- [ ] Offer 3-month free pilot
- [ ] Secure 3-5 signed LOIs (Letter of Intent)

**Metrics:**
- 20 clinic meetings booked
- 5 pilot agreements signed
- 1 paid pilot ($99/month early adopter pricing)

#### 5. Analytics & Tracking
- [ ] Implement Google Analytics 4
- [ ] Set up conversion funnels
- [ ] Track key events:
  - Quiz start
  - Quiz completion
  - Booking click
  - Form submission
- [ ] Weekly metrics review

**Success Criteria:**
- 100+ quiz completions
- 20+ booking requests
- 3-5 pilot clinic partners confirmed
- Match quality validated (80%+ satisfaction)

---

## Phase 2: Pilot Program (Apr - Sep 2026)

### Goal
**Prove ROI for clinics, establish pricing model, achieve product-market fit**

### Key Initiatives

#### 1. White-Label Widget Development
- [ ] Multi-tenant architecture (one widget, multiple clinics)
- [ ] Clinic branding customization (colors, logo, domain)
- [ ] Custom therapist database per clinic
- [ ] Booking integration with clinic calendars (Calendly, Acuity)
- [ ] Admin dashboard for clinics:
  - View bookings
  - Manage therapist profiles
  - See analytics (matches, conversions)

**Tech Stack:**
- React + Next.js (frontend)
- Node.js + PostgreSQL (backend)
- Clerk (auth)
- Vercel (hosting)

#### 2. Therapist Onboarding System
- [ ] Self-serve profile creation
- [ ] Bio/photo upload
- [ ] Specialty selection (from curated list)
- [ ] Availability management
- [ ] Verification process (license check)
- [ ] Onboarding email sequence

#### 3. Payment Integration
- [ ] Stripe subscription billing
- [ ] Pricing tiers:
  - **Starter:** $99/month (1-5 therapists)
  - **Growth:** $199/month (6-15 therapists)
  - **Enterprise:** $499/month (16+ therapists)
- [ ] Free trial (14 days, no credit card)
- [ ] Automated invoicing

#### 4. Advanced Analytics
- [ ] Clinic dashboard:
  - Daily/weekly/monthly matches
  - Booking conversion rates
  - Revenue attribution (if applicable)
  - Therapist performance (bookings per therapist)
- [ ] Therapist dashboard:
  - Matches received
  - Booking requests
  - Profile views
- [ ] Admin analytics:
  - Churn prediction
  - Feature usage
  - Support ticket trends

#### 5. Marketing & Sales
- [ ] Case study from Unison partnership
- [ ] SEO content (blog posts on therapy matching)
- [ ] LinkedIn outreach campaign
- [ ] Conference attendance (mental health tech events)
- [ ] Referral program (clinic refers clinic = 1 month free)

**Metrics:**
- 5-10 paying clinics
- $2K-5K MRR
- 50-100 therapists on platform
- 200+ matches per month
- <10% monthly churn
- 4+ NPS score

#### 6. Mobile App (React Native)
- [ ] Client-facing app for taking quiz
- [ ] View therapist profiles
- [ ] Book sessions
- [ ] Session reminders
- [ ] iOS + Android

**Success Criteria:**
- 10 paying clinics ($5K+ MRR)
- Product-market fit validated (low churn, high referrals)
- ROI proven for clinics (5x booking increase vs. old system)
- Funding secured ($150K-300K pre-seed)

---

## Phase 3: Scale (Oct 2026 - Dec 2027)

### Goal
**Reach 100+ clinics, 1000+ therapists, achieve profitability**

### Key Initiatives

#### 1. Self-Service Onboarding
- [ ] Automated clinic signup (no sales call required)
- [ ] Instant widget provisioning
- [ ] Interactive onboarding tutorial
- [ ] Email drip campaign for activation
- [ ] Chatbot support (Intercom or Drift)

#### 2. Marketplace for Individual Therapists
- [ ] Allow solo practitioners to join (not just clinics)
- [ ] Pricing: $29/month per therapist
- [ ] Public-facing directory (therapair.com/therapists)
- [ ] Client-initiated matching (reverse flow)

#### 3. Advanced AI Matching v2.0
- [ ] Machine learning model trained on booking outcomes
- [ ] Vector embeddings for semantic matching
- [ ] A/B testing of algorithms
- [ ] Continuous learning pipeline
- [ ] Explainability (show why therapist was matched)

**Research Partners:**
- University research labs (publish efficacy studies)
- Clinical psychologists as advisors

#### 4. Video Integration
- [ ] Embedded telehealth (Zoom/Doxy.me integration)
- [ ] Seamless booking → session flow
- [ ] HIPAA-compliant video solution
- [ ] Session recording (with consent, for training)

#### 5. Insurance Automation
- [ ] Verify insurance coverage in real-time
- [ ] Check out-of-network benefits
- [ ] Estimate client cost per session
- [ ] Partner with insurance companies for direct billing

#### 6. Outcome Measurement
- [ ] Standardised assessments (PHQ-9, GAD-7, etc.)
- [ ] Pre/post session tracking
- [ ] Therapist effectiveness metrics
- [ ] Match quality improvement via feedback loop

#### 7. International Expansion
- [ ] UK launch (adapt for NHS context)
- [ ] Canada launch (bilingual: English/French)
- [ ] Localisation: currency, language, cultural norms
- [ ] Therapist licensure verification per country

#### 8. Enterprise Sales
- [ ] Target hospital systems, universities, EAP providers
- [ ] Custom contracts ($10K-50K/year)
- [ ] Dedicated account management
- [ ] White-label with API access

**Metrics:**
- 100 paying clinics
- 1000 therapists on platform
- 5000 matches per month
- $50K+ MRR
- Break-even or profitable
- <5% monthly churn
- 50+ NPS score

**Success Criteria:**
- Profitability achieved
- Strong brand recognition in mental health tech
- Seed funding secured ($1M-2M) for Phase 4
- Waiting list of clinics wanting to join

---

## Phase 4: AI-Native Platform (2028-2029)

### Goal
**Become the global standard for therapy matching with agentic automation**

### Key Initiatives

#### 1. Agentic Workflows
**Autonomous Profile Enhancement:**
- AI extracts specialties from therapist bios/CVs
- Auto-tags skills, approaches, certifications
- Suggests profile improvements
- Keeps profiles up-to-date

**Intelligent Scheduling:**
- Predicts therapist availability
- Coordinates timezones automatically
- Suggests optimal booking times
- Auto-reschedules when conflicts arise

**Proactive Client Support:**
- Pre-session preparation emails (personalised)
- Post-session check-ins (with consent)
- Dropout risk prediction (proactive outreach)
- Resource recommendations (articles, tools)

**Continuous Match Optimisation:**
- Learn from every booking, session, outcome
- A/B test matching algorithms in real-time
- Personalise quiz questions based on user type
- Predict which therapist will have best outcomes for specific client

#### 2. Advanced AI Features
- [ ] Conversational AI intake (ChatGPT-style quiz)
- [ ] Voice-based quiz (for accessibility)
- [ ] Multi-language support (10+ languages)
- [ ] Cultural adaptation (different quiz flows per culture)
- [ ] Sentiment analysis on client feedback
- [ ] NLP-powered profile search

#### 3. Outcome-Based Matching
- [ ] Track therapeutic alliance scores
- [ ] Measure symptom improvement (PHQ-9, GAD-7)
- [ ] Retention rates per therapist-client pairing
- [ ] Use outcomes to refine matching weights
- [ ] Publish research on matching effectiveness

#### 4. B2B2C Partnerships
- [ ] Partner with EAP providers (access 10M+ employees)
- [ ] University counselling centres (1000+ campuses)
- [ ] Employer wellness programs
- [ ] Insurance companies (preferred provider networks)

#### 5. Research & Development
- [ ] Publish peer-reviewed studies on matching efficacy
- [ ] Partner with academic institutions
- [ ] Contribute to mental health tech standards
- [ ] Open-source parts of matching algorithm

**Metrics:**
- 1000+ clinics
- 10,000+ therapists
- 50,000+ matches per month
- $500K+ MRR
- International presence (3+ countries)
- Industry-leading NPS (60+)

---

## Phase 5: Global Platform (2030+)

### Goal
**Transform mental health care delivery globally, 10M+ users matched**

### Moonshot Features
- [ ] AI therapy prep assistant (helps clients prepare for sessions)
- [ ] Therapist co-pilot (AI suggests interventions based on client data)
- [ ] Mental health concierge (navigate insurance, find resources)
- [ ] Predictive crisis intervention (identify at-risk users)
- [ ] Peer support network integration
- [ ] Integration with wearables (mood tracking, sleep data)

### Acquisition Targets
- $500M-2B valuation
- Acquirers: BetterHelp, Headspace, Epic, UnitedHealth, Google/Apple Health

---

## Feature Backlog (Prioritised)

### High Priority (Next 6 Months)
1. User testing program (50+ participants)
2. Match quality tracking dashboard
3. Clinic partnership agreements (3-5 signed)
4. White-label widget MVP
5. Therapist self-onboarding system
6. Payment integration (Stripe)

### Medium Priority (6-12 Months)
1. Mobile app (React Native)
2. Advanced analytics dashboard
3. Video integration (Zoom/Doxy.me)
4. Insurance verification API
5. Multi-clinic management (for group practices)
6. Email marketing automation (Mailchimp/SendGrid)

### Low Priority (12-24 Months)
1. AI matching v2.0 (ML-powered)
2. Outcome measurement integration
3. International expansion (UK, Canada)
4. Enterprise sales playbook
5. API for third-party integrations
6. Mobile therapist app (for profile management)

### Future/Research (24+ Months)
1. Conversational AI quiz
2. Voice-based intake
3. Therapist co-pilot AI
4. Crisis intervention prediction
5. Wearable integration
6. Peer support networks

---

## Success Metrics by Phase

| Phase | Timeline | Clinics | Therapists | Monthly Matches | MRR | Status |
|-------|----------|---------|------------|-----------------|-----|--------|
| **Phase 0: MVP** | Q4 2025 | 1 | 8 | <10 | $0 | ✅ Complete |
| **Phase 1: Validation** | Q1-Q2 2026 | 3-5 | 20-30 | 50-100 | $0-500 | ► Next |
| **Phase 2: Pilot** | Q2-Q3 2026 | 5-10 | 50-100 | 200-500 | $2K-5K | Planned |
| **Phase 3: Scale** | Q4 2026-2027 | 100+ | 1000+ | 5000+ | $50K+ | Planned |
| **Phase 4: Platform** | 2028-2029 | 1000+ | 10,000+ | 50,000+ | $500K+ | Vision |
| **Phase 5: Global** | 2030+ | 10,000+ | 100,000+ | 500,000+ | $5M+ | Moonshot |

---

## Key Decisions & Trade-Offs

### Build vs. Buy Decisions

**Build:**
- Matching algorithm (core IP, competitive advantage)
- Quiz interface (unique UX is the product)
- Therapist profiles (custom data model)

**Buy/Integrate:**
- Video conferencing (Zoom/Doxy.me)
- Payment processing (Stripe)
- Email delivery (SendGrid)
- Calendar scheduling (Calendly API)
- Insurance verification (Eligible API)

### Technology Trade-Offs

**Current: Vanilla JS + PHP**
- ✅ Fast to build, easy to deploy
- ✅ Low cost, no dependencies
- ❌ Hard to scale, no type safety
- ❌ Limited testing infrastructure

**Phase 2: React + Node.js**
- ✅ Easier to scale, better DX
- ✅ Large talent pool
- ✅ Strong ecosystem
- ❌ More complex deployment
- ❌ Higher hosting costs

**Phase 3: Microservices**
- ✅ Independent scaling
- ✅ Team autonomy
- ✅ Fault isolation
- ❌ Operational complexity
- ❌ Requires DevOps expertise

### Market Positioning Trade-Offs

**Niche (LGBTQIA+, neurodivergent-focused):**
- ✅ Clear differentiation, less competition
- ✅ Strong brand loyalty, word-of-mouth
- ❌ Smaller TAM (total addressable market)
- ❌ Harder to scale quickly

**Broad (all therapy seekers):**
- ✅ Massive TAM, easier fundraising
- ✅ Faster growth potential
- ❌ Competing with incumbents directly
- ❌ Less differentiation

**Decision: Start niche, expand broad**
- Build for underserved communities first
- Prove concept, build brand loyalty
- Then expand to mainstream market

---

## Risk Mitigation Strategy

### Technical Risks
| Risk | Likelihood | Impact | Mitigation |
|------|------------|--------|------------|
| Scalability issues | Medium | High | Modular architecture, cloud migration plan |
| Data breach | Low | Critical | SOC 2, HIPAA compliance, security audits |
| AI hallucination | Medium | Medium | Template fallbacks, human review |
| Server downtime | Low | High | Multi-region hosting, 99.9% SLA |

### Business Risks
| Risk | Likelihood | Impact | Mitigation |
|------|------------|--------|------------|
| Low therapist adoption | Medium | High | Start with clinics (built-in therapists) |
| Client distrust of AI | Medium | Medium | Human therapists always in loop, transparency |
| Incumbent competition | High | Medium | Move fast, niche-first strategy |
| Regulatory changes | Low | High | Monitor legislation, legal advisors |

### Market Risks
| Risk | Likelihood | Impact | Mitigation |
|------|------------|--------|------------|
| Economic downturn | Medium | High | Target EAP providers, insurance integration |
| Therapist shortage | High | Medium | Make platform attractive for therapists |
| Privacy backlash | Low | Critical | Privacy-first design, data minimisation |

---

## Open Questions & Research Needed

### User Research Questions
- [ ] What % of people abandon therapist search due to overwhelm?
- [ ] How long does it take average person to find therapist?
- [ ] What's the most frustrating part of current search process?
- [ ] Would users pay for better matching? How much?
- [ ] What features would make them switch from Psychology Today?

### Business Model Questions
- [ ] What's the right pricing for clinics? (Usage-based vs. flat fee?)
- [ ] Should we charge clients or therapists or both?
- [ ] What's the LTV:CAC ratio we need to be sustainable?
- [ ] Can we justify premium pricing with outcome data?

### Technical Questions
- [ ] What matching accuracy is "good enough"?
- [ ] How much data do we need to train ML model?
- [ ] What's the right balance between AI and human curation?
- [ ] How do we measure match quality objectively?

---

## Conclusion

This roadmap is a living document. We'll adapt based on:
- User feedback
- Market changes
- Technical constraints
- Funding availability

**Core principles that won't change:**
1. **Identity-first matching** (always our differentiator)
2. **Therapists are partners, not products** (respect + empower)
3. **Privacy & security** (never compromise on user trust)
4. **Continuous improvement** (learn from every interaction)

**Next Milestone: Phase 1 Validation (Mar 2026)**
- 100+ users tested the matching quiz
- 3-5 clinic pilots signed
- Match quality validated (80%+ satisfaction)
- Funding secured for Phase 2 ($150K-300K)

---

*Last Updated: October 10, 2025*  
*Next Review: Monthly (first Tuesday of each month)*  
*Owner: Tino (Founder)*

