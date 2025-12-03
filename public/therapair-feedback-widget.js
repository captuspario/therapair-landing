/**
 * Therapair Global Feedback Widget
 * Floating footer widget accessible from anywhere on therapair.com.au
 * Saves to "User feedback" Notion database
 */

(function() {
  'use strict';

  // Configuration
  const CONFIG = {
    endpoint: '/api/research/feedback.php', // Generic endpoint for all feedback
    primaryColor: '#3A6EA5', // Therapair blue
    primaryHover: '#2F5985',
    primaryLight: '#E8F0F8'
  };

  // Create widget HTML
  function createWidgetHTML() {
    return `
      <div id="therapair-feedback-container" style="position: fixed; bottom: 20px; right: 20px; z-index: 10000;">
        <button id="therapair-feedback-toggle-btn" onclick="window.therapairFeedbackToggle()"
          style="background: ${CONFIG.primaryColor}; color: white; border: none; padding: 12px 20px; border-radius: 30px; font-weight: 600; box-shadow: 0 4px 12px rgba(0,0,0,0.2); cursor: pointer; display: flex; align-items: center; gap: 8px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
          <span>💬</span> Give Feedback
        </button>

        <div id="therapair-feedback-modal"
          style="display: none; position: absolute; bottom: 60px; right: 0; width: 360px; max-width: calc(100vw - 2rem); background: white; padding: 24px; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); border: 1px solid #e5e7eb; z-index: 10001;">
              <div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 24px;">
                <h4 style="margin: 0; color: #1F2937; font-size: 18px; line-height: 28px; font-weight: 700;">Share feedback</h4>
                <button onclick="window.therapairFeedbackToggle()" style="background: none; border: none; color: #6B7280; cursor: pointer; padding: 4px; border-radius: 4px; transition: color 0.15s;">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                  </svg>
                </button>
              </div>

              <form id="therapair-feedback-form" onsubmit="window.therapairFeedbackSubmit(event)">
                <div style="margin-bottom: 24px;">
                  <label style="display: block; font-size: 14px; line-height: 20px; font-weight: 600; color: #1F2937; margin-bottom: 12px;">
                    How's your experience so far? <span style="color: ${CONFIG.primaryColor};">*</span>
                  </label>
                  <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                    ${[1, 2, 3, 4, 5, 6].map(val => {
                      return `
                        <button type="button" onclick="window.therapairSetRating(${val})" class="therapair-rating-btn" data-rating="${val}"
                          style="min-width: 44px; min-height: 44px; border: none; background: transparent; cursor: pointer; transition: transform 0.15s, opacity 0.15s; opacity: 0.4; color: #E5E7EB;"
                          aria-label="Rating ${val} of 6 stars">
                          <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                          </svg>
                        </button>
                      `;
                    }).join('')}
                  </div>
                  <input type="hidden" id="therapair-feedback-rating" name="rating" required>
                </div>

                <div style="margin-bottom: 24px;">
                  <label style="display: block; font-size: 14px; line-height: 20px; font-weight: 600; color: #1F2937; margin-bottom: 12px;">
                    Which area best fits your feedback? <span style="color: #6B7280; font-weight: 400;">(optional)</span>
                  </label>
                  <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                    ${['Bug', 'Usability', 'Speed', 'Content', 'Accessibility', 'Other'].map(tag => `
                      <button type="button" onclick="window.therapairToggleTag('${tag}')" class="therapair-tag-btn" data-tag="${tag}"
                        style="padding: 8px 12px; border: 1px solid #E5E7EB; background: white; color: #1F2937; border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer; transition: all 0.15s;">
                        ${tag}
                      </button>
                    `).join('')}
                  </div>
                  <input type="hidden" id="therapair-feedback-tags" name="tags">
                </div>

                <div style="margin-bottom: 24px;">
                  <label style="display: block; font-size: 14px; line-height: 20px; font-weight: 600; color: #1F2937; margin-bottom: 12px;">
                    Add any additional comments (optional)
                  </label>
                  <textarea id="therapair-feedback-comment" name="comment" rows="4"
                    style="width: 100%; padding: 12px; border: 1px solid #E5E7EB; border-radius: 8px; font-family: inherit; font-size: 14px; line-height: 20px; resize: vertical; box-sizing: border-box;"
                    placeholder="Tell us more..."></textarea>
                </div>

                <div style="display: flex; gap: 12px;">
                  <button type="button" onclick="window.therapairFeedbackToggle()"
                    style="flex: 1; padding: 10px 16px; border: 1px solid #E5E7EB; background: white; color: #1F2937; border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer; transition: background 0.15s; min-height: 44px;">
                    Cancel
                  </button>
                  <button type="submit" id="therapair-feedback-submit"
                    style="flex: 1; padding: 10px 16px; border: none; background: ${CONFIG.primaryColor}; color: white; border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer; transition: background 0.15s; min-height: 44px; disabled:opacity:0.5;">
                    Submit
                  </button>
                </div>
              </form>

              <div id="therapair-feedback-success" style="display: none; text-align: center; padding: 24px 0;">
                <div style="width: 64px; height: 64px; background: ${CONFIG.primaryColor}10; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                  <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="${CONFIG.primaryColor}" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                  </svg>
                </div>
                <p style="color: #1F2937; font-size: 16px; line-height: 24px; margin: 0;">
                  Thanks — your note helps us improve Therapair.
                </p>
              </div>
            </div>
        </div>
    `;
  }

  // Get page context for tracking - captures which page feedback is from
  function getPageContext() {
    const context = {
      page_url: window.location.href,
      page_path: window.location.pathname,
      page_title: document.title,
      scroll_percent: Math.round((window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100),
      viewport_height: window.innerHeight,
      viewport_width: window.innerWidth,
      timestamp: new Date().toISOString(),
      referrer: document.referrer || null
    };

    // Detect page type/section
    const path = window.location.pathname.toLowerCase();
    if (path.includes('/sandbox')) {
      context.section = 'sandbox';
      // Try to get sandbox-specific context
      try {
        if (typeof currentQuestionIndex !== 'undefined') {
          context.current_step = currentQuestionIndex + 1;
          context.total_steps = typeof questions !== 'undefined' ? questions.length : null;
          if (typeof questions !== 'undefined' && questions[currentQuestionIndex]) {
            const currentQuestion = questions[currentQuestionIndex];
            context.question_id = currentQuestion.id;
            context.question_text = currentQuestion.text;
          }
        }
      } catch (e) {
        // Silently fail
      }
    } else if (path.includes('/survey') || path.includes('/research')) {
      context.section = 'survey';
    } else if (path.includes('/documentation')) {
      context.section = 'documentation';
    } else if (path.includes('/legal')) {
      context.section = 'legal';
    } else if (path === '/' || path === '/index.html') {
      context.section = 'home';
    } else {
      context.section = 'other';
    }

    // Extract UTM parameters
    const urlParams = new URLSearchParams(window.location.search);
    context.utm_source = urlParams.get('utm_source') || null;
    context.utm_medium = urlParams.get('utm_medium') || null;
    context.utm_campaign = urlParams.get('utm_campaign') || null;
    context.utm_content = urlParams.get('utm_content') || null;
    context.utm_term = urlParams.get('utm_term') || null;

    return context;
  }

  // Store page context when modal opens
  let feedbackPageContext = null;

  // Toggle modal
  window.therapairFeedbackToggle = function() {
    const modal = document.getElementById('therapair-feedback-modal');
    if (modal) {
      const isOpening = modal.style.display === 'none';
      modal.style.display = isOpening ? 'block' : 'none';
      
      // Capture page context when opening
      if (isOpening) {
        feedbackPageContext = getPageContext();
        console.log('[therapair-feedback] Page context captured:', feedbackPageContext);
        
        // Add click-outside-to-close listener
        setTimeout(() => {
          const handleClickOutside = function(event) {
            const container = document.getElementById('therapair-feedback-container');
            const modal = document.getElementById('therapair-feedback-modal');
            const toggleBtn = document.getElementById('therapair-feedback-toggle-btn');
            
            if (container && modal && modal.style.display !== 'none') {
              const clickedElement = event.target;
              const isClickInsideModal = modal.contains(clickedElement);
              const isClickOnToggle = toggleBtn && toggleBtn.contains(clickedElement);
              
              if (!isClickInsideModal && !isClickOnToggle) {
                window.therapairFeedbackToggle();
                document.removeEventListener('click', handleClickOutside);
              }
            }
          };
          
          setTimeout(() => {
            document.addEventListener('click', handleClickOutside);
          }, 100);
        }, 0);
      }
    }
  };

  // Add mobile responsive styles
  function addResponsiveStyles() {
    if (document.getElementById('therapair-feedback-styles')) return;
    
    const style = document.createElement('style');
    style.id = 'therapair-feedback-styles';
    style.textContent = `
      @media (max-width: 640px) {
        #therapair-feedback-container {
          bottom: 16px !important;
          right: 16px !important;
        }
        #therapair-feedback-modal {
          width: calc(100vw - 2rem) !important;
          max-width: 360px !important;
        }
      }
    `;
    document.head.appendChild(style);
  }

  // Set rating
  window.therapairSetRating = function(val) {
    document.getElementById('therapair-feedback-rating').value = val;
    document.querySelectorAll('.therapair-rating-btn').forEach(btn => {
      const btnVal = parseInt(btn.dataset.rating);
      if (btnVal <= val) {
        btn.style.opacity = '1';
        btn.style.color = CONFIG.primaryColor;
        btn.style.transform = 'scale(1.1)';
        btn.querySelector('svg path').setAttribute('fill', 'currentColor');
      } else {
        btn.style.opacity = '0.4';
        btn.style.color = '#E5E7EB';
        btn.style.transform = 'scale(1)';
        btn.querySelector('svg path').setAttribute('fill', 'none');
      }
    });
    document.getElementById('therapair-feedback-submit').disabled = false;
  };

  // Toggle tag
  const selectedTags = [];
  window.therapairToggleTag = function(tag) {
    const btn = document.querySelector(`.therapair-tag-btn[data-tag="${tag}"]`);
    const index = selectedTags.indexOf(tag);
    
    if (index > -1) {
      selectedTags.splice(index, 1);
      btn.style.background = 'white';
      btn.style.borderColor = '#E5E7EB';
      btn.style.color = '#1F2937';
    } else {
      selectedTags.push(tag);
      btn.style.background = `${CONFIG.primaryColor}10`;
      btn.style.borderColor = CONFIG.primaryColor;
      btn.style.color = CONFIG.primaryColor;
    }
    
    document.getElementById('therapair-feedback-tags').value = JSON.stringify(selectedTags);
  };

  // Submit feedback
  window.therapairFeedbackSubmit = async function(e) {
    e.preventDefault();
    const form = e.target;
    const submitBtn = document.getElementById('therapair-feedback-submit');
    const ratingInput = document.getElementById('therapair-feedback-rating');
    const rating = ratingInput ? ratingInput.value : null;
    const comment = document.getElementById('therapair-feedback-comment')?.value || '';
    const tagsInput = document.getElementById('therapair-feedback-tags');
    let tags = [];
    try {
      tags = tagsInput ? JSON.parse(tagsInput.value || '[]') : [];
    } catch (e) {
      console.error('Error parsing tags:', e);
      tags = [];
    }

    if (!rating || rating === '' || isNaN(parseInt(rating))) {
      alert('Please select a rating');
      return;
    }
    
    const ratingNum = parseInt(rating);
    if (ratingNum < 1 || ratingNum > 6) {
      alert('Please select a valid rating (1-6)');
      return;
    }

    submitBtn.disabled = true;
    submitBtn.textContent = 'Sending...';

    try {
      // Use stored context or get fresh context
      const pageContext = feedbackPageContext || getPageContext();
      
      // Get tracking identifiers from sessionStorage or URL
      const urlParams = new URLSearchParams(window.location.search);
      const email = sessionStorage.getItem('therapair_research_email') || 
                   urlParams.get('email') || 
                   urlParams.get('utm_email');
      const sessionId = sessionStorage.getItem('therapair_session_id') || 
                       urlParams.get('session_id');
      
      // Generate or get tracking ID (unique identifier for this user session)
      let trackingId = sessionStorage.getItem('therapair_tracking_id');
      if (!trackingId) {
        trackingId = typeof crypto !== 'undefined' && crypto.randomUUID 
          ? crypto.randomUUID() 
          : `track-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
        sessionStorage.setItem('therapair_tracking_id', trackingId);
      }
      
      const payload = {
        rating: ratingNum,
        comment: comment || '',
        tags: tags || [],
        page_url: pageContext.page_url,
        page_path: pageContext.page_path,
        page_title: pageContext.page_title,
        created_at: new Date().toISOString(),
        // Tracking identifiers
        therapist_email: email || null,
        session_id: sessionId || null,
        tracking_id: trackingId, // Unique tracking ID for linking feedback
        // Page context
        section: pageContext.section || 'unknown',
        scroll_percent: pageContext.scroll_percent || 0,
        viewport_height: pageContext.viewport_height || null,
        viewport_width: pageContext.viewport_width || null,
        referrer: pageContext.referrer || null,
        // UTM parameters
        utm_source: pageContext.utm_source || null,
        utm_medium: pageContext.utm_medium || null,
        utm_campaign: pageContext.utm_campaign || null,
        utm_content: pageContext.utm_content || null,
        utm_term: pageContext.utm_term || null,
        // Sandbox-specific context (if available)
        current_step: pageContext.current_step || null,
        total_steps: pageContext.total_steps || null,
        question_id: pageContext.question_id || null,
        question_text: pageContext.question_text || null
      };

      console.log('[feedback-widget] Submitting payload:', payload);
      console.log('[feedback-widget] Endpoint:', CONFIG.endpoint);
      console.log('[feedback-widget] Full URL:', window.location.origin + CONFIG.endpoint);
      
      try {
        const response = await fetch(CONFIG.endpoint, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(payload),
          credentials: 'same-origin'
        });

        console.log('[feedback-widget] Response status:', response.status, response.statusText);

        // Check if response is ok before parsing JSON
        if (!response.ok) {
          const errorText = await response.text();
          console.error('[feedback-widget] API error response:', response.status, errorText);
          let errorMessage = `Server error: ${response.status}`;
          try {
            const errorJson = JSON.parse(errorText);
            errorMessage = errorJson.error || errorJson.message || errorMessage;
          } catch (e) {
            errorMessage = errorText || errorMessage;
          }
          throw new Error(errorMessage);
        }

        const result = await response.json();
        console.log('[feedback-widget] API response:', result);

        if (result.ok) {
        form.style.display = 'none';
        document.getElementById('therapair-feedback-success').style.display = 'block';
        setTimeout(() => {
          window.therapairFeedbackToggle();
          form.reset();
          form.style.display = 'block';
          document.getElementById('therapair-feedback-success').style.display = 'none';
          submitBtn.disabled = false;
          submitBtn.textContent = 'Submit';
          selectedTags.length = 0;
          document.querySelectorAll('.therapair-tag-btn').forEach(btn => {
            btn.style.background = 'white';
            btn.style.borderColor = '#E5E7EB';
            btn.style.color = '#1F2937';
          });
          document.querySelectorAll('.therapair-rating-btn').forEach(btn => {
            btn.style.opacity = '0.4';
            btn.style.color = '#E5E7EB';
            btn.style.transform = 'scale(1)';
            const svgPath = btn.querySelector('svg path');
            if (svgPath) {
              svgPath.setAttribute('fill', 'none');
            }
          });
        }, 2000);
        } else {
          throw new Error(result.error || result.message || 'Failed to submit');
        }
      } catch (fetchError) {
        console.error('[feedback-widget] Fetch error:', fetchError);
        throw fetchError;
      }
    } catch (err) {
      console.error('Feedback submission error:', err);
      console.error('Error details:', {
        message: err.message,
        stack: err.stack,
        name: err.name
      });
      
      // Show more helpful error message
      let errorMsg = 'Something went wrong. Please try again.';
      if (err.message) {
        errorMsg = err.message;
        // Truncate long error messages
        if (errorMsg.length > 100) {
          errorMsg = errorMsg.substring(0, 100) + '...';
        }
      }
      alert(errorMsg);
      submitBtn.disabled = false;
      submitBtn.textContent = 'Submit';
    }
  };

  // Initialize widget
  function init() {
    // Remove existing widget if present (prevent duplicates)
    const existing = document.getElementById('therapair-feedback-container');
    if (existing) {
      existing.remove();
    }

    // Add responsive styles
    addResponsiveStyles();

    // Add widget to page
    const widgetHTML = createWidgetHTML();
    document.body.insertAdjacentHTML('beforeend', widgetHTML);

    // Initialize submit button as disabled
    const submitBtn = document.getElementById('therapair-feedback-submit');
    if (submitBtn) {
      submitBtn.disabled = true;
    }
  }

  // Initialize when DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();

