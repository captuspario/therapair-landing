const state = {
  token: new URLSearchParams(window.location.search).get("token") || "",
  sessionId: typeof crypto !== "undefined" && crypto.randomUUID ? crypto.randomUUID() : `session-${Date.now()}`,
  consentVersion: null,
  therapist: null,
  utm: extractUtmParameters(),
  lastSubmission: null,
  isSubmitting: false,
  isPreview: false,
};

const elements = {
  form: document.getElementById("therapist-survey"),
  steps: Array.from(document.querySelectorAll(".survey-step")),
  nextButton: document.querySelector('[data-action="next"]'),
  backButton: document.querySelector('[data-action="back"]'),
  progressFill: document.querySelector("[data-progress-fill]"),
  progressLabel: document.querySelector("[data-progress-label]"),
  globalError: document.querySelector("[data-global-error]"),
  submissionStates: Array.from(document.querySelectorAll("[data-submission]")),
  surveyFooter: document.querySelector(".survey-footer"),
  surveyProgress: document.querySelector(".survey-progress"),
  surveyHeader: document.querySelector(".survey-header"),
  surveyHeaderEyebrow: document.querySelector(".survey-header__eyebrow"),
  surveyHeaderTitle: document.querySelector(".survey-header h1"),
  surveyHeaderLede: document.querySelector(".survey-header__lede"),
};

let currentStepIndex = 0;

async function initialiseSurvey() {
  configureOptionCards();
  configureDependentInputs();
  configureProfileDetailSlider();
  configurePricingSliders();
  bindNavigationHandlers();
  bindSubmissionControls();
  configureCompletionLinks(); // Set up tracking links early

  if (!state.token) {
    disableSurvey("This link is missing its secure access token. Please use the personalised link we emailed you.");
    return;
  }

  try {
    const sessionPayload = await fetchSessionDetails(state.token);
    state.consentVersion = sessionPayload.consent?.version || "2025-11-13";
    state.therapist = sessionPayload.data || null;
    state.isPreview = isPreviewSession();
    applyVariantVisibility();
    configureFutureContactToggle();
    refreshSteps();
    ensureActiveStep();
  } catch (error) {
    console.error(error);
    disableSurvey(error.message || "We couldn't verify this link. Please contact the Therapair team for a fresh invite.");
    return;
  }

  updateNavigation();
  updateProgress();
  configureCompletionLinks(); // Update links after state is loaded
}

function bindNavigationHandlers() {
  elements.nextButton?.addEventListener("click", async () => {
    if (state.isSubmitting) return;

    refreshSteps();
    const step = elements.steps[currentStepIndex];
    const validation = validateStep(step);

    if (!validation.valid) {
      displayGlobalError(validation.message || "Please complete this step before continuing.");
      return;
    }

    clearGlobalError();

    if (currentStepIndex === elements.steps.length - 1) {
      await submitSurvey();
      return;
    }

    goToStep(currentStepIndex + 1);
  });

  elements.backButton?.addEventListener("click", () => {
    if (state.isSubmitting) return;
    if (currentStepIndex === 0) return;

    clearGlobalError();
    refreshSteps();
    goToStep(currentStepIndex - 1);
  });
}

function bindSubmissionControls() {
  const retryButton = document.querySelector('[data-action="retry"]');
  retryButton?.addEventListener("click", () => {
    toggleSurveyVisibility(true);
    showSubmissionState(null);
    state.isSubmitting = false;
    updateNavigation();
  });

  // Completion links are configured in configureCompletionLinks()
  // which is called after survey state is loaded
}

/**
 * Configure completion page links with tracking
 * Called after survey state is loaded to set up tracking URLs
 */
