<?php
/**
 * Therapair Landing Page - Form Submission Handler with AI-Powered Personalisation
 * Sends emails to admin and AI-generated personalised confirmation to user
 */

// Debug logging removed - emails working correctly

// Load configuration
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/email-template-base.php';

// Prevent direct access
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /');
    exit;
}

// ============================================
// HELPER FUNCTION - Define early
// ============================================
function sanitize($data)
{
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate a JWT-like token for research survey access
 * Matches the Node.js implementation in research/scripts
 */
function generateResearchToken($payload)
{
    $secret = defined('RESEARCH_TOKEN_SECRET') ? RESEARCH_TOKEN_SECRET : '';
    if (empty($secret)) {
        error_log('Warning: RESEARCH_TOKEN_SECRET not defined, cannot generate token');
        return null;
    }
    
    // Encode header
    $header = ['alg' => 'HS256', 'typ' => 'JWT'];
    $headerB64 = rtrim(strtr(base64_encode(json_encode($header)), '+/', '-_'), '=');
    
    // Encode payload
    $payloadB64 = rtrim(strtr(base64_encode(json_encode($payload)), '+/', '-_'), '=');
    
    // Create signature
    $signedPortion = $headerB64 . '.' . $payloadB64;
    $signature = hash_hmac('sha256', $signedPortion, $secret, true);
    $signatureB64 = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');
    
    return $signedPortion . '.' . $signatureB64;
}

// Use constants from config with fallbacks
$ADMIN_EMAIL = defined('ADMIN_EMAIL') ? ADMIN_EMAIL : 'contact@therapair.com.au';
$FROM_EMAIL = defined('FROM_EMAIL') ? FROM_EMAIL : 'contact@therapair.com.au';
$FROM_NAME = defined('FROM_NAME') ? FROM_NAME : 'Therapair Team';
$WEBSITE_URL = defined('WEBSITE_URL') ? WEBSITE_URL : 'https://therapair.com.au';
$RESEND_API_KEY = defined('RESEND_API_KEY') ? RESEND_API_KEY : '';
$USE_RESEND = defined('USE_RESEND') ? USE_RESEND : true;
$OPENAI_API_KEY = defined('OPENAI_API_KEY') ? OPENAI_API_KEY : '';
$USE_AI_PERSONALIZATION = defined('USE_AI_PERSONALIZATION') ? USE_AI_PERSONALIZATION : false;
$AI_MODEL = defined('AI_MODEL') ? AI_MODEL : 'gpt-4o-mini';
$USE_NOTION_SYNC = defined('USE_NOTION_SYNC') ? USE_NOTION_SYNC : false;

// Load Notion sync handler
if ($USE_NOTION_SYNC && file_exists(__DIR__ . '/notion-sync.php')) {
    require_once __DIR__ . '/notion-sync.php';
}

// Debug: Log all POST data (remove after testing)
error_log("Form submission received: " . print_r($_POST, true));

// Get form data
$audience = isset($_POST['Audience_Type']) ? sanitize($_POST['Audience_Type']) : '';
$email = isset($_POST['Email']) ? sanitize($_POST['Email']) : '';
$therapyInterests = isset($_POST['Therapy_Interests']) ? sanitize($_POST['Therapy_Interests']) : '';
$timestamp = date('Y-m-d H:i:s');

// Debug validation
error_log("Audience: '{$audience}', Email: '{$email}'");

// Validate required fields
if (empty($audience)) {
    error_log("Validation failed: audience is empty");
    header('Location: /?error=no-audience');
    exit;
}

if (empty($email)) {
    error_log("Validation failed: email is empty");
    header('Location: /?error=no-email');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    error_log("Validation failed: invalid email format");
    header('Location: /?error=invalid-email');
    exit;
}

// Honeypot spam check
if (!empty($_POST['_honey'])) {
    header('Location: /thank-you.html');
    exit; // Silent fail for bots
}

// Validate email consent (required for GDPR compliance)
if (empty($_POST['Email_Consent']) || $_POST['Email_Consent'] !== 'yes') {
    error_log("Validation failed: email consent not provided");
    header('Location: /?error=consent-required');
    exit;
}

// Collect all form data based on audience type
$formData = collectFormData($_POST, $audience);

// ============================================
// 1. ADMIN EMAIL DISABLED
// ============================================
// EOI submissions are saved to Notion databases, so admin email notifications
// are not needed. The contact@therapair.com.au inbox is only for direct messages.
// 
// Removed admin email notification to reduce inbox clutter.
// All EOI data is tracked in Notion databases for review.

// ============================================
// 2. SEND CONFIRMATION EMAIL TO USER (AI-POWERED)
// ============================================
// Set subject based on audience type
$userSubject = 'Thank you for your interest in Therapair';
if ($audience === 'organization') {
    $userSubject = 'Thank you for your interest in partnering with Therapair';
} elseif ($audience === 'other') {
    $userSubject = 'Thank you for your interest in supporting Therapair';
}

// Generate AI-powered personalised message or fallback to template
if ($USE_AI_PERSONALIZATION && !empty($OPENAI_API_KEY) && $OPENAI_API_KEY !== 'YOUR_OPENAI_API_KEY_HERE') {
    try {
        $personalisedContent = generateAIPersonalizedEmail($formData, $audience, $OPENAI_API_KEY, $AI_MODEL);
        if ($personalisedContent) {
            $userMessage = formatUserEmailWithAI($personalisedContent, $formData, $audience);
        } else {
            $userMessage = formatUserEmail($formData, $audience);
        }
    } catch (Exception $e) {
        error_log("AI email generation failed: " . $e->getMessage());
        $userMessage = formatUserEmail($formData, $audience);
    }
} else {
    $userMessage = formatUserEmail($formData, $audience);
}

// Add tracking URLs to email links
// Note: We'll use the actual email address in the tracking URL
// The webhook handler will look up the Notion page by email address
// For click tracking via track.php, we'll pass the email so it can look up the page
$userMessage = addTrackingToEmailLinks($userMessage, $email, $audience);

// Send user email via Resend or fallback to mail()
$userSent = sendEmailViaResend(
    $email,
    $userSubject,
    $userMessage,
    $FROM_EMAIL,
    $FROM_NAME,
    $ADMIN_EMAIL // Reply-To
);

// If Resend failed, try fallback
if (!$userSent) {
    error_log("Resend failed for user confirmation email, trying PHP mail() fallback");
    $userHeaders = "From: {$FROM_NAME} <{$FROM_EMAIL}>\r\n";
    $userHeaders .= "Reply-To: {$ADMIN_EMAIL}\r\n";
    $userHeaders .= "MIME-Version: 1.0\r\n";
    $userHeaders .= "Content-Type: text/html; charset=UTF-8\r\n";
    $userHeaders .= "X-Mailer: Therapair Automated Response\r\n";
    $userHeaders .= "X-Priority: 3\r\n";
    $userSent = @mail($email, $userSubject, $userMessage, $userHeaders);
    if ($userSent) {
        error_log("PHP mail() fallback succeeded for user confirmation email");
    } else {
        error_log("PHP mail() fallback also failed for user confirmation email");
    }
}

// Email system working correctly - logging removed

// ============================================
// 3. SYNC TO NOTION DATABASE
// ============================================
if ($USE_NOTION_SYNC) {
    // All EOI form submissions go to the EOI database
    $targetDb = defined('NOTION_DB_EOI') ? NOTION_DB_EOI : null;

    if (empty($targetDb)) {
        error_log("Notion sync skipped: NOTION_DB_EOI not defined. Audience: $audience");
    } else {
        error_log("Notion sync: Attempting to sync to EOI database ID: $targetDb, Audience: $audience, Email: " . ($formData['email'] ?? 'N/A'));
        $notionResult = syncToNotion($formData, $audience, $targetDb);
        if ($notionResult['success']) {
            $pageId = isset($notionResult['response']['id']) ? $notionResult['response']['id'] : 'unknown';
            error_log("Notion sync: Success! Entry created in EOI database. Page ID: $pageId");
            
            // If email was already sent, we can't add tracking URLs retroactively
            // But we can log the page ID for future reference
            // Future enhancement: Send a follow-up email with tracked links, or use Resend webhooks
        } else {
            error_log("Notion sync failed: " . print_r($notionResult, true));
            // Also log to a file for easier debugging
            $errorLogFile = __DIR__ . '/notion-sync-errors.log';
            file_put_contents($errorLogFile, date('Y-m-d H:i:s') . " - Audience: $audience, Email: " . ($formData['email'] ?? 'N/A') . "\n" . print_r($notionResult, true) . "\n\n", FILE_APPEND);
            // Continue anyway - don't block user experience
        }
    }
}

// NOTE: Since Notion sync happens after email sending, tracking URLs are added via formatUserEmail()
// which uses a placeholder that gets replaced. For now, we'll use email-based tracking via Resend webhooks
// or add tracking URLs that work without Notion page ID (using email hash instead)

// ============================================
// 4. REDIRECT TO THANK YOU PAGE
// ============================================
if ($adminSent && $userSent) {
    header('Location: /thank-you.html?status=success');
} else {
    // Still redirect even if emails fail (user doesn't see error)
    header('Location: /thank-you.html?status=sent');
}
exit;

// ============================================
// AI PERSONALIZATION FUNCTIONS
// ============================================

function generateAIPersonalizedEmail($data, $audience, $apiKey, $model)
{
    // Build context for AI
    $context = buildAIContext($data, $audience);

    // System prompt for consistent, professional responses (based on email-ai-prompt.md)
    $systemPrompt = "You are Therapair's confirmation email assistant. Your job is to help us send clear, thoughtful, and human responses when people submit the form on therapair.com.au.

Please generate a short, warm, and helpful email (2–3 paragraphs max) based on the audience type and form submission data provided.

Key Requirements:
- ✅ Thank them for their submission and acknowledge what they shared
- ✅ Briefly explain what Therapair is (a therapist-matching concierge experience built with real humans and real care)
- ✅ Let them know we've received their info and what the next steps might be (even if that's 'we'll be in touch soon')
- ✅ Mention we're in early development / learning phase if relevant
- ✅ Optional: if project launch timing is shared (e.g. 'beta launch coming soon'), include a friendly update sentence about that
- ✅ Optional: include a light, friendly reminder that no clinical or sensitive information should be sent via email (for privacy/HIPAA awareness)
- ✅ If the user is a Supporter or Organisation, include a subtle CTA like 'we'd love to stay in touch as we grow' (but no pressure)

Style Examples:
✅ Good:
- 'Hi Jess, thanks so much for taking the time to share this.'
- 'We're excited to learn what matters most to you.'
- 'You're one of the very first people to explore this with us.'

🚫 Avoid:
- Cold / corporate tone like 'Dear User' or 'Your request has been received'
- Over-promising outcomes ('We've matched you!') — we're not ready for that yet
- Sharing detailed next steps that aren't locked in

Delivery Format:
- Output plain text only (no HTML)
- Do not include subject line — that's handled separately
- Avoid emojis (unless explicitly instructed)

Always sign off as:
Warm regards,

Therapair Team";

    $userPrompt = "Write a warm, human confirmation email for this form submission:\n\n{$context}\n\nFocus on being conversational and acknowledging what they shared. Mention we're building a therapist-matching concierge experience with real humans and real care. We're in early development phase. Be encouraging about them being one of the first to explore this with us. Keep it to 2-3 paragraphs max, plain text only.";

    // Call OpenAI API
    try {
        $response = callOpenAI($systemPrompt, $userPrompt, $apiKey, $model);
        return $response;
    } catch (Exception $e) {
        // Fallback to static template if AI fails
        error_log("AI email generation failed: " . $e->getMessage());
        return null;
    }
}

function buildAIContext($data, $audience)
{
    $context = "Audience Type: " . ucfirst($audience) . "\n";

    switch ($audience) {
        case 'individual':
            $context .= "Email: {$data['email']}\n";
            if (!empty($data['therapy_interests']) && $data['therapy_interests'] !== 'None selected') {
                $context .= "What they think is important: {$data['therapy_interests']}\n";
            }
            if (!empty($data['additional_thoughts'])) {
                $context .= "Additional thoughts: {$data['additional_thoughts']}\n";
            }
            $context .= "\nThis person is expressing early interest in Therapair and sharing what they think is important in a therapist (this is research/feedback, not active matching).";
            break;

        case 'therapist':
            $context .= "Name: {$data['full_name']}\n";
            $context .= "Title: {$data['professional_title']}\n";
            $context .= "Organization: {$data['organization']}\n";
            $context .= "Email: {$data['email']}\n";
            if (!empty($data['specializations'])) {
                $context .= "Specializations: {$data['specializations']}\n";
            }
            $context .= "\nThis is a mental health professional interested in joining the Therapair network.";
            break;

        case 'organization':
            $context .= "Contact Name: {$data['contact_name']}\n";
            $context .= "Position: {$data['position']}\n";
            $context .= "Organization: {$data['organization_name']}\n";
            $context .= "Email: {$data['email']}\n";
            if (!empty($data['partnership_interest'])) {
                $context .= "Partnership Interest: {$data['partnership_interest']}\n";
            }
            $context .= "\nThis is an organization interested in partnering with Therapair.";
            break;

        case 'other':
            if (!empty($data['name'])) {
                $context .= "Name: {$data['name']}\n";
            }
            $context .= "Email: {$data['email']}\n";
            if (!empty($data['support_interest'])) {
                $context .= "Interest: {$data['support_interest']}\n";
            }
            $context .= "\nThis person wants to support or invest in Therapair.";
            break;
    }

    return $context;
}

function callOpenAI($systemPrompt, $userPrompt, $apiKey, $model)
{
    $url = 'https://api.openai.com/v1/chat/completions';

    $data = [
        'model' => $model,
        'messages' => [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userPrompt]
        ],
        'temperature' => 0.7,
        'max_tokens' => 400
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $apiKey,
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        throw new Exception("OpenAI API returned status code: {$httpCode}");
    }

    $responseData = json_decode($response, true);

    if (isset($responseData['choices'][0]['message']['content'])) {
        return $responseData['choices'][0]['message']['content'];
    }

    throw new Exception("Invalid OpenAI API response");
}

function formatUserEmailWithAI($aiContent, $data, $audience)
{
    // Design system colors
    $darkNavy = '#0F1E4B';
    $midBlue = '#3D578A';
    $darkGrey = '#4A5568';
    
    // Build content HTML
    $content = '
        <h1 style="margin: 0 0 24px 0; color: ' . $darkNavy . '; font-size: 28px; font-weight: 600; line-height: 1.3;">
            Thank You for Your Interest
        </h1>
        <div style="font-size: 16px; line-height: 1.8; color: ' . $darkGrey . '; white-space: pre-wrap;">' . nl2br(htmlspecialchars($aiContent)) . '</div>
    ';
    
    return getEmailTemplate($content, 'Thank you for your interest in Therapair');
}

// ============================================
// HELPER FUNCTIONS
// ============================================

/**
 * Send email via Resend API
 * Returns true on success, false on failure
 */
function sendEmailViaResend($to, $subject, $html, $fromEmail, $fromName, $replyTo = '')
{
    global $RESEND_API_KEY, $USE_RESEND;
    
    // If Resend is disabled or API key is missing, return false
    if (!$USE_RESEND || empty($RESEND_API_KEY)) {
        error_log("Resend email skipped: USE_RESEND=" . ($USE_RESEND ? 'true' : 'false') . ", API_KEY=" . (empty($RESEND_API_KEY) ? 'empty' : 'set'));
        return false;
    }
    
    // Use exact same format as research campaign (which is working)
    // Research campaign uses: 'Therapair Research <onboarding@resend.dev>'
    // Format: "Name <email@domain.com>"
    $fromAddress = $fromName . ' <' . $fromEmail . '>';
    
    // Resend API accepts 'to' as string or array - use string for simplicity
    $emailData = [
        'from' => $fromAddress,
        'to' => $to,  // Changed from array to string
        'subject' => $subject,
        'html' => $html,
        // Enable tracking for opens and clicks (best practice)
        'track_opens' => true,
        'track_clicks' => true
    ];
    
    if (!empty($replyTo)) {
        $emailData['reply_to'] = $replyTo;
    }
    
    // Send via Resend API
    $ch = curl_init('https://api.resend.com/emails');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($emailData),
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $RESEND_API_KEY,
            'Content-Type: application/json'
        ]
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    if ($httpCode === 200 || $httpCode === 201) {
        $responseData = json_decode($response, true);
        if (isset($responseData['id'])) {
            error_log("✅ Resend email sent successfully. ID: " . $responseData['id'] . " To: {$to}");
            return true;
        } else {
            error_log("⚠️ Resend returned {$httpCode} but no message ID. Response: " . substr($response, 0, 500));
        }
    }
    
    // Log detailed error information
    $responseData = json_decode($response, true);
    $errorMessage = '';
    if (is_array($responseData)) {
        if (isset($responseData['message'])) {
            $errorMessage = $responseData['message'];
        } elseif (isset($responseData['error'])) {
            $errorMessage = is_array($responseData['error']) ? json_encode($responseData['error']) : $responseData['error'];
        }
    }
    
    error_log("❌ Resend email failed. HTTP Code: {$httpCode}, cURL Error: {$curlError}, API Error: {$errorMessage}, Response: " . substr($response, 0, 500));
    return false;
}

