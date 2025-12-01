# Survey Deployment Check

## Issue: Sandbox CTA Button Not Visible

### Current Status
- ✅ Button HTML exists in `index.html` (line 1193-1199)
- ✅ Button has correct ID: `sandbox-demo-button`
- ✅ CSS class `.submission-state.active` should display it
- ✅ JavaScript function `showSubmissionState("success")` is called after submission

### Verification Steps

1. **Check if page is deployed:**
   - Visit: `https://therapair.com.au/research/survey/index.html?token=TEST`
   - Complete a survey submission
   - Verify the success state shows

2. **Check browser cache:**
   - Hard refresh: `Cmd+Shift+R` (Mac) or `Ctrl+Shift+R` (Windows)
   - Or clear cache and reload

3. **Check CSS:**
   ```css
   .submission-state {
     display: none; /* Hidden by default */
   }
   .submission-state.active {
     display: block; /* Shown when active */
   }
   ```

4. **Check JavaScript:**
   - After successful submission, `showSubmissionState("success")` should be called
   - This adds the `active` class to the section with `data-submission="success"`

### Debugging

If button still not visible, check browser console for:
- JavaScript errors
- CSS conflicts
- Element visibility in DevTools

### Deployment

If changes are not live, deploy:
```bash
# Files to deploy:
- products/landing-page/research/survey/index.html
- products/landing-page/research/survey/main.js
- products/landing-page/research/survey/styles.css
```

### Button Location
```html
<section class="submission-state" data-submission="success">
  <h2>Thank you so much</h2>
  <p>Your insights are now logged...</p>
  <a href="/documentation.html" class="button secondary">Read more...</a>
  <a href="/sandbox/sandbox-demo.html" 
     class="button primary" 
     id="sandbox-demo-button">See sandbox-demo</a>
</section>
```