function configureCompletionLinks() {
  // Configure sandbox demo button
  const sandboxButton = document.getElementById("sandbox-demo-button");
  if (sandboxButton) {
    // Build tracking URL with UTM parameters from survey
    const url = new URL(sandboxButton.href, window.location.origin);
    
    // Preserve existing UTM parameters from survey link
    if (state.utm.utm_source) url.searchParams.set('utm_source', state.utm.utm_source);
    if (state.utm.utm_medium) url.searchParams.set('utm_medium', state.utm.utm_medium);
    if (state.utm.utm_campaign) url.searchParams.set('utm_campaign', state.utm.utm_campaign);
    if (state.utm.utm_email) url.searchParams.set('utm_email', state.utm.utm_email);
    
    // Add specific tracking for sandbox demo click
    url.searchParams.set('utm_content', 'sandbox_demo');
    url.searchParams.set('utm_term', 'survey_completion');
    url.searchParams.set('survey_completed', 'true');
    url.searchParams.set('session_id', state.sessionId);
    
    // Update href with tracking parameters
    sandboxButton.href = url.toString();
    
    // Track click on navigation (only add listener once)
    if (!sandboxButton.dataset.trackingConfigured) {
      sandboxButton.addEventListener("click", () => {
        trackSandboxClick(url.toString());
      });
      sandboxButton.dataset.trackingConfigured = 'true';
    }
  }

  // Configure documentation link (now a text link, not a button)
  const docLink = document.querySelector('[data-track="documentation"]');
  if (docLink && !docLink.dataset.trackingConfigured) {
    const url = new URL(docLink.href, window.location.origin);
    if (state.utm.utm_source) url.searchParams.set('utm_source', state.utm.utm_source);
    if (state.utm.utm_medium) url.searchParams.set('utm_medium', state.utm.utm_medium);
    if (state.utm.utm_campaign) url.searchParams.set('utm_campaign', state.utm.utm_campaign);
    if (state.utm.utm_email) url.searchParams.set('utm_email', state.utm.utm_email);
    url.searchParams.set('utm_content', 'documentation');
    url.searchParams.set('utm_term', 'survey_completion');
    docLink.href = url.toString();
    docLink.dataset.trackingConfigured = 'true';
  }
}

/**
 * Track sandbox demo click for analytics
 */
function trackSandboxClick(url) {
  // Store in sessionStorage for sandbox page to detect
  if (window.sessionStorage) {
    window.sessionStorage.setItem('therapair_sandbox_visit', '1');
    window.sessionStorage.setItem('therapair_survey_completed', '1');
    window.sessionStorage.setItem('therapair_session_id', state.sessionId);
  }
  
  // Optional: Send tracking event to analytics endpoint
  // This can be enhanced later with a dedicated tracking endpoint
  try {
    if (navigator.sendBeacon) {
      const trackingData = {
        event: 'survey_completion_sandbox_click',
        session_id: state.sessionId,
        therapist_email: state.therapist?.email || null,
        utm: state.utm,
        timestamp: new Date().toISOString(),
        url: url
      };
      navigator.sendBeacon('/api/research/track-event.php', JSON.stringify(trackingData));
    }
  } catch (e) {
    // Silently fail - tracking shouldn't block navigation
    console.debug('Tracking failed:', e);
  }
}

function configureOptionCards() {
  elements.form?.addEventListener("change", (event) => {
    const target = event.target;
    if (!(target instanceof HTMLInputElement)) return;

    if (target.type === "checkbox" || target.type === "radio") {
      const name = target.name;
      const inputs = elements.form.querySelectorAll(`input[name="${cssEscape(name)}"]`);
      inputs.forEach((input) => {
        const card = input.closest(".option-card");
        if (card) {
          card.classList.toggle("selected", input.checked);
        }
      });

      handleDependentVisibility(name);

      if (name === "future_contact") {
        updateEmailRequirement();
      }
    }
  });
}

function configureDependentInputs() {
  const containers = elements.form?.querySelectorAll("[data-dependent]") || [];
  containers.forEach((container) => {
    const fieldName = container.getAttribute("data-dependent");
    if (!fieldName) return;
    handleDependentVisibility(fieldName);
  });
}

function configureFutureContactToggle() {
  updateEmailRequirement();
}