function collectFormData($post, $audience)
{
    $data = [
        'audience' => sanitize($post['Audience_Type'] ?? ''),
        'email' => sanitize($post['Email'] ?? ''),
        'email_consent' => sanitize($post['Email_Consent'] ?? 'no'),
        'consent_timestamp' => date('Y-m-d H:i:s'),
        'timestamp' => date('Y-m-d H:i:s')
    ];

    switch ($audience) {
        case 'individual':
            $data['therapy_interests'] = sanitize($post['Therapy_Interests'] ?? 'None selected');
            $data['additional_thoughts'] = sanitize($post['Additional_Thoughts'] ?? '');
            break;

        case 'therapist':
            $data['full_name'] = sanitize($post['Full_Name'] ?? '');
            $data['professional_title'] = sanitize($post['Professional_Title'] ?? '');
            $data['organization'] = sanitize($post['Organization'] ?? '');
            $data['specializations'] = sanitize($post['Specializations'] ?? '');
            break;

        case 'organization':
            $data['contact_name'] = sanitize($post['Contact_Name'] ?? '');
            $data['position'] = sanitize($post['Position'] ?? '');
            $data['organization_name'] = sanitize($post['Organization_Name'] ?? '');
            $data['partnership_interest'] = sanitize($post['Partnership_Interest'] ?? '');
            break;

        case 'other':
            $data['name'] = sanitize($post['Name'] ?? '');
            $data['support_interest'] = sanitize($post['Support_Interest'] ?? '');
            break;
    }

    return $data;
}

