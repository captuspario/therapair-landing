# Email Template Source of Truth

## Overview
This document defines which email templates are the **source of truth** for different email types and how to maintain consistency.

## Email Types & Sources

### 1. **Research Invitation Email** (Therapist Research)
**Source of Truth:** `research/therapair-research-email.html`

**Used by:**
- `research/scripts/create-test-participant.mjs`
- `research/scripts/create-therapist-invite.mjs`
- `research/scripts/generate-batch-tokens.mjs`

**CTA Structure:**
- ✅ Left-aligned CTAs
- ✅ Explain text comes first, then CTA
- ✅ No sublines below CTAs
- ✅ Primary CTA: "JOIN THE RESEARCH SURVEY (5–7 minutes)"
- ✅ Secondary CTA: "View Sandbox Demo"

**Last Updated:** 2025-01-13 (Left-aligned CTAs to match EOI template)

---

### 2. **EOI Confirmation Email** (Expression of Interest)
**Source of Truth:** `submit-form.php` → `formatUserEmail()` function

**Used by:**
- `submit-form.php` (EOI form submission handler)

**CTA Structure:**
- ✅ Left-aligned CTAs
- ✅ Explain text comes first, then CTA
- ✅ No sublines below CTAs
- ✅ Primary CTA: "View Sandbox Demo"
- ✅ Secondary CTA: "Take Research Survey" (therapist audience only)

**Last Updated:** 2025-01-13 (Left-aligned CTAs, removed sublines)

---

## Design System Consistency

### CTA Button Styles
All CTAs use the same button styles from `email-template-base.php`:
- **Primary:** `getEmailButtonStyle('primary')` - Gradient blue background
- **Secondary:** `getEmailButtonStyle('secondary')` - White background with blue border

### Layout Rules
1. **Text first, CTA second:** Always place explanatory text before the CTA button
2. **Left alignment:** All CTAs are left-aligned (not centered)
3. **No sublines:** Do not add explanatory text below CTA buttons
4. **Spacing:** 24px margin between CTAs, 32px margin before first CTA

### Example Structure
```html
<p style="...">Explanatory text comes first...</p>

<div style="margin: 0 0 24px 0;">
    <a href="..." style="...">CTA Button Text</a>
</div>
```

---

## Maintenance Guidelines

### When Updating Email Templates

1. **Update the source of truth first**
   - For research emails: Update `research/therapair-research-email.html`
   - For EOI emails: Update `submit-form.php` → `formatUserEmail()`

2. **Test the changes**
   - Run validation script: `php scripts/validate-email-template.php`
   - Send test email to verify rendering

3. **Sync related templates** (if any)
   - Check if React Email templates need updating
   - Update any hardcoded templates in scripts

4. **Document changes**
   - Update this file with the date and changes made
   - Note any breaking changes

### Deprecated Templates

The following templates are **NOT** the source of truth and should not be edited directly:
- `research/tino-research-email.html` (old version)
- `research/ResearchInvitationEmail.tsx` (React Email - may be used for preview only)

---

## Template Sync Status

| Template | Status | Last Synced | Notes |
|----------|--------|-------------|-------|
| `research/therapair-research-email.html` | ✅ Source of Truth | 2025-01-13 | Left-aligned CTAs |
| `submit-form.php` → `formatUserEmail()` | ✅ Source of Truth | 2025-01-13 | Left-aligned CTAs |
| `research/ResearchInvitationEmail.tsx` | ⚠️ Needs Sync | - | React Email preview only |

---

## Quick Reference

### Research Email Template
- **File:** `research/therapair-research-email.html`
- **Placeholders:** `{FIRST_NAME}`, `{THERAPIST_ID}`, `{EMAIL}`, `TOKEN_PLACEHOLDER`
- **Scripts using it:** All `create-*-invite.mjs` scripts

### EOI Email Template
- **File:** `submit-form.php` (function: `formatUserEmail()`)
- **Variables:** PHP variables (not placeholders)
- **Used by:** EOI form submission handler

---

## Questions?

If you need to update email copy or structure:
1. Identify which email type (Research or EOI)
2. Update the corresponding source of truth file
3. Test and deploy
4. Update this document