function configureProfileDetailSlider() {
  const slider = document.getElementById("profileDetailSlider");
  const display = document.getElementById("profileDetailDisplay");
  if (!slider || !display) return;

  const valueTexts = {
    1: "— Keep it very simple",
    2: "— Minimal details",
    3: "— A few paragraphs is fine",
    4: "— Some context helpful",
    5: "— Moderate depth",
    6: "— Good detail level",
    7: "— Detailed with context",
    8: "— Very detailed",
    9: "— Comprehensive profile",
    10: "— The more detail the better",
  };

  function updateDisplay() {
    const value = parseInt(slider.value, 10);
    const numberSpan = display.querySelector(".slider-value-number");
    const textSpan = display.querySelector(".slider-value-text");
    if (numberSpan) numberSpan.textContent = value;
    if (textSpan) textSpan.textContent = valueTexts[value] || "";
  }

  slider.addEventListener("input", updateDisplay);
  updateDisplay(); // Set initial display
}

function configurePricingSliders() {
  // Fee per match slider ($10-$100 per session, step 5)
  const feeSlider = document.getElementById("feePerMatchSlider");
  const feeDisplay = document.getElementById("feePerMatchDisplay");
  if (feeSlider && feeDisplay) {
    function updateFeeDisplay() {
      const value = parseInt(feeSlider.value, 10);
      const numberSpan = feeDisplay.querySelector(".slider-value-number");
      if (numberSpan) numberSpan.textContent = `$${value}`;
    }
    feeSlider.addEventListener("input", updateFeeDisplay);
    updateFeeDisplay(); // Set initial display
  }

  // Monthly subscription slider ($20-$200, step 10)
  const subSlider = document.getElementById("monthlySubscriptionSlider");
  const subDisplay = document.getElementById("monthlySubscriptionDisplay");
  if (subSlider && subDisplay) {
    function updateSubDisplay() {
      const value = parseInt(subSlider.value, 10);
      const numberSpan = subDisplay.querySelector(".slider-value-number");
      if (numberSpan) numberSpan.textContent = `$${value}`;
    }
    subSlider.addEventListener("input", updateSubDisplay);
    updateSubDisplay(); // Set initial display
  }
}

function updateEmailRequirement() {
  const futureContactChoice = elements.form?.querySelector('input[name="future_contact"]:checked')?.value;
  const emailField = elements.form?.querySelector('[data-step="contact-details"] input[name="email"]');
  if (!emailField) return;

  if (futureContactChoice === "Yes") {
    emailField.required = true;
    emailField.parentElement?.classList.remove("hidden");
  } else {
    emailField.required = false;
    emailField.value = "";
  }
}

function handleDependentVisibility(fieldName) {
  const container = elements.form?.querySelector(`[data-dependent="${fieldName}"]`);
  if (!container) return;

  const inputs = elements.form.querySelectorAll(`input[name="${cssEscape(fieldName)}"]`);
  const otherInput = container.querySelector("input, textarea");
  const otherSelected = Array.from(inputs).some((input) => input.checked && input.value === "Other");

  container.classList.toggle("hidden", !otherSelected);
  if (otherInput) {
    if (otherSelected) {
      otherInput.setAttribute("required", "required");
    } else {
      otherInput.removeAttribute("required");
      otherInput.value = "";
    }
  }
}

function goToStep(index) {
  refreshSteps();

  if (index < 0 || index >= elements.steps.length) return;

  elements.steps[currentStepIndex]?.classList.remove("active");
  currentStepIndex = index;
  elements.steps[currentStepIndex]?.classList.add("active");

  updateNavigation();
  updateProgress();
  elements.steps[currentStepIndex]?.scrollIntoView({ behavior: "smooth", block: "start" });
}