function getAdminSubject($audience)
{
    $subjects = [
        'individual' => '🎯 New Interest: Individual Seeking Therapy',
        'therapist' => '👨‍⚕️ New Interest: Mental Health Professional',
        'organization' => '🏢 New Interest: Organization/Clinic',
        'other' => '💡 New Interest: Supporter/Investor'
    ];

    return $subjects[$audience] ?? '📋 New Therapair Interest Form';
}

function formatAdminEmail($data, $audience, $timestamp)
{
    // Design system colors
    $darkNavy = '#0F1E4B';
    $midBlue = '#3D578A';
    $darkGrey = '#4A5568';
    $warmBeige = '#FAF8F5';
    $white = '#FFFFFF';
    
    // Build content HTML
    $content = '
        <h1 style="margin: 0 0 24px 0; color: ' . $darkNavy . '; font-size: 24px; font-weight: 600; line-height: 1.3;">
            New Therapair Interest Form Submission
        </h1>
        
        <div style="margin-bottom: 20px;">
            <div style="margin-bottom: 12px;">
                <div style="font-weight: 600; color: ' . $darkNavy . '; font-size: 14px; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">
                    Submitted
                </div>
                <div style="padding: 12px; background-color: ' . $warmBeige . '; border-radius: 6px; color: ' . $darkGrey . '; font-size: 15px;">
                    ' . htmlspecialchars($timestamp) . '
                </div>
            </div>
            
            <div style="margin-bottom: 12px;">
                <div style="font-weight: 600; color: ' . $darkNavy . '; font-size: 14px; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">
                    Audience Type
                </div>
                <div style="padding: 12px; background-color: ' . $warmBeige . '; border-radius: 6px; color: ' . $darkGrey . '; font-size: 15px;">
                    ' . htmlspecialchars(ucfirst($audience)) . '
                </div>
            </div>
            
            <div style="margin-bottom: 12px;">
                <div style="font-weight: 600; color: ' . $darkNavy . '; font-size: 14px; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">
                    Email
                </div>
                <div style="padding: 12px; background-color: ' . $warmBeige . '; border-radius: 6px; font-size: 15px;">
                    <a href="mailto:' . htmlspecialchars($data['email']) . '" style="color: ' . $midBlue . '; text-decoration: none;">' . htmlspecialchars($data['email']) . '</a>
                </div>
            </div>
            
            <div style="margin-bottom: 12px;">
                <div style="font-weight: 600; color: ' . $darkNavy . '; font-size: 14px; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">
                    Email Consent
                </div>
                <div style="padding: 12px; background-color: ' . $warmBeige . '; border-radius: 6px; color: ' . $darkGrey . '; font-size: 15px;">
                    ' . (isset($data['email_consent']) && $data['email_consent'] === 'yes' ? 'Given (' . htmlspecialchars($data['consent_timestamp'] ?? 'N/A') . ')' : 'Not given') . '
                </div>
            </div>
    ';

    // Add audience-specific fields
    switch ($audience) {
        case 'individual':
            $content .= '
            <div style="margin-bottom: 12px;">
                <div style="font-weight: 600; color: ' . $darkNavy . '; font-size: 14px; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">
                    Therapy Interests
                </div>
                <div style="padding: 12px; background-color: ' . $warmBeige . '; border-radius: 6px; color: ' . $darkGrey . '; font-size: 15px; line-height: 1.6;">
                    ' . nl2br(htmlspecialchars($data['therapy_interests'])) . '
                </div>
            </div>
            ';
            if (!empty($data['additional_thoughts'])) {
                $content .= '
                <div style="margin-bottom: 12px;">
                    <div style="font-weight: 600; color: ' . $darkNavy . '; font-size: 14px; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">
                        Additional Thoughts
                    </div>
                    <div style="padding: 12px; background-color: ' . $warmBeige . '; border-radius: 6px; color: ' . $darkGrey . '; font-size: 15px; line-height: 1.6;">
                        ' . nl2br(htmlspecialchars($data['additional_thoughts'])) . '
                    </div>
                </div>
                ';
            }
            if (!empty($data['name'])) {
                $content .= '
                <div style="margin-bottom: 12px;">
                    <div style="font-weight: 600; color: ' . $darkNavy . '; font-size: 14px; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">
                        Name
                    </div>
                    <div style="padding: 12px; background-color: ' . $warmBeige . '; border-radius: 6px; color: ' . $darkGrey . '; font-size: 15px;">
                        ' . htmlspecialchars($data['name']) . '
                    </div>
                </div>
                ';
            }
            break;

        case 'therapist':
            $content .= '
            <div style="margin-bottom: 12px;">
                <div style="font-weight: 600; color: ' . $darkNavy . '; font-size: 14px; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">
                    Full Name
                </div>
                <div style="padding: 12px; background-color: ' . $warmBeige . '; border-radius: 6px; color: ' . $darkGrey . '; font-size: 15px;">
                    ' . htmlspecialchars($data['full_name'] ?? '') . '
                </div>
            </div>
            <div style="margin-bottom: 12px;">
                <div style="font-weight: 600; color: ' . $darkNavy . '; font-size: 14px; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">
                    Professional Title
                </div>
                <div style="padding: 12px; background-color: ' . $warmBeige . '; border-radius: 6px; color: ' . $darkGrey . '; font-size: 15px;">
                    ' . htmlspecialchars($data['professional_title'] ?? '') . '
                </div>
            </div>
            <div style="margin-bottom: 12px;">
                <div style="font-weight: 600; color: ' . $darkNavy . '; font-size: 14px; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">
                    Organization
                </div>
                <div style="padding: 12px; background-color: ' . $warmBeige . '; border-radius: 6px; color: ' . $darkGrey . '; font-size: 15px;">
                    ' . htmlspecialchars($data['organization'] ?? '') . '
                </div>
            </div>
            <div style="margin-bottom: 12px;">
                <div style="font-weight: 600; color: ' . $darkNavy . '; font-size: 14px; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">
                    Specializations
                </div>
                <div style="padding: 12px; background-color: ' . $warmBeige . '; border-radius: 6px; color: ' . $darkGrey . '; font-size: 15px; line-height: 1.6;">
                    ' . nl2br(htmlspecialchars($data['specializations'] ?? '')) . '
                </div>
            </div>
            ';
            break;

        case 'organization':
            $content .= '
            <div style="margin-bottom: 12px;">
                <div style="font-weight: 600; color: ' . $darkNavy . '; font-size: 14px; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">
                    Contact Name
                </div>
                <div style="padding: 12px; background-color: ' . $warmBeige . '; border-radius: 6px; color: ' . $darkGrey . '; font-size: 15px;">
                    ' . htmlspecialchars($data['contact_name'] ?? '') . '
                </div>
            </div>
            <div style="margin-bottom: 12px;">
                <div style="font-weight: 600; color: ' . $darkNavy . '; font-size: 14px; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">
                    Position
                </div>
                <div style="padding: 12px; background-color: ' . $warmBeige . '; border-radius: 6px; color: ' . $darkGrey . '; font-size: 15px;">
                    ' . htmlspecialchars($data['position'] ?? '') . '
                </div>
            </div>
            <div style="margin-bottom: 12px;">
                <div style="font-weight: 600; color: ' . $darkNavy . '; font-size: 14px; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">
                    Organization Name
                </div>
                <div style="padding: 12px; background-color: ' . $warmBeige . '; border-radius: 6px; color: ' . $darkGrey . '; font-size: 15px;">
                    ' . htmlspecialchars($data['organization_name'] ?? '') . '
                </div>
            </div>
            <div style="margin-bottom: 12px;">
                <div style="font-weight: 600; color: ' . $darkNavy . '; font-size: 14px; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">
                    Partnership Interest
                </div>
                <div style="padding: 12px; background-color: ' . $warmBeige . '; border-radius: 6px; color: ' . $darkGrey . '; font-size: 15px; line-height: 1.6;">
                    ' . nl2br(htmlspecialchars($data['partnership_interest'] ?? '')) . '
                </div>
            </div>
            ';
            break;

        case 'other':
            $content .= '
            <div style="margin-bottom: 12px;">
                <div style="font-weight: 600; color: ' . $darkNavy . '; font-size: 14px; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">
                    Name
                </div>
                <div style="padding: 12px; background-color: ' . $warmBeige . '; border-radius: 6px; color: ' . $darkGrey . '; font-size: 15px;">
                    ' . htmlspecialchars($data['name'] ?? '') . '
                </div>
            </div>
            <div style="margin-bottom: 12px;">
                <div style="font-weight: 600; color: ' . $darkNavy . '; font-size: 14px; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">
                    Support Interest
                </div>
                <div style="padding: 12px; background-color: ' . $warmBeige . '; border-radius: 6px; color: ' . $darkGrey . '; font-size: 15px; line-height: 1.6;">
                    ' . nl2br(htmlspecialchars($data['support_interest'] ?? '')) . '
                </div>
            </div>
            ';
            break;
    }
    
    // Add action required footer
    $content .= '
        <div style="margin-top: 32px; padding: 20px; background-color: ' . $darkNavy . '; border-radius: 8px;">
            <p style="margin: 0; color: ' . $white . '; font-size: 15px; font-weight: 600; line-height: 1.6;">
                Action Required: Respond to this inquiry within 1-2 business days.
            </p>
        </div>
        <p style="margin-top: 20px; color: ' . $darkGrey . '; font-size: 13px; line-height: 1.5;">
            This email was sent from the Therapair landing page contact form.
        </p>
    ';
    
    return getEmailTemplate($content, 'New form submission from Therapair landing page');
}

