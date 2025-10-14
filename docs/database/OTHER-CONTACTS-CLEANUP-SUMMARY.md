# 🧹 "Other Contacts" Column Cleanup Summary

## 📊 Current Status

- **Total entries in database:** 202
- **Entries with "Other Contacts" data:** 111
- **Entries that will be updated:** 31
- **Entries that will be skipped:** 80

---

## ✨ What the Cleanup Script Does

### 1. **Extracts Phone Numbers**
Identifies Australian phone number formats and moves them to the `Phone` column:
- ✅ `Mobile: 0476053180` → Phone column
- ✅ `0435 727 197` → Phone column
- ✅ `(03) 7003 2602` → Phone column
- ✅ `Ph: 0401144483` → Phone column

### 2. **Extracts Social Media Handles**
Parses various social media patterns:
- ✅ `Facebook+Instagram: Howearth Psychology` → Both columns
- ✅ `@SocialSense_Au` (Twitter) → Twitter/X column
- ✅ `Instagram: caito.the.osteo` → Instagram column
- ✅ `Insta: Right__relationship` → Instagram column

### 3. **Clears Duplicate Data**
Removes entries that are already in their proper columns:
- If phone number exists in both `Phone` and `Other Contacts` → Clear from Other Contacts
- If social media already extracted → Clear the field

### 4. **Preserves Complex Data**
Skips entries that need manual review:
- ⏭️ Full URLs (e.g., `https://www.instagram.com/...`) - already handled elsewhere
- ⏭️ Business names without clear patterns
- ⏭️ Email addresses (should go in Email column)
- ⏭️ Directory links (Psychology Today, etc.)

---

## 📝 Examples of Changes

### Example 1: Clear Duplicate Phone
**Before:**
- Phone: `0476 053 180`
- Other Contacts: `Mobile: 0476053180`

**After:**
- Phone: `0476 053 180`
- Other Contacts: *(cleared)*

---

### Example 2: Extract New Phone Number
**Before:**
- Phone: *(empty)*
- Other Contacts: `Mobile: 0450 080 792`

**After:**
- Phone: `0450 080 792`
- Other Contacts: *(cleared)*

---

### Example 3: Extract Social Media
**Before:**
- Twitter/X: *(empty)*
- Facebook: `https://facebook.com/`
- Instagram: `@SocialSenseAlliedHealth`
- Other Contacts: `Facebook: @SocialSenseAlliedHealth | Twitter: @SocialSense_Au | Instagram: @socialsense_alliedhealth`

**After:**
- Twitter/X: `https://twitter.com/SocialSense_Au`
- Facebook: `https://facebook.com/`
- Instagram: `@SocialSenseAlliedHealth`
- Other Contacts: *(cleared)*

---

### Example 4: Extract Instagram Handle
**Before:**
- Instagram: *(empty)*
- Other Contacts: `Insta: Right__relationship`

**After:**
- Instagram: `https://instagram.com/Right__relationship`
- Other Contacts: *(cleared)*

---

## 🔧 How to Run

### Preview (Safe - No Changes)
```bash
cd /Users/tino/Projects/therapair-landing-page
python3 scripts/notion/preview-other-contacts.py
```

### Apply Changes (Updates Database)
```bash
cd /Users/tino/Projects/therapair-landing-page
python3 scripts/notion/clean-other-contacts.py
```

---

## ⚠️ What Gets Skipped (Requires Manual Review)

The script **will skip** these types of entries:

1. **Full URLs already in proper format:**
   - `https://www.instagram.com/fossickpoint/`
   - `https://www.facebook.com/SolutionPsychology`
   - `https://www.linkedin.com/in/mpomenta/`

2. **Directory listings:**
   - `https://www.psychologytoday.com/profile/...`
   - `www.goodtherapy.com.au/...`

3. **Business names without patterns:**
   - `Facebook The Therapy Hub`
   - `Albert Road Clinic`

4. **Incomplete phone numbers:**
   - `412930789` (missing leading 0)
   - `468863690`

5. **Complex social media without clear patterns:**
   - `@nutritionbychantal (instagram)` - (parentheses break pattern)
   - `healing_conversations (Instagram)`

6. **Email addresses:**
   - `info@alchemy-of-eros.com`
   - `hello@regenerativepsychology.com.au`

7. **N/A or generic text:**
   - `n/a`
   - `as above - fbk page`
   - `linkedin` (no username)

---

## 📊 Impact

### Will Be Cleaned (31 entries)
- Mostly duplicate phone numbers
- Some extractable social media handles
- Clear, parseable patterns

### Needs Manual Review (80 entries)
- Full URLs (can be copy-pasted manually)
- Complex formats
- Directory listings
- Incomplete data

---

## ✅ Next Steps

1. **Run the preview** to see exact changes
2. **Run the cleanup script** to apply changes
3. **Manually review the 80 skipped entries** and add data where appropriate
4. **Verify in Notion** that the changes look correct

---

## 🎯 Result

After running the cleanup script:
- **31 entries** will have clean, organized data
- **"Other Contacts" column** will be mostly cleared
- **Phone, Facebook, Instagram, Twitter/X, LinkedIn** columns will have structured data
- **80 entries** will still need manual review (but you'll know which ones)

---

**Created:** 2025-10-14  
**Script Location:** `scripts/notion/clean-other-contacts.py`  
**Preview Script:** `scripts/notion/preview-other-contacts.py`

