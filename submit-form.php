<?php
/**
 * Therapair Landing Page - Form Submission Handler with AI-Powered Personalization
 * Sends emails to admin and AI-generated personalized confirmation to user
 */

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
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

// Use constants from config
$ADMIN_EMAIL = ADMIN_EMAIL;
$FROM_EMAIL = FROM_EMAIL;
$FROM_NAME = FROM_NAME;
$WEBSITE_URL = WEBSITE_URL;
$OPENAI_API_KEY = OPENAI_API_KEY;
$USE_AI_PERSONALIZATION = USE_AI_PERSONALIZATION;
$AI_MODEL = AI_MODEL;

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

// Collect all form data based on audience type
$formData = collectFormData($_POST, $audience);

// ============================================
// 1. SEND EMAIL TO ADMIN
// ============================================
$adminSubject = getAdminSubject($audience);
$adminMessage = formatAdminEmail($formData, $audience, $timestamp);

$adminHeaders = "From: {$FROM_NAME} <{$FROM_EMAIL}>\r\n";
$adminHeaders .= "Reply-To: {$email}\r\n";
$adminHeaders .= "MIME-Version: 1.0\r\n";
$adminHeaders .= "Content-Type: text/html; charset=UTF-8\r\n";

$adminSent = mail($ADMIN_EMAIL, $adminSubject, $adminMessage, $adminHeaders);

// ============================================
// 2. SEND CONFIRMATION EMAIL TO USER (AI-POWERED)
// ============================================
$userSubject = 'Thank you for your interest in Therapair';

// Generate AI-powered personalized message or fallback to template
if ($USE_AI_PERSONALIZATION && !empty($OPENAI_API_KEY) && $OPENAI_API_KEY !== 'YOUR_OPENAI_API_KEY_HERE') {
    $personalizedContent = generateAIPersonalizedEmail($formData, $audience, $OPENAI_API_KEY, $AI_MODEL);
    $userMessage = formatUserEmailWithAI($personalizedContent, $formData, $audience);
} else {
    $userMessage = formatUserEmail($formData, $audience);
}

$userHeaders = "From: {$FROM_NAME} <{$FROM_EMAIL}>\r\n";
$userHeaders .= "Reply-To: {$ADMIN_EMAIL}\r\n";
$userHeaders .= "MIME-Version: 1.0\r\n";
$userHeaders .= "Content-Type: text/html; charset=UTF-8\r\n";

$userSent = mail($email, $userSubject, $userMessage, $userHeaders);

// ============================================
// 3. REDIRECT TO THANK YOU PAGE
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

function generateAIPersonalizedEmail($data, $audience, $apiKey, $model) {
    // Build context for AI
    $context = buildAIContext($data, $audience);
    
    // System prompt for consistent, professional responses (based on email-ai-prompt.md)
    $systemPrompt = "You are writing on behalf of the Therapair team. Write brief, professional confirmation emails to people who expressed EARLY INTEREST in learning about Therapair.

IMPORTANT CONTEXT:
- This is NOT active matching - they're just expressing interest and sharing opinions
- They're helping us with early research to build the service
- We're gathering data about what people think is important
- No therapy matching happening yet

TONE & STYLE:
- Professional yet warm
- Concise and to the point (80-120 words max)
- Natural, human voice
- Not overly enthusiastic
- Don't promise matching or deep analysis

STRUCTURE:
- Paragraph 1: Thank them for sharing their thoughts/interest
- Paragraph 2: Acknowledge this helps us build the service + we'll follow up within 24 hours
- Keep it short and professional

WHAT TO AVOID:
- Saying we'll 'match them' (we're not ready yet)
- Over-explaining or going too deep into their interests
- Marketing language or overselling
- Making it obvious it's AI-generated
- Long, wordy responses

Always sign off as:
Warm regards,

Therapair Team";

    $userPrompt = "Write a brief, professional confirmation email for this early interest/research submission:\n\n{$context}\n\nRemember: This is just interest gathering and user research, not active matching. Thank them for sharing their thoughts/feedback. Be warm but concise. 80-120 words maximum.";
    
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