function updateNavigation() {
  refreshSteps();
  const isFirstStep = currentStepIndex === 0;
  const isLastStep = currentStepIndex === elements.steps.length - 1;

  if (elements.backButton) {
    elements.backButton.disabled = isFirstStep;
    elements.backButton.textContent = isFirstStep ? "Back" : "Back";
    elements.backButton.classList.toggle("hidden", isFirstStep);
  }

  if (elements.nextButton) {
    elements.nextButton.textContent = isLastStep ? "Submit responses" : "Continue";
  }

  updateSurveyHeader();
}

function updateProgress() {
  if (!elements.progressFill || !elements.progressLabel) return;

  refreshSteps();
  const totalSteps = elements.steps.length;
  const progress = ((currentStepIndex + 1) / totalSteps) * 100;
  elements.progressFill.style.width = `${progress}%`;
  elements.progressLabel.textContent = `Step ${currentStepIndex + 1} of ${totalSteps}`;
}

function validateStep(step) {
  if (!step) return { valid: true };

  const requiredInputs = Array.from(step.querySelectorAll("[required]"));
  const processedGroupNames = new Set();

  for (const input of requiredInputs) {
    if (!(input instanceof HTMLInputElement || input instanceof HTMLTextAreaElement)) continue;

    const { type, name, value } = input;

    if ((type === "radio" || type === "checkbox") && name) {
      if (processedGroupNames.has(name)) continue;
      processedGroupNames.add(name);

      const groupInputs = step.querySelectorAll(`input[name="${cssEscape(name)}"]`);
      const anyChecked = Array.from(groupInputs).some((option) => option.checked);
      if (!anyChecked) {
        return { valid: false, message: "Please choose at least one option to continue." };
      }
    } else if (type === "email") {
      if (input.required && !value.trim()) {
        return { valid: false, message: "Please provide an email address so we can stay in touch." };
      }
      if (value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value.trim())) {
        return { valid: false, message: "That email address doesn’t look quite right. Please double check it." };
      }
    } else if (!value || !value.trim()) {
      return { valid: false, message: "Please add a response before continuing." };
    }
  }

  return { valid: true };
}

function updateSurveyHeader() {
  const header = elements.surveyHeader;
  if (!header) return;

  const isFirstStep = currentStepIndex === 0;
  
  // On first step: show full header (eyebrow + h1 + lede)
  // On subsequent steps: show only eyebrow "Therapair User Research"
  header.classList.toggle("is-compact", !isFirstStep);

  // Ensure eyebrow always shows "Therapair User Research"
  if (elements.surveyHeaderEyebrow) {
    elements.surveyHeaderEyebrow.textContent = "Therapair User Research";
  }
}

async function submitSurvey() {
  if (state.isSubmitting) return;
  state.isSubmitting = true;

  clearGlobalError();
  toggleSurveyVisibility(false);
  showSubmissionState("pending");

  const payload = buildSubmissionPayload();

  try {
    const response = await fetch("/api/research/response.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-Requested-With": "XMLHttpRequest",
      },
      body: JSON.stringify(payload),
      credentials: "same-origin",
    });

    if (!response.ok) {
      const errorBody = await safeJson(response);
      throw new Error(errorBody?.error || "The server returned an unexpected error.");
    }

    const result = await response.json();
    if (!result.success) {
      throw new Error(result.error || "We couldn’t store your answers. Please try again.");
    }

    state.lastSubmission = payload;
    
    // Redirect to standalone success page
    const successUrl = new URL('/research/survey/success.html', window.location.origin);
    // Preserve UTM parameters
    if (state.utm.utm_source) successUrl.searchParams.set('utm_source', state.utm.utm_source);
    if (state.utm.utm_medium) successUrl.searchParams.set('utm_medium', state.utm.utm_medium);
    if (state.utm.utm_campaign) successUrl.searchParams.set('utm_campaign', state.utm.utm_campaign);
    if (state.utm.utm_email) successUrl.searchParams.set('utm_email', state.utm.utm_email);
    // Store session ID and email in sessionStorage before redirect
    if (state.sessionId) {
      sessionStorage.setItem('therapair_session_id', state.sessionId);
      successUrl.searchParams.set('session_id', state.sessionId);
    }
    if (state.therapist?.email) {
      sessionStorage.setItem('therapair_research_email', state.therapist.email);
    }
    successUrl.searchParams.set('utm_content', 'survey_completion');
    
    window.location.href = successUrl.toString();
  } catch (error) {
    console.error("Submission failed", error);
    displaySubmissionError(error.message);
  } finally {
    state.isSubmitting = false;
  }
}

