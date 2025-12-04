# A/B Test Dashboard Setup Guide

## Overview

This guide shows you how to create Notion dashboard views to track A/B test performance for the research email campaign.

---

## 📊 Dashboard Views to Create

### 1. **A/B Test Overview Dashboard**

**Purpose:** High-level metrics comparing Variant A vs Variant B

**Create a new view called "A/B Test Overview"**

**Add these columns (formulas or rollups):**

| Column Name | Type | Formula/Calculation |
|------------|------|-------------------|
| **Total Invited** | Rollup | Count all entries where `Research Invite Sent` = true |
| **Variant A Count** | Rollup | Count where `Research Variant` = "A" AND `Research Invite Sent` = true |
| **Variant B Count** | Rollup | Count where `Research Variant` = "B" AND `Research Invite Sent` = true |
| **Variant A Opens** | Rollup | Count where `Research Variant` = "A" AND `Research Email Opened` = true |
| **Variant B Opens** | Rollup | Count where `Research Variant` = "B" AND `Research Email Opened` = true |
| **Variant A Open Rate** | Formula | `Variant A Opens / Variant A Count * 100` |
| **Variant B Open Rate** | Formula | `Variant B Opens / Variant B Count * 100` |
| **Variant A Clicks** | Rollup | Count where `Research Variant` = "A" AND `Research Survey Clicked` = true |
| **Variant B Clicks** | Rollup | Count where `Research Variant` = "B" AND `Research Survey Clicked` = true |
| **Variant A CTR** | Formula | `Variant A Clicks / Variant A Count * 100` |
| **Variant B CTR** | Formula | `Variant B Clicks / Variant B Count * 100` |
| **Variant A Completions** | Rollup | Count where `Research Variant` = "A" AND `Research Survey Completed` = true |
| **Variant B Completions** | Rollup | Count where `Research Variant` = "B" AND `Research Survey Completed` = true |
| **Variant A Completion Rate** | Formula | `Variant A Completions / Variant A Count * 100` |
| **Variant B Completion Rate** | Formula | `Variant B Completions / Variant B Count * 100` |

**Filter:**
- `Research Invite Sent` = true

**Sort:** None (this is a summary view)

---

### 2. **Variant A Performance**

**Purpose:** Detailed view of Variant A recipients

**Create a new view called "Variant A - Detailed"**

**Columns to show:**
- Name (First Name)
- Email Address
- Research Variant
- Research Invite Sent Date
- Research Email Opened
- Research Email Opens Count
- Research Survey Clicked
- Research Survey Clicks Count
- Research Survey Completed
- Research Survey Completed Date
- Research Sandbox Clicked
- Research Status

**Filter:**
- `Research Variant` = "A"
- `Research Invite Sent` = true

**Sort:** 
- `Research Invite Sent Date` (newest first)

**Group by:** `Research Status`

---

### 3. **Variant B Performance**

**Purpose:** Detailed view of Variant B recipients

**Create a new view called "Variant B - Detailed"**

**Columns to show:** (same as Variant A)

**Filter:**
- `Research Variant` = "B"
- `Research Invite Sent` = true

**Sort:** 
- `Research Invite Sent Date` (newest first)

**Group by:** `Research Status`

---

### 4. **Recent Activity**

**Purpose:** See all recent engagement activity

**Create a new view called "Recent Activity"**

**Columns to show:**
- Name (First Name)
- Email Address
- Research Variant
- Research Status
- Research Email Opened Date
- Research Survey Clicked Date
- Research Survey Completed Date
- Research Sandbox Clicked Date

**Filter:**
- `Research Invite Sent` = true
- AND (`Research Email Opened Date` is not empty OR `Research Survey Clicked Date` is not empty OR `Research Survey Completed Date` is not empty)

**Sort:** 
- `Research Survey Completed Date` (newest first)
- Then `Research Survey Clicked Date` (newest first)
- Then `Research Email Opened Date` (newest first)

---

### 5. **Non-Responders (For Reminder)**