function formatUserEmail($data, $audience)
{
    // Design system colors
    $darkNavy = '#0F1E4B';
    $midBlue = '#3D578A';
    $darkGrey = '#4A5568';
    $warningBg = '#FFF5E6';
    $warningBorder = '#FFD700';
    $warningText = '#C05621';
    
    // Build content HTML based on audience type
    switch ($audience) {
        case 'therapist':
            $name = !empty($data['full_name']) ? htmlspecialchars($data['full_name']) : 'there';
            $hasTakenSurvey = false; // TODO: Check if therapist has taken survey
            
            $content = '
                <h1 style="margin: 0 0 24px 0; color: ' . $darkNavy . '; font-size: 24px; font-weight: bold; line-height: 1.4;">
                    Thank you for your interest in Therapair
                </h1>
                
                <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 0 0 16px 0;">
                    Hi ' . $name . ',
                </p>
                
                <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 0 0 16px 0;">
                    Thanks for your Expression of Interest to join Therapair as a mental health professional. We\'re excited about the possibility of working together.
                </p>
                
                <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 0 0 16px 0;">
                    <strong>What is Therapair?</strong>
                </p>
                <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 0 0 16px 0;">
                    Therapair is a privacy-first therapist-matching platform designed to help people find therapists who truly align with their needs, values, and identity. We focus on identity-aware matching—so that people from marginalised communities, LGBTQ+ individuals, neurodivergent people, and others can find therapists who understand and affirm their experiences.
                </p>
            ';
            
            if ($hasTakenSurvey) {
                $content .= '
                    <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 0 0 16px 0;">
                        We noticed you\'ve previously taken part in a Therapair research survey. We\'ll be in touch soon with a data consent form so you can choose how we use your previous information when setting up your profile.
                    </p>
                ';
            }
            
            $content .= '
                <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 0 0 16px 0;">
                    <strong>What happens next?</strong>
                </p>
                <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 0 0 16px 0;">
                    We\'re currently in a pre-MVP phase, building the platform with input from therapists like you. We\'ll email you when onboarding is ready in the coming months, and you\'ll be among the first to hear about pilot opportunities.
                </p>
                <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 0 0 24px 0;">
                    <strong>Explore Therapair</strong>
                </p>
                <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 0 0 32px 0;">
                    We\'d love your input as we build Therapair. Here are two ways to get involved:
                </p>
                
                <!-- First CTA: Sandbox Demo -->
                <div style="margin: 0 0 24px 0;">
                    <a href="https://therapair.com.au/sandbox/sandbox-demo.html" style="' . getEmailButtonStyle('primary') . '; display: inline-block;">
                        View Sandbox Demo
                    </a>
                </div>
                
                <!-- Second CTA: Research Survey - Generate token for therapist EOI submissions -->
                <div style="margin: 0 0 24px 0;">';
            
            // Generate token for therapist EOI submissions
            if ($audience === 'therapist') {
                $tokenPayload = [
                    'therapist_id' => 'EOI-' . strtoupper(substr(md5($email), 0, 8)),
                    'therapist_name' => $formData['full_name'] ?? 'Therapist',
                    'first_name' => explode(' ', $formData['full_name'] ?? 'Therapist')[0],
                    'practice_name' => $formData['organization'] ?? '',
                    'email' => $email,
                    'directory_page_id' => null,
                    'therapist_research_id' => 'eoi-' . time(),
                    'exp' => time() + (30 * 24 * 60 * 60) // 30 days
                ];
                $surveyToken = generateResearchToken($tokenPayload);
                $surveyUrl = $surveyToken ? 'https://therapair.com.au/research/survey/index.html?token=' . urlencode($surveyToken) : 'https://therapair.com.au/research/survey/index.html';
            } else {
                $surveyUrl = 'https://therapair.com.au/research/survey/index.html';
            }
            
            $content .= '
                    <a href="' . $surveyUrl . '" style="' . getEmailButtonStyle('secondary') . '; display: inline-block;">
                        Take Research Survey
                    </a>
                </div>
                
                <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 24px 0 0 0;">
                    In the meantime, if you have questions or ideas, you can simply reply to this email.
                </p>
                
                <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 24px 0 0 0;">
                    Thank you for being part of building a better way to connect people with mental health support.
                </p>
                
                <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 24px 0 0 0;">
                    Best regards,<br />
                    The Therapair Team
                </p>
            ';
            
            $html = getEmailTemplate($content, 'Thanks for your interest in Therapair');
            break;

        case 'individual':
            $name = !empty($data['name']) ? htmlspecialchars($data['name']) : 'there';
            
            $content = '
                <h1 style="margin: 0 0 24px 0; color: ' . $darkNavy . '; font-size: 24px; font-weight: bold; line-height: 1.4;">
                    Thank you for your interest in Therapair
                </h1>
                
                <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 0 0 16px 0;">
                    Hi ' . $name . ',
                </p>
                
                <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 0 0 16px 0;">
                    Thanks for your Expression of Interest. We\'re building Therapair to help people like you find therapists who truly understand your needs and values.
                </p>
                
                <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 0 0 16px 0;">
                    <strong>What is Therapair?</strong>
                </p>
                <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 0 0 16px 0;">
                    Therapair is a privacy-first therapist-matching platform designed to help people find therapists who align with their identity, values, and needs. We focus on identity-aware matching—so that people from marginalised communities, LGBTQ+ individuals, neurodivergent people, and others can find therapists who understand and affirm their experiences.
                </p>
                
                <div style="background-color: ' . $warningBg . '; border: 1px solid ' . $warningBorder . '; border-radius: 6px; padding: 20px; margin: 24px 0;">
                    <p style="font-size: 18px; font-weight: bold; color: ' . $warningText . '; margin: 0 0 12px 0;">
                        ⚠️ Important: We\'re not an emergency service
                    </p>
                    <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 0 0 12px 0;">
                        <strong>Therapair is not an emergency service.</strong> We cannot provide crisis support or immediate help.
                    </p>
                    <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 0 0 12px 0;">
                        If you\'re in crisis or considering self-harm, please contact:
                    </p>
                    <p style="font-size: 16px; line-height: 1.75; color: ' . $darkGrey . '; margin: 0;">
                        <strong>Emergency:</strong> <a href="tel:000" style="color: ' . $midBlue . '; text-decoration: underline;">000</a><br />
                        <strong>Lifeline:</strong> <a href="tel:131114" style="color: ' . $midBlue . '; text-decoration: underline;">13 11 14</a> (24/7 crisis support) - <a href="https://www.lifeline.org.au" style="color: ' . $midBlue . '; text-decoration: underline;">lifeline.org.au</a><br />
                        <strong>Beyond Blue:</strong> <a href="tel:1300224636" style="color: ' . $midBlue . '; text-decoration: underline;">1300 22 4636</a> - <a href="https://www.beyondblue.org.au" style="color: ' . $midBlue . '; text-decoration: underline;">beyondblue.org.au</a><br />
                        <strong>Suicide Call Back Service:</strong> <a href="tel:1300659467" style="color: ' . $midBlue . '; text-decoration: underline;">1300 659 467</a> - <a href="https://www.suicidecallbackservice.org.au" style="color: ' . $midBlue . '; text-decoration: underline;">suicidecallbackservice.org.au</a>
                    </p>
                </div>
                
                <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 0 0 16px 0;">
                    <strong>What happens next?</strong>
                </p>
                <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 0 0 16px 0;">
                    We\'re currently in a pre-MVP phase. We\'ll notify you when therapist matching opens in your area and invite you to share a few preferences (if you want to) so we can suggest aligned therapists.
                </p>
                <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 0 0 16px 0;">
                    <strong>Explore our sandbox demo</strong>
                </p>
                <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 0 0 16px 0;">
                    Want to see what we\'re building? Check out our sandbox demo prototype to experience the therapist-matching concept:
                </p>
                <div style="text-align: center; margin: 24px 0;">
                    <a href="https://therapair.com.au/sandbox/sandbox-demo.html" style="' . getEmailButtonStyle('primary') . '">
                        View Sandbox Demo
                    </a>
                </div>
                <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 0 0 16px 0;">
                    In the meantime, if you have questions, you can reply to this email. Please note we can\'t offer clinical advice or crisis support by email.
                </p>
                
                <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 24px 0 0 0;">
                    Thank you for being part of building a better way to connect people with mental health support.
                </p>
                
                <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 24px 0 0 0;">
                    Best regards,<br />
                    The Therapair Team
                </p>
            ';
            
            $html = getEmailTemplate($content, 'Thanks for your interest in Therapair');
            break;

        case 'organization':
            $name = !empty($data['contact_name']) ? htmlspecialchars($data['contact_name']) : 'there';
            $orgName = !empty($data['organization_name']) ? htmlspecialchars($data['organization_name']) : 'your organisation';
            $callBookingUrl = ''; // TODO: Add call booking URL if available
            
            $content = '
                <h1 style="margin: 0 0 24px 0; color: ' . $darkNavy . '; font-size: 24px; font-weight: bold; line-height: 1.4;">
                    Thank you for your interest in partnering with Therapair
                </h1>
                
                <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 0 0 16px 0;">
                    Hi ' . $name . ',
                </p>
                
                <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 0 0 16px 0;">
                    Thanks for your Expression of Interest from ' . $orgName . '. We\'re excited about the possibility of collaborating.
                </p>
                
                <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 0 0 16px 0;">
                    <strong>How we might work together</strong>
                </p>
                <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 0 0 8px 0; padding-left: 8px;">
                    • <strong>Pilot partnership</strong> – Early access to the platform for your clinicians
                </p>
                <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 0 0 8px 0; padding-left: 8px;">
                    • <strong>Clinic listing</strong> – Feature your organisation and clinicians on Therapair
                </p>
                <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 0 0 16px 0; padding-left: 8px;">
                    • <strong>Integration</strong> – Connect Therapair with your existing systems over time
                </p>
                
                <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 0 0 16px 0;">
                    <strong>What happens next?</strong>
                </p>
                <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 0 0 16px 0;">
                    We\'ll review your submission and be in touch within a few business days to explore the best way to work together.' . ($callBookingUrl ? ' If you\'d like to skip straight to a call, you can reply to this email or use this link to suggest a time: ' . htmlspecialchars($callBookingUrl) : ' If you\'d like to skip straight to a call, you can reply to this email.') . '
                </p>
                <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 0 0 16px 0;">
                    <strong>Explore our sandbox demo</strong>
                </p>
                <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 0 0 16px 0;">
                    Want to see what we\'re building? Check out our sandbox demo prototype to experience the therapist-matching concept:
                </p>
                <div style="text-align: center; margin: 24px 0;">
                    <a href="https://therapair.com.au/sandbox/sandbox-demo.html" style="' . getEmailButtonStyle('primary') . '">
                        View Sandbox Demo
                    </a>
                </div>
                
                <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 24px 0 0 0;">
                    Thank you for being part of building a better way to connect people with mental health support.
                </p>
                
                <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 24px 0 0 0;">
                    Best regards,<br />
                    The Therapair Team
                </p>
            ';
            
            $html = getEmailTemplate($content, 'Thanks for reaching out about Therapair');
            break;

        case 'other':
            $name = !empty($data['name']) ? htmlspecialchars($data['name']) : 'there';
            
            $content = '
                <h1 style="margin: 0 0 24px 0; color: ' . $darkNavy . '; font-size: 24px; font-weight: bold; line-height: 1.4;">
                    Thank you for your interest in supporting Therapair
                </h1>
                
                <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 0 0 16px 0;">
                    Hi ' . $name . ',
                </p>
                
                <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 0 0 16px 0;">
                    Thanks for your Expression of Interest. We\'re grateful for your support in building Therapair.
                </p>
                
                <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 0 0 16px 0;">
                    <strong>The vision</strong>
                </p>
                <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 0 0 16px 0;">
                    Therapair is building a privacy-first therapist-matching platform that helps people find therapists who truly align with their needs, values, and identity. We focus on identity-aware matching—so that people from marginalised communities, LGBTQ+ individuals, neurodivergent people, and others can find therapists who understand and affirm their experiences.
                </p>
                
                <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 0 0 16px 0;">
                    <strong>The problem</strong>
                </p>
                <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 0 0 16px 0;">
                    Finding the right therapist is hard. Traditional directories offer hundreds of options with little guidance, making it overwhelming and time-consuming. For people from marginalised communities, the challenge is even greater—finding therapists who are not just "LGBTQ+ friendly" but truly affirming, who understand neurodivergence, who respect cultural identity.
                </p>
                
                <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 0 0 16px 0;">
                    <strong>How you might be involved</strong>
                </p>
                <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 0 0 8px 0; padding-left: 8px;">
                    • <strong>Advisory</strong> – Share expertise and guidance as we build
                </p>
                <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 0 0 8px 0; padding-left: 8px;">
                    • <strong>Introductions</strong> – Connect us with therapists, clinics, or potential users
                </p>
                <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 0 0 16px 0; padding-left: 8px;">
                    • <strong>Future funding</strong> – We\'ll be raising pre-seed funding in the coming months
                </p>
                
                <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 0 0 16px 0;">
                    <strong>What happens next?</strong>
                </p>
                <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 0 0 16px 0;">
                    We\'ll review your submission and be in touch within a few business days to discuss how you might support Therapair. If you have specific questions or would like to schedule a call, you can reply to this email.
                </p>
                <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 0 0 16px 0;">
                    <strong>Explore our sandbox demo</strong>
                </p>
                <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 0 0 16px 0;">
                    Want to see what we\'re building? Check out our sandbox demo prototype to experience the therapist-matching concept:
                </p>
                <div style="text-align: center; margin: 24px 0;">
                    <a href="https://therapair.com.au/sandbox/sandbox-demo.html" style="' . getEmailButtonStyle('primary') . '">
                        View Sandbox Demo
                    </a>
                </div>
                
                <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 24px 0 0 0;">
                    Thank you for being part of building a better way to connect people with mental health support.
                </p>
                
                <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 24px 0 0 0;">
                    Best regards,<br />
                    The Therapair Team
                </p>
            ';
            
            $html = getEmailTemplate($content, 'Thanks for your interest in Therapair');
            break;

        default:
            $content = '
                <h1 style="margin: 0 0 24px 0; color: ' . $darkNavy . '; font-size: 24px; font-weight: bold; line-height: 1.4;">
                    Thank you for your interest in Therapair
                </h1>
                
                <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 0 0 16px 0;">
                    Hi there,
                </p>
                
                <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 0 0 16px 0;">
                    Thanks for your interest in Therapair. We\'ll be in touch soon.
                </p>
                
                <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 24px 0 0 0;">
                    Best regards,<br />
                    The Therapair Team
                </p>
            ';
            
            $html = getEmailTemplate($content, 'Thanks for your interest in Therapair');
    }
    
    return $html;
}

