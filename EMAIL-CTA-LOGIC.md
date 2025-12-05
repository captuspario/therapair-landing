# Email CTA Logic - Clear Rules

## Overview
This document clearly defines which CTAs appear in which emails.

---

## 1. EOI Confirmation Emails (from submit-form.php)

**Source:** `submit-form.php` → `formatUserEmail()` function

### All Audiences (individual, therapist, organization, other):
- ✅ **Primary CTA: "View Sandbox Demo"** (ALWAYS shown)
  - Links to: `https://therapair.com.au/sandbox/sandbox-demo.html`
  - Tracked through: `track.php?dest=sandbox`

### Therapist Audience ONLY:
- ✅ **Secondary CTA: "JOIN THE RESEARCH SURVEY (5–7 minutes)"** (ADDITIONAL CTA)
  - Links to: `https://therapair.com.au/research/survey/index.html?token=...`
  - Includes personalized token for tracking
  - Tracked through: `track.php?dest=survey`

### Summary:
- **Individual/Organization/Other EOI:** 1 CTA (Sandbox only)
- **Therapist EOI:** 2 CTAs (Sandbox + Survey)

---

## 2. Research Invitation Emails (from VIC Therapists database)

**Source:** `research/therapair-research-email.html`

### All Research Invites:
- ✅ **Primary CTA: "JOIN THE RESEARCH SURVEY (5–7 minutes)"** (ONLY CTA)
  - Links to: `https://therapair.com.au/research/survey/index.html?token=...`
  - Includes personalized token for tracking
  - Tracked through: `track.php?dest=survey`
- ❌ **NO Sandbox CTA** (research invites are focused on survey completion)

### Summary:
- **Research Invites:** 1 CTA (Survey only)

---

## Logic Flow

```
EOI Submission
├── Individual/Org/Other → Sandbox CTA only
└── Therapist → Sandbox CTA + Survey CTA

Research Invite (VIC Database)
└── All → Survey CTA only
```

---

## Why This Logic?

1. **EOI Submissions:**
   - Everyone gets sandbox access to explore the concept
   - Therapists additionally get survey invite (they're the target audience for research)

2. **Research Invites:**
   - Focused on survey completion (primary goal)
   - No sandbox distraction (they can explore later if interested)
   - Clean, single-purpose email

---

## Files to Update

If you need to change CTA logic:

1. **EOI Emails:** Edit `submit-form.php` → `formatUserEmail()` function
2. **Research Invites:** Edit `research/therapair-research-email.html`

---

## Last Updated
2025-01-13 - Aligned all email templates with clear CTA logic

