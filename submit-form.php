<?php
/**
 * Therapair Landing Page - Form Submission Handler with AI-Powered Personalisation
 * Sends emails to admin and AI-generated personalised confirmation to user
 */

// Debug logging removed - emails working correctly

// Load configuration
require_once __DIR__ . '/config.php';

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
// 1. SEND EMAIL TO ADMIN
// ============================================
$adminSubject = getAdminSubject($audience);
$adminMessage = formatAdminEmail($formData, $audience, $timestamp);

// Use same sender format as research campaign (which is working)
$senderEmail = 'onboarding@resend.dev'; // Use Resend's verified domain
$senderName = 'Therapair Team'; // Match research campaign naming style

$adminHeaders = "From: {$FROM_NAME} <{$FROM_EMAIL}>\r\n";
$adminHeaders .= "Reply-To: {$email}\r\n";
$adminHeaders .= "MIME-Version: 1.0\r\n";
$adminHeaders .= "Content-Type: text/html; charset=UTF-8\r\n";
$adminHeaders .= "X-Mailer: Therapair Contact Form\r\n";
$adminHeaders .= "X-Priority: 3\r\n";

// Send admin email via Resend or fallback to mail()
$adminSent = sendEmailViaResend(
    $ADMIN_EMAIL,
    $adminSubject,
    $adminMessage,
    $senderEmail,
    $FROM_NAME,
    $email // Reply-To
);

// If Resend failed, try fallback
if (!$adminSent) {
    error_log("Resend failed for admin email, trying PHP mail() fallback");
    $adminSent = @mail($ADMIN_EMAIL, $adminSubject, $adminMessage, $adminHeaders);
    if ($adminSent) {
        error_log("PHP mail() fallback succeeded for admin email");
    } else {
        error_log("PHP mail() fallback also failed for admin email");
    }
}

// ============================================
// 2. SEND CONFIRMATION EMAIL TO USER (AI-POWERED)
// ============================================
$userSubject = 'Thank you for your interest in Therapair';

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