/**
 * Add tracking URLs to email links
 * Replaces direct links with tracking redirect URLs that include Notion page ID and UTM parameters
 */
function addTrackingToEmailLinks($emailHtml, $email, $audience) {
    // Base tracking URL
    $trackBase = 'https://therapair.com.au/track.php';
    
    // UTM parameters
    $utmSource = 'email';
    $utmMedium = 'eoi_confirmation';
    $utmContent = $audience;
    
    // Use email hash for privacy (don't expose email in URL)
    $emailHash = md5(strtolower(trim($email)));
    
    // Track sandbox demo links
    $sandboxUrl = 'https://therapair.com.au/sandbox/sandbox-demo.html';
    $trackingSandboxUrl = $trackBase . '?email=' . urlencode($emailHash) . 
        '&dest=sandbox' . 
        '&utm_source=' . urlencode($utmSource) . 
        '&utm_medium=' . urlencode($utmMedium) . 
        '&utm_campaign=sandbox_demo' . 
        '&utm_content=' . urlencode($utmContent);
    $emailHtml = str_replace($sandboxUrl, $trackingSandboxUrl, $emailHtml);
    $emailHtml = str_replace('href="' . $sandboxUrl, 'href="' . $trackingSandboxUrl, $emailHtml);
    
    // Track research survey links - preserve token if present
    // Use regex to match survey URL with optional token parameter
    $emailHtml = preg_replace_callback(
        '/href="(https:\/\/therapair\.com\.au\/research\/survey\/index\.html)(\?[^"]*)?"/',
        function($matches) use ($trackBase, $emailHash, $utmSource, $utmMedium, $utmContent) {
            $baseUrl = $matches[1];
            $existingParams = isset($matches[2]) ? $matches[2] : '';
            
            // Build tracking parameters
            $trackingParams = 'email=' . urlencode($emailHash) . 
                '&dest=survey' . 
                '&utm_source=' . urlencode($utmSource) . 
                '&utm_medium=' . urlencode($utmMedium) . 
                '&utm_campaign=research_survey' . 
                '&utm_content=' . urlencode($utmContent);
            
            // If there are existing params (like token), append tracking params
            if (!empty($existingParams)) {
                $finalUrl = $baseUrl . $existingParams . '&' . $trackingParams;
            } else {
                $finalUrl = $baseUrl . '?' . $trackingParams;
            }
            
            // Redirect through track.php while preserving token
            $trackingUrl = $trackBase . '?dest=survey&redirect=' . urlencode($finalUrl) . 
                '&email=' . urlencode($emailHash) . 
                '&utm_source=' . urlencode($utmSource) . 
                '&utm_medium=' . urlencode($utmMedium) . 
                '&utm_campaign=research_survey' . 
                '&utm_content=' . urlencode($utmContent);
            
            return 'href="' . $trackingUrl . '"';
        },
        $emailHtml
    );
    
    // Track email preferences links
    $preferencesUrl = 'https://therapair.com.au/email-preferences.html';
    $trackingPreferencesUrl = $trackBase . '?email=' . urlencode($emailHash) . 
        '&dest=preferences' . 
        '&utm_source=' . urlencode($utmSource) . 
        '&utm_medium=' . urlencode($utmMedium) . 
        '&utm_campaign=email_preferences' . 
        '&utm_content=' . urlencode($utmContent);
    $emailHtml = str_replace($preferencesUrl, $trackingPreferencesUrl, $emailHtml);
    $emailHtml = str_replace('href="' . $preferencesUrl, 'href="' . $trackingPreferencesUrl, $emailHtml);
    
    // Track website/home links
    $homeUrl = 'https://therapair.com.au';
    $trackingHomeUrl = $trackBase . '?email=' . urlencode($emailHash) . 
        '&dest=home' . 
        '&utm_source=' . urlencode($utmSource) . 
        '&utm_medium=' . urlencode($utmMedium) . 
        '&utm_campaign=website' . 
        '&utm_content=' . urlencode($utmContent);
    // Only replace if it's a standalone link (not part of another URL)
    $emailHtml = preg_replace(
        '/(href=")' . preg_quote($homeUrl, '/') . '(")/',
        '$1' . $trackingHomeUrl . '$2',
        $emailHtml
    );
    
    return $emailHtml;
}
?>