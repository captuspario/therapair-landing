# Manual Clean Screenshot Capture Instructions

**Goal**: Capture clean screenshots of just the Therapair widget content (no browser window, no Unison branding)

---

## ✅ Quiz Question - COMPLETED!

**Status**: ✅ Already captured clean quiz question  
**File**: `images/therapair-quiz-question.png`  
**Method**: Playwright iframe capture (automatic)

---

## 🎯 Results Cards - Manual Capture Needed

**Goal**: Capture 3 therapist result cards from iframe only

---

## 🚀 Method 1: Automated Script (Recommended)

### Run the manual capture script:

```bash
cd /Users/tino/Projects/therapair-landing-page
node capture-results-manual.js
```

**What happens**:
1. Browser opens to Unison page
2. CSS is injected to reduce white space
3. You complete the quiz manually
4. Press ENTER when ready to capture
5. Screenshot saved automatically

---

## 🖥️ Method 2: Manual Browser Capture

### Step 1: Open browser with proper viewport

```bash
# Chrome with tall viewport for 3 cards
/Applications/Google\ Chrome.app/Contents/MacOS/Google\ Chrome \
  --window-size=1600,2400 \
  https://unisonmentalhealth.com/find-a-therapist-who-is-right-for-you/
```

### Step 2: Inject CSS for reduced white space

1. Open DevTools (F12 or Cmd+Option+I)
2. Go to Console tab
3. Paste this code:

```javascript
// Find and inject CSS into iframe
const frame = document.querySelector('iframe');
if (frame && frame.contentDocument) {
  const style = frame.contentDocument.createElement('style');
  style.innerHTML = `
    [class*="skill"], [class*="pill"], [class*="tag"] {
      margin-bottom: 0.5rem !important;
    }
    [class*="button"], [class*="cta"], button {
      margin-top: 1rem !important;
      padding-top: 0.5rem !important;
    }
    [class*="card"] {
      margin-bottom: 1rem !important;
    }
  `;
  frame.contentDocument.head.appendChild(style);
  console.log('✅ CSS injected for compact spacing');
}
```

### Step 3: Complete the quiz

1. Answer all quiz questions
2. Reach the results page with 3 therapist cards
3. Ensure all 3 cards are fully visible (profile pic to CTA buttons)

### Step 4: Capture clean iframe content

**Option A: DevTools Screenshot (Best)**
1. Right-click on the widget area
2. Select "Inspect Element"
3. Find the main widget container (look for `.typebot-container` or similar)
4. Right-click on that element
5. Select "Capture node screenshot"
6. Save as `therapair-results-3-cards.png`

**Option B: Manual Crop**
1. Take full browser screenshot (Cmd+Shift+4)
2. Crop to just the widget area
3. Save as `therapair-results-3-cards.png`

---

## 📐 What to Capture

### Target Elements:
- **Quiz Question**: Progress indicator, question text, answer buttons
- **Results Cards**: 3 therapist profiles with:
  - Profile pictures (top)
  - Names and credentials
  - Specializations/skills pills
  - "Book Now" buttons (bottom)

### Avoid:
- ❌ Browser window chrome
- ❌ Unison Mental Health branding
- ❌ Page navigation elements
- ❌ Footer or external content

---

## 📁 File Locations

Save screenshots to:
```
/Users/tino/Projects/therapair-landing-page/images/
```

**Files**:
- `therapair-quiz-question.png` ✅ (already captured)
- `therapair-results-3-cards.png` 🎯 (to be captured)

---

## 🎨 CSS for Reduced White Space

The CSS injection reduces spacing between:
- Skills pills and "Book Now" buttons
- Card elements for compact layout
- Overall widget spacing

**Result**: Professional, compact appearance without excessive white space

---

## ✅ Verification Checklist

Before saving, verify:
- [ ] **3 cards visible** - All therapist recommendations showing
- [ ] **Full cards** - Profile pic to CTA buttons visible
- [ ] **Clean edges** - No browser window or external branding
- [ ] **Reduced spacing** - Compact layout between elements
- [ ] **Good quality** - Crisp, readable text and images

---

## 🔄 After Capture

### Update landing page (if needed):

```bash
# If you want to use the new screenshot filename
# Edit index.html line ~356:
# src="images/therapair-results-3-cards.png"

# Commit and deploy
git add images/therapair-results-3-cards.png
git commit -m "Add clean 3-card results screenshot"
git push github main
./deploy-to-hostinger.sh
```

---

## 🎯 Quick Commands

```bash
# Method 1: Automated script
node capture-results-manual.js

# Method 2: Playwright test (if working)
npx playwright test tests/capture-clean-widget.spec.js -g "capture clean results cards" --debug

# Method 3: Manual browser capture
# Open browser manually and follow Method 2 steps
```

---

## 📞 Troubleshooting

### Issue: Iframe not found
**Solution**: The widget might not be in an iframe. Use manual browser capture instead.

### Issue: CSS not applying
**Solution**: Make sure you're injecting into the correct iframe. Check console for errors.

### Issue: Cards cut off
**Solution**: Increase browser window height or adjust viewport.

### Issue: Too much white space
**Solution**: Adjust the CSS margin values in the injection code.

---

**Last Updated**: 14 October 2025  
**Status**: Ready for manual capture

**Next Step**: Run `node capture-results-manual.js` for automated approach