// Send user email via Resend or fallback to mail()
$userSent = sendEmailViaResend(
    $email,
    $userSubject,
    $userMessage,
    $senderEmail,
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
    // Determine target database based on audience type
    $targetDb = null;

    if ($audience === 'individual') {
        // Individuals answering research questions go to Survey DB
        $targetDb = defined('NOTION_DB_SURVEY') ? NOTION_DB_SURVEY : (defined('NOTION_DB_EOI') ? NOTION_DB_EOI : null);
    } else {
        // Therapists, Organizations, etc. go to EOI DB
        $targetDb = defined('NOTION_DB_EOI') ? NOTION_DB_EOI : null;
    }

    $notionResult = syncToNotion($formData, $audience, $targetDb);
    if (!$notionResult['success']) {
        error_log("Notion sync failed: " . print_r($notionResult, true));
        // Continue anyway - don't block user experience
    }
}

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
    // Extract name for greeting if available
    $name = '';
    if ($audience === 'therapist' && !empty($data['full_name'])) {
        $name = $data['full_name'];
    } elseif ($audience === 'organization' && !empty($data['contact_name'])) {
        $name = $data['contact_name'];
    } elseif ($audience === 'other' && !empty($data['name'])) {
        $name = $data['name'];
    }

    // Format AI-generated content in beautiful HTML template
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.8; color: #333; margin: 0; padding: 0; }
            .container { max-width: 600px; margin: 0 auto; padding: 0; background: #f9fafb; }
            .header { background: linear-gradient(135deg, #2563eb, #06b6d4); color: white; padding: 40px 20px; text-align: center; }
            .content { background: #ffffff; padding: 40px 30px; border-left: 1px solid #e5e7eb; border-right: 1px solid #e5e7eb; }
            .message { font-size: 15px; line-height: 1.8; color: #374151; white-space: pre-wrap; }
            .footer { background: #ffffff; padding: 30px 20px; border-top: 2px solid #e5e7eb; border-left: 1px solid #e5e7eb; border-right: 1px solid #e5e7eb; border-bottom: 1px solid #e5e7eb; color: #6b7280; font-size: 13px; text-align: center; }
            a { color: #2563eb; text-decoration: none; }
            a:hover { text-decoration: underline; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1 style="margin: 0; font-size: 26px; font-weight: 600;">Thank You for Your Interest! 🎉</h1>
            </div>
            <div class="content">
                <div class="message">' . nl2br(htmlspecialchars($aiContent)) . '</div>
            </div>
            <div class="footer">
                <p style="margin: 5px 0;"><strong>Therapair</strong></p>
                <p style="margin: 5px 0;">AI-powered therapy matching for inclusive mental health care</p>
                <p style="margin-top: 15px;">
                    📧 <a href="mailto:contact@therapair.com.au">contact@therapair.com.au</a><br>
                    🌐 <a href="https://therapair.com.au">therapair.com.au</a><br>
                    ⚙️ <a href="https://therapair.com.au/email-preferences.html">Manage Email Preferences</a>
                </p>
                <p style="margin-top: 15px; font-size: 11px; color: #9ca3af;">
                    © 2025 Therapair. Made with 💕 for inclusive mental health.
                </p>
            </div>
        </div>
    </body>
    </html>
    ';

    return $html;
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
        'html' => $html
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
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #2563eb, #06b6d4); color: white; padding: 20px; border-radius: 8px 8px 0 0; }
            .content { background: #f9fafb; padding: 20px; border-radius: 0 0 8px 8px; }
            .field { margin-bottom: 15px; }
            .label { font-weight: bold; color: #4F064F; }
            .value { margin-top: 5px; padding: 10px; background: white; border-radius: 4px; }
            .footer { margin-top: 20px; padding-top: 20px; border-top: 2px solid #e5e7eb; color: #6b7280; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h2 style="margin: 0;">🎯 New Therapair Interest Form</h2>
            </div>
            <div class="content">
                <div class="field">
                    <div class="label">📅 Submitted:</div>
                    <div class="value">' . htmlspecialchars($timestamp) . '</div>
                </div>
                
                <div class="field">
                    <div class="label">👤 Audience Type:</div>
                    <div class="value">' . htmlspecialchars(ucfirst($audience)) . '</div>
                </div>
                
                <div class="field">
                    <div class="label">📧 Email:</div>
                    <div class="value"><a href="mailto:' . htmlspecialchars($data['email']) . '">' . htmlspecialchars($data['email']) . '</a></div>
                </div>
                <div class="field">
                    <div class="label">✅ Email Consent:</div>
                    <div class="value">' . (isset($data['email_consent']) && $data['email_consent'] === 'yes' ? '✅ Given (' . htmlspecialchars($data['consent_timestamp'] ?? 'N/A') . ')' : '❌ Not given') . '</div>
                </div>
    ';

    // Add audience-specific fields
    switch ($audience) {
        case 'individual':
            $html .= '
                <div class="field">
                    <div class="label">💭 Therapy Interests:</div>
                    <div class="value">' . nl2br(htmlspecialchars($data['therapy_interests'])) . '</div>
                </div>
            ';
            if (!empty($data['additional_thoughts'])) {
                $html .= '
                    <div class="field">
                        <div class="label">💬 What\'s Important to Them:</div>
                        <div class="value">' . nl2br(htmlspecialchars($data['additional_thoughts'])) . '</div>
                    </div>
                ';
            }
            break;

        case 'therapist':
            $html .= '
                <div class="field">
                    <div class="label">👤 Full Name:</div>
                    <div class="value">' . htmlspecialchars($data['full_name']) . '</div>
                </div>
                <div class="field">
                    <div class="label">💼 Professional Title:</div>
                    <div class="value">' . htmlspecialchars($data['professional_title']) . '</div>
                </div>
                <div class="field">
                    <div class="label">🏢 Organization:</div>
                    <div class="value">' . htmlspecialchars($data['organization']) . '</div>
                </div>
                <div class="field">
                    <div class="label">🎯 Specializations:</div>
                    <div class="value">' . nl2br(htmlspecialchars($data['specializations'])) . '</div>
                </div>
            ';
            break;

        case 'organization':
            $html .= '
                <div class="field">
                    <div class="label">👤 Contact Name:</div>
                    <div class="value">' . htmlspecialchars($data['contact_name']) . '</div>
                </div>
                <div class="field">
                    <div class="label">💼 Position:</div>
                    <div class="value">' . htmlspecialchars($data['position']) . '</div>
                </div>
                <div class="field">
                    <div class="label">🏢 Organization Name:</div>
                    <div class="value">' . htmlspecialchars($data['organization_name']) . '</div>
                </div>
                <div class="field">
                    <div class="label">🤝 Partnership Interest:</div>
                    <div class="value">' . nl2br(htmlspecialchars($data['partnership_interest'])) . '</div>
                </div>
            ';
            break;

        case 'other':
            $html .= '
                <div class="field">
                    <div class="label">👤 Name:</div>
                    <div class="value">' . htmlspecialchars($data['name']) . '</div>
                </div>
                <div class="field">
                    <div class="label">💡 Support Interest:</div>
                    <div class="value">' . nl2br(htmlspecialchars($data['support_interest'])) . '</div>
                </div>
            ';
            break;
    }

    $html .= '
                <div class="footer">
                    <p><strong>⚡ Action Required:</strong> Respond to this inquiry within 1-2 business days.</p>
                    <p style="margin-top: 10px;">This email was sent from the Therapair landing page contact form.</p>
                </div>
            </div>
        </div>
    </body>
    </html>
    ';

    return $html;
}

function formatUserEmail($data, $audience)
{
    $name = '';
    $personalisedMessage = '';

    // Get name and create personalised message based on audience type
    switch ($audience) {
        case 'individual':
            $greeting = "Hi there,";
            $personalisedMessage = "Thanks so much for taking the time to share what's important to you. ";

            if (!empty($data['therapy_interests']) && $data['therapy_interests'] !== 'None selected') {
                $personalisedMessage .= "We're excited to learn what matters most to you, especially your interest in <strong>" . htmlspecialchars($data['therapy_interests']) . "</strong>. This helps us build a therapist-matching concierge experience with real humans and real care.";
            } else {
                $personalisedMessage .= "We're excited to learn what matters most to you. This helps us build a therapist-matching concierge experience with real humans and real care.";
            }
            break;

        case 'therapist':
            $name = $data['full_name'] ?? '';
            $greeting = $name ? "Hi {$name}," : "Hi there,";
            $title = !empty($data['professional_title']) ? htmlspecialchars($data['professional_title']) : 'mental health professional';

            $personalisedMessage = "Thanks so much for taking the time to share about your practice. ";
            $personalisedMessage .= "We'd love to learn about your work as a <strong>{$title}</strong> and how we can build a therapist-matching concierge experience together. ";

            if (!empty($data['specializations'])) {
                $personalisedMessage .= "Your expertise in <strong>" . htmlspecialchars(substr($data['specializations'], 0, 100)) . (strlen($data['specializations']) > 100 ? '...' : '') . "</strong> is exactly what we're looking for.";
            } else {
                $personalisedMessage .= "We're excited to learn more about your practice and explore how we can work together.";
            }
            break;

        case 'organization':
            $name = $data['contact_name'] ?? '';
            $greeting = $name ? "Hi {$name}," : "Hi there,";
            $orgName = !empty($data['organization_name']) ? htmlspecialchars($data['organization_name']) : 'your organisation';

            $personalisedMessage = "Thanks so much for reaching out on behalf of <strong>{$orgName}</strong>. ";
            $personalisedMessage .= "We'd love to stay in touch as we grow and build our therapist-matching concierge experience. ";

            if (!empty($data['partnership_interest'])) {
                $personalisedMessage .= "We're excited to explore how we can collaborate and support each other.";
            } else {
                $personalisedMessage .= "We'd love to learn more about your organisation and how we can work together.";
            }
            break;

        case 'other':
            $name = $data['name'] ?? '';
            $greeting = $name ? "Hi {$name}," : "Hi there,";

            $personalisedMessage = "Thanks so much for your interest in supporting Therapair! ";
            $personalisedMessage .= "We'd love to stay in touch as we grow and build our therapist-matching concierge experience with real humans and real care. ";

            if (!empty($data['support_interest'])) {
                $personalisedMessage .= "We're excited to explore how we can work together and support each other.";
            } else {
                $personalisedMessage .= "We'd love to share more about our vision and explore how you can be part of this journey.";
            }
            break;

        default:
            $greeting = "Hi there,";
            $personalisedMessage = "Thanks so much for your interest in Therapair! We're excited to connect with you and build something meaningful together.";
    }

    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; background: #f9fafb; }
            .header { background: linear-gradient(135deg, #2563eb, #06b6d4); color: white; padding: 30px 20px; border-radius: 8px 8px 0 0; text-align: center; }
            .content { background: #ffffff; padding: 30px 20px; border: 1px solid #e5e7eb; }
            .box { background: #f0e7f3; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #9B74B7; }
            .footer { margin-top: 30px; padding-top: 20px; border-top: 2px solid #e5e7eb; color: #6b7280; font-size: 12px; text-align: center; }
            .signature { margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1 style="margin: 0; font-size: 28px;">Thank You for Your Interest! 🎉</h1>
            </div>
            <div class="content">
                <p style="font-size: 16px;"><strong>' . htmlspecialchars($greeting) . '</strong></p>
                
                <p style="font-size: 15px; line-height: 1.8;">' . $personalisedMessage . '</p>
                
                <div class="box">
                    <h3 style="margin-top: 0; color: #4F064F; font-size: 18px;">📋 What happens next?</h3>
                    <p style="margin: 8px 0; font-size: 15px;">✓ You\'re one of the very first people to explore this with us</p>
                    <p style="margin: 8px 0; font-size: 15px;">✓ We\'ll be in touch soon as we build our therapist-matching concierge experience</p>
                    <p style="margin: 8px 0; font-size: 15px;">✓ We\'ll keep you updated on our progress and let you know when we\'re ready to launch</p>
                </div>
                
                <p style="font-size: 15px;">If you have any urgent questions, please don\'t hesitate to reply to this email.</p>
                
                <div class="signature">
                    <p style="margin: 5px 0; font-size: 15px;">Warm regards,</p>
                    <p style="margin: 5px 0;"><strong style="font-size: 16px;">Tino</strong></p>
                    <p style="margin: 5px 0; color: #6b7280; font-size: 14px;">Therapair Team</p>
                </div>
                
                <div class="footer">
                    <p style="margin: 5px 0;"><strong>Therapair</strong></p>
                    <p style="margin: 5px 0;">AI-powered therapy matching for inclusive mental health care</p>
                    <p style="margin-top: 15px;">
                        📧 <a href="mailto:contact@therapair.com.au" style="color: #2563eb; text-decoration: none;">contact@therapair.com.au</a><br>
                        🌐 <a href="https://therapair.com.au" style="color: #2563eb; text-decoration: none;">therapair.com.au</a><br>
                        ⚙️ <a href="https://therapair.com.au/email-preferences.html" style="color: #2563eb; text-decoration: none;">Manage Email Preferences</a>
                    </p>
                    <p style="margin-top: 15px; font-size: 11px; color: #9ca3af;">
                        © 2025 Therapair. Made with 💕 for inclusive mental health.
                    </p>
                </div>
            </div>
        </div>
    </body>
    </html>
    ';

    return $html;
}
?>