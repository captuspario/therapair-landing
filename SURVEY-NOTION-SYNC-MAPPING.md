# Survey Questions → Notion Properties Mapping

**Date:** 2025-12-03  
**Status:** ✅ Synced

---

## Complete Field Mapping

### **Survey Questions → Notion Properties**

| Survey Field | Notion Property | Type | Status |
|-------------|----------------|------|--------|
| `email` | `1. Email` | Email | ✅ |
| `profession` | `2. Profession` | Select | ✅ |
| `profession_other` | `3. Profession Other` | Rich Text | ✅ |
| `years_practice` | `4. Years in Practice` | Select | ✅ |
| `client_types` | `5. Client Types` | Multi-select | ✅ |
| `client_types_other` | `6. Client Types Other` | Rich Text | ✅ |
| `modalities` | `7. Modalities` | Multi-select | ✅ |
| `modalities_other` | `8. Modalities Other` | Rich Text | ✅ |
| `clients_find_you` | `9. How Clients Find You` | Multi-select | ✅ |
| `clients_find_you_other` | `10. How Clients Find You Other` | Rich Text | ✅ |
| `match_factors` | `11. Great Match Factors` | Multi-select | ✅ |
| `match_factors_other` | `12. Great Match Other` | Rich Text | ✅ |
| `biggest_gap` | `13. Biggest Gap` | Rich Text | ✅ |
| `screens_clients` | `14. Screening Clients` | Select | ✅ |
| `open_to_sharing` | `15. Open to Sharing` | Select | ✅ |
| `questions_matter` | `16. Which Questions Matter` | Multi-select | ✅ |
| `questions_matter_other` | `17. Which Questions Matter Other` | Rich Text | ✅ |
| `personality_test` | `18. Personality Test` | Select | ✅ |
| `too_personal` | `19. Too Personal` | Multi-select | ✅ |
| `too_personal_other` | `20. Too Personal Other` | Rich Text | ✅ |
| `profile_detail_level` | `21. Profile Detail Level` | Select | ✅ |
| `onboarding_time` | `22. Onboarding Time` | Select | ✅ |
| `free_listing_interest` | `24. Free Listing Interest` | Select | ✅ |
| `profile_intent` | `Profile Intent` | Select | ✅ |
| `future_contact` | `25. Future Contact` | Select | ✅ |
| **`value_fee_per_match`** | **`26. Fee Per Match (Practitioner View)`** | **Number** | ✅ **ADDED** |
| **`value_monthly_subscription`** | **`27. Monthly Subscription (Practitioner View)`** | **Number** | ✅ **ADDED** |
| `comments` | `28. Comments` | Rich Text | ✅ |

---

## Changes Made

### **Added Pricing Fields:**
- ✅ `value_fee_per_match` → `26. Fee Per Match (Practitioner View)` (Number)
- ✅ `value_monthly_subscription` → `27. Monthly Subscription (Practitioner View)` (Number)

### **Removed Obsolete Fields:**
- ❌ `26. Value Payment Model` (was referencing non-existent `value_payment_model`)
- ❌ `27. Value Payment Notes` (was referencing non-existent `value_payment_notes`)
- ❌ `28. Subscription Amount (Practitioner View)` (was referencing non-existent `value_subscription_amount`)

---

## Notion Database Setup Required

**You need to add these properties to your Notion Research Database:**

1. **`26. Fee Per Match (Practitioner View)`**
   - Type: **Number**
   - Format: **Number** (no currency symbol)

2. **`27. Monthly Subscription (Practitioner View)`**
   - Type: **Number**
   - Format: **Number** (no currency symbol)

---

## Property Types Reference

- **Select**: Single choice dropdown
- **Multi-select**: Multiple choice tags
- **Rich Text**: Long-form text
- **Number**: Numeric value (for pricing)
- **Email**: Email address
- **Date**: Date/timestamp

---

**All survey questions are now properly synced with Notion! ✅**