**Purpose:** Identify who hasn't responded yet

**Create a new view called "Non-Responders"**

**Columns to show:**
- Name (First Name)
- Email Address
- Research Variant
- Research Invite Sent Date
- Research Email Opened
- Research Survey Clicked

**Filter:**
- `Research Invite Sent` = true
- `Research Survey Completed` = false OR empty
- `Research Survey Completed Date` is empty

**Sort:** 
- `Research Invite Sent Date` (oldest first)

**Group by:** `Research Variant`

---

### 6. **Sandbox Follow-up Candidates**

**Purpose:** People who completed survey but haven't clicked sandbox

**Create a new view called "Sandbox Follow-up"**

**Columns to show:**
- Name (First Name)
- Email Address
- Research Variant
- Research Survey Completed Date
- Research Sandbox Clicked

**Filter:**
- `Research Survey Completed` = true
- `Research Sandbox Clicked` = false OR empty

**Sort:** 
- `Research Survey Completed Date` (newest first)

---

## 📈 Key Metrics to Monitor

### Primary Metrics (A/B Test Decision)
1. **Survey Completion Rate** (most important)
   - Variant A: `Completed / Invited * 100`
   - Variant B: `Completed / Invited * 100`
   - **Winner:** Higher completion rate

2. **Survey Click-Through Rate (CTR)**
   - Variant A: `Clicked / Invited * 100`
   - Variant B: `Clicked / Invited * 100`

3. **Open Rate**
   - Variant A: `Opened / Invited * 100`
   - Variant B: `Opened / Invited * 100`

### Secondary Metrics
- **Time to Click** (average time from send to click)
- **Time to Complete** (average time from send to completion)
- **Sandbox Engagement** (for follow-up emails)

---

## 🎯 Decision Rules

### When to Declare a Winner

After reviewing the dashboard:

1. **Minimum Sample Size:** Wait until at least 5 recipients per variant have had time to respond (24-48 hours)

2. **Completion Rate Difference:**
   - If one variant has **≥5 percentage points** higher completion rate → Declare winner
   - If difference is <5 points → Consider inconclusive, may need more data

3. **Statistical Significance (Optional):**
   - For small samples (10-20), use directional decisions
   - For larger samples (50+), consider statistical significance tests

4. **Practical Considerations:**
   - Check if one variant has significantly better CTR even if completion is similar
   - Consider qualitative feedback from survey responses
   - Review open rates to understand subject line performance

### Example Decision Table

| Scenario | Variant A Completion | Variant B Completion | Decision |
|----------|---------------------|----------------------|----------|
| Clear winner | 30% | 15% | Use Variant A for next batch |
| Close call | 25% | 22% | Inconclusive, may need more data |
| Tied | 20% | 20% | Either variant acceptable, or test different variable |

---

## 🔄 Workflow

1. **Send Batch** (10 recipients: 5 A, 5 B)
2. **Wait 24-48 hours** for responses
3. **Review Dashboard:**
   - Check "A/B Test Overview" for high-level metrics
   - Review "Variant A - Detailed" and "Variant B - Detailed"
   - Check "Recent Activity" for engagement
4. **Analyze Results:**
   - Compare completion rates
   - Review any qualitative feedback
   - Check open/click rates
5. **Make Decision:**
   - Declare winner (if clear)
   - Or request more data
   - Or optimize and test again
6. **Send Next Batch** (if approved) with winning variant or new test

---

## 📝 Notes

- **Dashboard updates automatically** as webhooks update Notion properties
- **Resend dashboard** also shows open/click data (can cross-reference)
- **Survey responses** appear in separate Research Responses database
- **Link tracking** via UTM parameters in URLs

---

## 🚀 Quick Start

1. Run `php scripts/add-ab-testing-properties.php` to add properties
2. Create the dashboard views in Notion (follow guide above)
3. Send first batch: `node research/scripts/send-ab-test-batch.mjs 10`
4. Monitor dashboard for 24-48 hours
5. Review results and decide on next steps