function buildAIContext($data, $audience) {
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

function callOpenAI($systemPrompt, $userPrompt, $apiKey, $model) {
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

function formatUserEmailWithAI($aiContent, $data, $audience) {
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
                    🌐 <a href="https://therapair.com.au">therapair.com.au</a>
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

function collectFormData($post, $audience) {
    $data = [
        'audience' => sanitize($post['Audience_Type'] ?? ''),
        'email' => sanitize($post['Email'] ?? ''),
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

function getAdminSubject($audience) {
    $subjects = [
        'individual' => '🎯 New Interest: Individual Seeking Therapy',
        'therapist' => '👨‍⚕️ New Interest: Mental Health Professional',
        'organization' => '🏢 New Interest: Organization/Clinic',
        'other' => '💡 New Interest: Supporter/Investor'
    ];
    
    return $subjects[$audience] ?? '📋 New Therapair Interest Form';
}

function formatAdminEmail($data, $audience, $timestamp) {
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

function formatUserEmail($data, $audience) {
    $name = '';
    $personalizedMessage = '';
    
    // Get name and create personalized message based on audience type
    switch ($audience) {
        case 'individual':
            $greeting = "Hi there,";
            $personalizedMessage = "We're thrilled you're taking this important step toward finding the right therapist. ";
            
            if (!empty($data['therapy_interests']) && $data['therapy_interests'] !== 'None selected') {
                $personalizedMessage .= "We noticed you're particularly interested in <strong>" . htmlspecialchars($data['therapy_interests']) . "</strong>. We'll prioritize connecting you with therapists who specialize in these areas and can provide the affirming, culturally competent care you deserve.";
            } else {
                $personalizedMessage .= "We'll help you find a therapist who truly understands your unique needs and can provide the affirming, culturally competent care you deserve.";
            }
            break;
            
        case 'therapist':
            $name = $data['full_name'] ?? '';
            $greeting = $name ? "Hi {$name}," : "Hi there,";
            $title = !empty($data['professional_title']) ? htmlspecialchars($data['professional_title']) : 'mental health professional';
            
            $personalizedMessage = "Thank you for your interest in joining the Therapair network as a <strong>{$title}</strong>. ";
            $personalizedMessage .= "We're building a community of inclusive, culturally competent practitioners who are passionate about serving diverse populations. ";
            
            if (!empty($data['specializations'])) {
                $personalizedMessage .= "Your expertise in <strong>" . htmlspecialchars(substr($data['specializations'], 0, 100)) . (strlen($data['specializations']) > 100 ? '...' : '') . "</strong> aligns perfectly with our mission.";
            } else {
                $personalizedMessage .= "We'd love to learn more about your practice and how we can work together to serve our community.";
            }
            break;
            
        case 'organization':
            $name = $data['contact_name'] ?? '';
            $greeting = $name ? "Hi {$name}," : "Hi there,";
            $orgName = !empty($data['organization_name']) ? htmlspecialchars($data['organization_name']) : 'your organization';
            
            $personalizedMessage = "Thank you for reaching out on behalf of <strong>{$orgName}</strong>. ";
            $personalizedMessage .= "We're excited about the possibility of partnering with forward-thinking organizations that share our commitment to inclusive mental health care. ";
            
            if (!empty($data['partnership_interest'])) {
                $personalizedMessage .= "We're particularly interested in exploring how we can collaborate to achieve your goals.";
            } else {
                $personalizedMessage .= "We'd love to discuss how Therapair can support your organization's mental health initiatives.";
            }
            break;
            
        case 'other':
            $name = $data['name'] ?? '';
            $greeting = $name ? "Hi {$name}," : "Hi there,";
            
            $personalizedMessage = "Thank you for your interest in supporting Therapair! ";
            $personalizedMessage .= "Advocates and supporters like you are essential to our mission of creating truly inclusive mental health care. ";
            
            if (!empty($data['support_interest'])) {
                $personalizedMessage .= "We're inspired by your commitment to this important cause and would love to explore how we can work together.";
            } else {
                $personalizedMessage .= "We'd love to share more about our vision and explore how you can be part of this journey.";
            }
            break;
            
        default:
            $greeting = "Hi there,";
            $personalizedMessage = "Thank you for your interest in Therapair! We're excited to connect with you.";
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
                
                <p style="font-size: 15px; line-height: 1.8;">' . $personalizedMessage . '</p>
                
                <div class="box">
                    <h3 style="margin-top: 0; color: #4F064F; font-size: 18px;">📋 What happens next?</h3>
                    <p style="margin: 8px 0; font-size: 15px;">✓ We\'ll review your information carefully</p>
                    <p style="margin: 8px 0; font-size: 15px;">✓ Someone from our team will contact you within <strong>1-2 business days</strong></p>
                    <p style="margin: 8px 0; font-size: 15px;">✓ We\'ll discuss the best next steps for your specific needs</p>
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
                        🌐 <a href="https://therapair.com.au" style="color: #2563eb; text-decoration: none;">therapair.com.au</a>
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

