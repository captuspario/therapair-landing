# A/B Testing Implementation Summary

## ✅ What's Been Implemented

### 1. **Batch Sending Script** (`research/scripts/send-ab-test-batch.mjs`)

- Sends to **10 recipients by default** (5 Variant A, 5 Variant B)
- Randomly assigns variants (alternating for even split)
- Tracks variant assignment in Notion
- Includes UTM parameters for tracking (`utm_experiment`, `utm_variant`)
- Respects Resend rate limits (600ms delay between sends)

**Usage:**
```bash
node research/scripts/send-ab-test-batch.mjs [batch_size] [RESEND_API_KEY]
# Example: node research/scripts/send-ab-test-batch.mjs 10 YOUR_API_KEY
```

### 2. **Notion Properties** (`scripts/add-ab-testing-properties.php`)

Adds all required properties to VIC Therapist database:

**A/B Test Assignment:**
- `Research Variant` (Select: A, B)
- `Research Experiment ID` (Rich Text)

**Invite Tracking:**
- `Research Invite Sent` (Checkbox)
- `Research Invite Sent Date` (Date)

**Engagement Tracking:**
- `Research Email Opened` (Checkbox)
- `Research Email Opened Date` (Date)
- `Research Email Opens Count` (Number)
- `Research Survey Clicked` (Checkbox)
- `Research Survey Clicked Date` (Date)
- `Research Survey Clicks Count` (Number)
- `Research Survey Completed` (Checkbox)
- `Research Survey Completed Date` (Date)
- `Research Sandbox Clicked` (Checkbox)
- `Research Sandbox Clicked Date` (Date)

**Status:**
- `Research Status` (Select: Not Invited, Invited, Opened, Clicked Survey, Completed Survey, Clicked Sandbox)

**Run:**
```bash
php scripts/add-ab-testing-properties.php
```

### 3. **Webhook Tracking** (`api/research/email-event.php`)

Automatically updates Notion when:
- **Email is opened** → Sets `Research Email Opened`, `Research Email Opened Date`, increments `Research Email Opens Count`
- **Survey link is clicked** → Sets `Research Survey Clicked`, `Research Survey Clicked Date`, increments `Research Survey Clicks Count`
- **Sandbox link is clicked** → Sets `Research Sandbox Clicked`, `Research Sandbox Clicked Date`

### 4. **Survey Completion Tracking** (`api/research/response.php`)

When survey is submitted:
- Extracts email and variant from token
- Updates therapist directory:
  - Sets `Research Survey Completed` = true
  - Sets `Research Survey Completed Date`
  - Updates `Research Status` = "Completed Survey"
  - Ensures `Research Variant` is set

### 5. **Dashboard Setup Guide** (`AB-TEST-DASHBOARD-SETUP.md`)

Complete guide for creating Notion dashboard views:
- A/B Test Overview (high-level metrics)
- Variant A Performance (detailed)
- Variant B Performance (detailed)
- Recent Activity
- Non-Responders (for reminders)
- Sandbox Follow-up Candidates

---

## 🚀 Quick Start Workflow

### Step 1: Setup Notion Properties
```bash
php scripts/add-ab-testing-properties.php
```

### Step 2: Create Dashboard Views
Follow `AB-TEST-DASHBOARD-SETUP.md` to create views in Notion

### Step 3: Send First Batch
```bash
node research/scripts/send-ab-test-batch.mjs 10
```

### Step 4: Monitor Dashboard
- Wait 24-48 hours for responses
- Check "A/B Test Overview" for metrics
- Review "Variant A - Detailed" and "Variant B - Detailed"
- Check "Recent Activity" for engagement

### Step 5: Analyze & Decide
- Compare completion rates
- Review open/click rates
- Check qualitative feedback
- Decide on winner or request more data

### Step 6: Send Next Batch (if approved)
- Use winning variant, or
- Test new variable, or
- Continue with current test

---

## 📊 Metrics Tracked

### Primary Metrics (A/B Test Decision)
1. **Survey Completion Rate**
   - Variant A: `Completed / Invited * 100`
   - Variant B: `Completed / Invited * 100`

2. **Survey Click-Through Rate (CTR)**
   - Variant A: `Clicked / Invited * 100`
   - Variant B: `Clicked / Invited * 100`

3. **Open Rate**
   - Variant A: `Opened / Invited * 100`
   - Variant B: `Opened / Invited * 100`

### Secondary Metrics
- Email opens count (multiple opens tracked)
- Survey clicks count (multiple clicks tracked)
- Time to click (from send date to click date)
- Time to complete (from send date to completion date)
- Sandbox engagement (for follow-up emails)

---

## 🎯 A/B Test Variants

### Variant A
- **Subject:** "Help shape a better therapist-matching system"
- **Preview:** "We found your details in the VIC therapists register and thought you might be interested in shaping a new approach to therapist–client matching."
- **Intro:** More narrative, explains context

### Variant B
- **Subject:** "5 minutes to improve therapist matching?"
- **Preview:** "Quick research survey: Help us understand what questions matter most for meaningful therapist–client matching."
- **Intro:** More direct, question-based hook

---

## 📝 Notes

- **Batch size:** Default 10 (5 A, 5 B) - can be adjusted
- **Rate limiting:** 600ms delay between sends (Resend allows 2/sec)
- **Tracking:** All events automatically logged via webhooks
- **Dashboard:** Updates in real-time as webhooks fire
- **Token data:** Saved to `research/batch-{batch_id}.json` for reference

---

## 🔄 Next Steps

1. **Send first batch** (10 recipients)
2. **Monitor dashboard** for 24-48 hours
3. **Review results** and decide on winner
4. **Optimize** based on feedback
5. **Send next batch** with winning variant or new test

---

## 📚 Related Files

- `research/scripts/send-ab-test-batch.mjs` - Batch sending script
- `scripts/add-ab-testing-properties.php` - Notion properties setup
- `AB-TEST-DASHBOARD-SETUP.md` - Dashboard creation guide
- `api/research/email-event.php` - Webhook handler for opens/clicks
- `api/research/response.php` - Survey completion tracking

