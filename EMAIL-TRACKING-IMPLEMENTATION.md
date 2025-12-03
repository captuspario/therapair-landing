# Email Link Tracking Implementation Guide

## 🎯 Best Practice for EOI Email Tracking

### Recommended Approach: **Hybrid Tracking System**

1. **UTM Parameters** - For analytics and source attribution
2. **Resend Webhooks** - For email open/click events (if using Resend)
3. **Notion Database Properties** - For storing engagement data
4. **Tracking Redirect** - For reliable click tracking via Notion page ID

---

## 📊 Current State Analysis

### ✅ What Exists:
- `track.php` - Basic tracking redirect system (not currently used)
- Resend webhook handler at `/api/research/email-event.php` (for research emails only)
- Notion sync creates entries with page IDs

### ❌ What's Missing:
- UTM parameters on email links
- Tracking redirect URLs in confirmation emails
- Tracking properties in EOI Notion database
- Notion page ID stored/used for tracking
- Resend webhook configured for EOI emails

---

## 🔧 Implementation Plan

### Step 1: Add Tracking Properties to Notion EOI Database

**Required Properties:**
- `Email Opened Date` (Date) - When email was opened
- `Email Clicked Date` (Date) - When any link was clicked
- `Sandbox Clicked Date` (Date) - When sandbox demo link was clicked
- `Last Engagement Date` (Date) - Most recent engagement
- `Email Opens Count` (Number) - Total opens
- `Email Clicks Count` (Number) - Total clicks
- `Last Clicked Link` (Rich Text) - Which link was clicked last

**Optional Properties:**
- `UTM Source` (Rich Text) - Source of click
- `UTM Medium` (Rich Text) - Medium of click
- `UTM Campaign` (Rich Text) - Campaign name

### Step 2: Store Notion Page ID

When creating EOI entry in Notion, store the page ID so we can track clicks:
- Store in session/cookie (temporary)
- Or pass via URL parameter (more reliable)

### Step 3: Create Tracking URLs

Replace direct links with tracking redirect URLs:
```
Before: https://therapair.com.au/sandbox/sandbox-demo.html
After:  https://therapair.com.au/track.php?uid={NOTION_PAGE_ID}&dest=sandbox&utm_source=email&utm_medium=eoi_confirmation&utm_campaign=sandbox_demo
```

### Step 4: Add UTM Parameters

All email links should include:
- `utm_source=email`
- `utm_medium=eoi_confirmation`
- `utm_campaign={link_type}` (e.g., sandbox_demo, email_preferences)
- `utm_content={audience_type}` (e.g., therapist, individual)

### Step 5: Update track.php

Enhance `track.php` to:
- Accept Notion page ID
- Track click in Notion database
- Preserve UTM parameters
- Redirect to final destination

### Step 6: Configure Resend Webhooks (Optional but Recommended)

If using Resend for email delivery:
- Set up webhook endpoint for EOI emails
- Track opens and clicks via Resend events
- Update Notion database with engagement data

---

## 🛠️ Technology Requirements

### Required:
- ✅ PHP (already have)
- ✅ Notion API (already configured)
- ✅ Notion database with tracking properties (needs to be added)

### Optional but Recommended:
- Resend webhooks (if using Resend for email delivery)
- Google Analytics (for additional analytics)
- Custom analytics dashboard

---

## 📝 Implementation Checklist

### Database Setup:
- [ ] Add tracking properties to Notion EOI database
- [ ] Test property updates via Notion API
- [ ] Verify property names match code

### Code Updates:
- [ ] Update `submit-form.php` to store Notion page ID
- [ ] Update `formatUserEmail()` to use tracking URLs
- [ ] Enhance `track.php` for EOI tracking
- [ ] Add UTM parameters to all email links
- [ ] Test tracking flow end-to-end

### Testing:
- [ ] Send test EOI confirmation email
- [ ] Click sandbox demo link
- [ ] Verify Notion database updates
- [ ] Check UTM parameters are preserved
- [ ] Test with all audience types

---

## 🎯 Tracking URLs Format

### Sandbox Demo Link:
```
https://therapair.com.au/track.php?uid={NOTION_PAGE_ID}&dest=sandbox&utm_source=email&utm_medium=eoi_confirmation&utm_campaign=sandbox_demo&utm_content={audience}
```

### Email Preferences Link:
```
https://therapair.com.au/track.php?uid={NOTION_PAGE_ID}&dest=preferences&utm_source=email&utm_medium=eoi_confirmation&utm_campaign=email_preferences&utm_content={audience}
```

### Website Link:
```
https://therapair.com.au/track.php?uid={NOTION_PAGE_ID}&dest=home&utm_source=email&utm_medium=eoi_confirmation&utm_campaign=website&utm_content={audience}
```

---

## 📊 What Gets Tracked

1. **Email Opens** - Via Resend webhook (if configured)
2. **Link Clicks** - Via tracking redirect URLs
3. **Click Type** - Which link was clicked (sandbox, preferences, etc.)
4. **Click Timestamp** - When the click occurred
5. **Source Attribution** - UTM parameters for analytics
6. **Engagement History** - All interactions in one place

---

## 🔒 Privacy Considerations

- Tracking is opt-in (user submitted EOI)
- No personal data in tracking URLs (only Notion page ID)
- UTM parameters are standard analytics practice
- Users can unsubscribe via email preferences

---

## 🚀 Next Steps

1. Review and approve this implementation plan
2. Add tracking properties to Notion database
3. Implement code changes
4. Test with real email
5. Monitor tracking data

