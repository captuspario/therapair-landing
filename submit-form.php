<?php
/**
 * Therapair Landing Page - Form Submission Handler
 * Sends emails to admin and user confirmation
 */

// Prevent direct access
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /');
    exit;
}

// Configuration
$ADMIN_EMAIL = 'contact@therapair.com.au';
$FROM_EMAIL = 'noreply@therapair.com.au';
$FROM_NAME = 'Therapair';
$WEBSITE_URL = 'https://therapair.com.au';

// Get form data
$audience = isset($_POST['Audience_Type']) ? sanitize($_POST['Audience_Type']) : '';
$email = isset($_POST['Email']) ? sanitize($_POST['Email']) : '';
$therapyInterests = isset($_POST['Therapy_Interests']) ? sanitize($_POST['Therapy_Interests']) : '';
$timestamp = date('Y-m-d H:i:s');

// Validate required fields
if (empty($audience) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: /?error=invalid');
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
// 2. SEND CONFIRMATION EMAIL TO USER
// ============================================
$userSubject = 'Thank you for your interest in Therapair';
$userMessage = formatUserEmail($formData, $audience);

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
// HELPER FUNCTIONS
// ============================================

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

function collectFormData($post, $audience) {
    $data = [
        'audience' => sanitize($post['Audience_Type'] ?? ''),
        'email' => sanitize($post['Email'] ?? ''),
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    switch ($audience) {
        case 'individual':
            $data['therapy_interests'] = sanitize($post['Therapy_Interests'] ?? 'None selected');
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
    
    // Get name if available
    if ($audience === 'therapist' && !empty($data['full_name'])) {
        $name = $data['full_name'];
    } elseif ($audience === 'organization' && !empty($data['contact_name'])) {
        $name = $data['contact_name'];
    } elseif ($audience === 'other' && !empty($data['name'])) {
        $name = $data['name'];
    }
    
    $greeting = $name ? "Hi {$name}," : "Hi there,";
    
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #2563eb, #06b6d4); color: white; padding: 30px 20px; border-radius: 8px 8px 0 0; text-align: center; }
            .content { background: #ffffff; padding: 30px 20px; border-radius: 0 0 8px 8px; border: 1px solid #e5e7eb; }
            .box { background: #f0e7f3; padding: 20px; border-radius: 8px; margin: 20px 0; }
            .footer { margin-top: 30px; padding-top: 20px; border-top: 2px solid #e5e7eb; color: #6b7280; font-size: 12px; text-align: center; }
            .button { display: inline-block; padding: 12px 24px; background: linear-gradient(135deg, #2563eb, #06b6d4); color: white; text-decoration: none; border-radius: 8px; margin: 10px 0; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1 style="margin: 0; font-size: 28px;">Thank You! 🎉</h1>
            </div>
            <div class="content">
                <p>' . htmlspecialchars($greeting) . '</p>
                
                <p>Thank you for your interest in Therapair! We\'ve received your submission and are excited to connect with you.</p>
                
                <div class="box">
                    <h3 style="margin-top: 0; color: #4F064F;">What happens next?</h3>
                    <p style="margin: 5px 0;">✓ We\'ll review your information</p>
                    <p style="margin: 5px 0;">✓ Contact you within <strong>1-2 business days</strong></p>
                    <p style="margin: 5px 0;">✓ Help you get matched with the right support</p>
                </div>
                
                <p>Your journey to finding the right therapist starts here.</p>
                
                <p>If you have any questions in the meantime, please reply to this email or contact us at <a href="mailto:contact@therapair.com.au" style="color: #2563eb;">contact@therapair.com.au</a></p>
                
                <p style="margin-top: 30px;">Best regards,<br>
                <strong>The Therapair Team</strong></p>
                
                <div class="footer">
                    <p><strong>Therapair</strong> - AI-powered therapy matching for inclusive mental health care</p>
                    <p style="margin-top: 10px;">
                        📧 <a href="mailto:contact@therapair.com.au" style="color: #2563eb;">contact@therapair.com.au</a><br>
                        🌐 <a href="https://therapair.com.au" style="color: #2563eb;">therapair.com.au</a>
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