function buildSubmissionPayload() {
  const formData = new FormData(elements.form);

  const pickAll = (name) => formData.getAll(name).map((value) => value.trim()).filter(Boolean);
  const pickSingle = (name) => {
    const value = formData.get(name);
    return typeof value === "string" ? value.trim() : "";
  };

  const survey = {
    profession: pickSingle("profession"),
    profession_other: pickSingle("profession_other"),
    years_practice: pickSingle("years_practice"),
    client_types: pickAll("client_types"),
    client_types_other: pickSingle("client_types_other"),
    modalities: pickAll("modalities"),
    modalities_other: pickSingle("modalities_other"),
    clients_find_you: pickAll("clients_find_you"),
    clients_find_you_other: pickSingle("clients_find_you_other"),
    match_factors: pickAll("match_factors"),
    match_factors_other: pickSingle("match_factors_other"),
    biggest_gap: pickSingle("biggest_gap"),
    screens_clients: pickSingle("screens_clients"),
    open_to_sharing: pickSingle("open_to_sharing"),
    questions_matter: pickAll("questions_matter"),
    questions_matter_other: pickSingle("questions_matter_other"),
    personality_test: pickSingle("personality_test"),
    too_personal: pickAll("too_personal"),
    too_personal_other: pickSingle("too_personal_other"),
    profile_detail_level: pickSingle("profile_detail_level"),
    onboarding_time: pickSingle("onboarding_time"),
    free_listing_interest: pickSingle("free_listing_interest"),
    profile_intent: pickSingle("profile_intent"),
    future_contact: pickSingle("future_contact"),
    email: pickSingle("email"),
    value_fee_per_match: pickSingle("value_fee_per_match"),
    value_monthly_subscription: pickSingle("value_monthly_subscription"),
    comments: pickSingle("comments"),
  };

  const metadata = {
    utm: state.utm,
    referrer: document.referrer || null,
    landing_path: window.location.pathname + window.location.search,
    therapist_directory_id: state.therapist?.directory_page_id || null,
    therapist_research_id: state.therapist?.therapist_research_id || null,
    sandbox_visit: window.sessionStorage?.getItem("therapair_sandbox_visit") === "1",
    user_agent: navigator.userAgent,
  };

  const consent = {
    accepted: true,
    version: state.consentVersion || "2025-11-13",
    timestamp: new Date().toISOString(),
  };

  if (!survey.future_contact) {
    survey.future_contact = "Yes";
  }

  if (!survey.email) {
    survey.email = state.therapist?.email || "";
  }

  return {
    token: state.token,
    session_id: state.sessionId,
    consent,
    survey,
    therapist: state.therapist,
    metadata,
  };
}

function toggleSurveyVisibility(visible) {
  elements.form?.classList.toggle("hidden", !visible);
  elements.surveyFooter?.classList.toggle("hidden", !visible);
  elements.surveyProgress?.classList.toggle("hidden", !visible);
}

function showSubmissionState(stateKey) {
  elements.submissionStates.forEach((section) => {
    const key = section.getAttribute("data-submission");
    if (!stateKey) {
      section.classList.remove("active");
    } else {
      section.classList.toggle("active", key === stateKey);
    }
  });
  
  // Hide survey progress and form when showing success state
  const surveyProgress = document.querySelector(".survey-progress");
  const surveyForm = document.getElementById("therapist-survey");
  
  if (stateKey === "success") {
    if (surveyProgress) {
      surveyProgress.style.display = "none";
      surveyProgress.classList.add("hidden");
    }
    if (surveyForm) {
      surveyForm.style.display = "none";
      surveyForm.classList.add("hidden");
    }
  } else {
    if (surveyProgress) {
      surveyProgress.style.display = "";
      surveyProgress.classList.remove("hidden");
    }
    if (surveyForm) {
      surveyForm.style.display = "";
      surveyForm.classList.remove("hidden");
    }
  }
}

function displaySubmissionError(message) {
  showSubmissionState("error");
  const errorSection = document.querySelector('[data-submission="error"] p');
  if (errorSection) {
    errorSection.innerHTML = `${message || "Something went wrong while saving your answers."} Please email <a href="mailto:contact@therapair.com.au" class="link">contact@therapair.com.au</a> if it keeps happening.`;
  }
}

function disableSurvey(message) {
  displayGlobalError(message);
  elements.nextButton?.setAttribute("disabled", "disabled");
  elements.backButton?.setAttribute("disabled", "disabled");
  elements.form?.querySelectorAll("input, textarea, button").forEach((input) => {
    input.setAttribute("disabled", "disabled");
  });
}

function displayGlobalError(message) {
  if (!elements.globalError) return;
  elements.globalError.textContent = message;
  elements.globalError.classList.remove("hidden");
}

function clearGlobalError() {
  elements.globalError?.classList.add("hidden");
  if (elements.globalError) {
    elements.globalError.textContent = "";
  }
}

async function fetchSessionDetails(token) {
  const response = await fetch(`/api/research/session.php?token=${encodeURIComponent(token)}`, {
    method: "GET",
    headers: {
      Accept: "application/json",
    },
    credentials: "same-origin",
  });

  if (!response.ok) {
    const errorBody = await safeJson(response);
    throw new Error(errorBody?.error || "We couldn’t verify your invite link.");
  }

  const payload = await response.json();
  if (!payload.success) {
    throw new Error(payload.error || "This invite link is no longer valid.");
  }

  return payload;
}

// Header removed - personaliseHeader function no longer needed

function extractUtmParameters() {
  const params = new URLSearchParams(window.location.search);
  const utmKeys = ["utm_source", "utm_medium", "utm_campaign", "utm_content", "utm_term"];
  const utm = {};
  utmKeys.forEach((key) => {
    const value = params.get(key);
    if (value) {
      utm[key] = value;
    }
  });
  return utm;
}

function cssEscape(value) {
  if (typeof CSS !== "undefined" && typeof CSS.escape === "function") {
    return CSS.escape(value);
  }
  return value.replace(/([ #;?%&,.+*~\':"!^$[\]()=>|\/@])/g, "\\$1");
}

async function safeJson(response) {
  try {
    return await response.json();
  } catch (_) {
    return null;
  }
}

document.addEventListener("DOMContentLoaded", initialiseSurvey);

function refreshSteps() {
  elements.steps = Array.from(document.querySelectorAll(".survey-step"));
}

function ensureActiveStep() {
  const steps = elements.steps;
  if (steps.length === 0) {
    currentStepIndex = 0;
    return;
  }

  const activeIndex = steps.findIndex((step) => step.classList.contains("active"));
  if (activeIndex === -1) {
    steps[0].classList.add("active");
    currentStepIndex = 0;
  } else {
    currentStepIndex = activeIndex;
  }
}

function applyVariantVisibility() {
  if (state.isPreview) {
    return;
  }

  const removableSteps = elements.form?.querySelectorAll("[data-preview-only='true']") || [];
  removableSteps.forEach((step) => step.remove());
  refreshSteps();
  if (currentStepIndex >= elements.steps.length) {
    currentStepIndex = Math.max(0, elements.steps.length - 1);
  }
}

function isPreviewSession() {
  return (
    state.token === "preview" ||
    state.therapist?.therapist_research_id === "preview" ||
    state.therapist?.therapist_id === "VIC-PREVIEW-0000"
  );
}

