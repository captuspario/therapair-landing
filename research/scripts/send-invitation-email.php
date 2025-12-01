<?php
/**
 * Send research invitation email
 * Usage: php send-invitation-email.php
 */

// Load token data
$tokenDataPath = __DIR__ . '/../tino-token-data.json';
// Also try current directory if running from research folder
if (!file_exists($tokenDataPath)) {
    $tokenDataPath = __DIR__ . '/tino-token-data.json';
}
// Also try parent directory
if (!file_exists($tokenDataPath)) {
    $tokenDataPath = dirname(__DIR__) . '/tino-token-data.json';
}
if (!file_exists($tokenDataPath)) {
    die("Error: tino-token-data.json not found. Run create-therapist-invite.mjs first.\n");
}

$tokenData = json_decode(file_get_contents($tokenDataPath), true);
if (!$tokenData) {
    die("Error: Could not parse token data.\n");
}

// Email configuration (update these if needed)
$fromEmail = 'contact@therapair.com.au';
$fromName = 'Therapair Team';
$toEmail = $tokenData['email'];
$subject = 'Help us build a better therapist-matching system';

// Build HTML email
$htmlMessage = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #2563eb 0%, #06b6d4 100%); color: white; padding: 30px; border-radius: 8px 8px 0 0; text-align: center; }
        .content { background: #ffffff; padding: 30px; border: 1px solid #e5e7eb; border-top: none; }
        .button { display: inline-block; background: #2563eb; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin: 10px 5px; font-weight: 600; }
        .button:hover { background: #1d4ed8; }
        .link { color: #2563eb; text-decoration: none; word-break: break-all; }
        .footer { background: #f9fafb; padding: 20px; border: 1px solid #e5e7eb; border-top: none; border-radius: 0 0 8px 8px; font-size: 14px; color: #6b7280; text-align: center; }
        ul { padding-left: 20px; }
        li { margin: 8px 0; }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0;">Therapair Research Study</h1>
    </div>
    <div class="content">
        <p>Hi ' . htmlspecialchars($tokenData['first_name']) . ',</p>
        
        <p>Thank you for your interest in the Therapair research study.</p>
        
        <p>As practitioners, we know that the right fit between therapist and client can change everything. Therapair is building a new way to match people with therapists who truly fit them—by values, lived experience, and communication style.</p>
        
        <p><strong>We need your help to shape the future:</strong></p>
        <ul>
            <li>Try our sandbox demo to see the concept in action</li>
            <li>Share your insights in a short 5-7 minute research survey</li>
            <li>Help us understand which questions create the deepest personalisation</li>
        </ul>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="' . htmlspecialchars($tokenData['survey_url']) . '" class="button">Take the Research Survey</a>
        </div>
        
        <p><strong>Your Personalized Survey Link:</strong><br>
        <a href="' . htmlspecialchars($tokenData['survey_url']) . '" class="link">' . htmlspecialchars($tokenData['survey_url']) . '</a></p>
        
        <p><strong>Try the Sandbox Demo:</strong><br>
        <a href="' . htmlspecialchars($tokenData['sandbox_url']) . '" class="link">' . htmlspecialchars($tokenData['sandbox_url']) . '</a></p>
        
        <p><strong>Visit the Landing Page:</strong><br>
        <a href="' . htmlspecialchars($tokenData['landing_url']) . '" class="link">' . htmlspecialchars($tokenData['landing_url']) . '</a></p>
        
        <p style="background: #f0f9ff; padding: 15px; border-radius: 6px; border-left: 4px solid #2563eb;">
            <strong>Note:</strong> This token is valid for 30 days and is linked to your email address (' . htmlspecialchars($tokenData['email']) . ').
        </p>
        
        <p>Your responses will help us build a platform that truly serves both therapists and clients.</p>
        
        <p>Best regards,<br>
        <strong>Therapair Team</strong></p>
    </div>
    <div class="footer">
        <p>Therapair Research Study<br>
        <a href="mailto:contact@therapair.com.au" style="color: #2563eb;">contact@therapair.com.au</a></p>
    </div>
</body>
</html>
';

// Plain text version
$textMessage = "
Hi {$tokenData['first_name']},

Thank you for your interest in the Therapair research study.

As practitioners, we know that the right fit between therapist and client can change everything. Therapair is building a new way to match people with therapists who truly fit them—by values, lived experience, and communication style.

We need your help to shape the future:
• Try our sandbox demo to see the concept in action
• Share your insights in a short 5-7 minute research survey
• Help us understand which questions create the deepest personalisation

YOUR PERSONALIZED SURVEY LINK:
{$tokenData['survey_url']}

TRY THE SANDBOX DEMO:
{$tokenData['sandbox_url']}

VISIT THE LANDING PAGE:
{$tokenData['landing_url']}

This token is valid for 30 days and is linked to your email address ({$tokenData['email']}).

Your responses will help us build a platform that truly serves both therapists and clients.

Best regards,
Therapair Team

---
Therapair Research Study
contact@therapair.com.au
";

// Email headers
$headers = [];
$headers[] = "From: {$fromName} <{$fromEmail}>";
$headers[] = "Reply-To: {$fromEmail}";
$headers[] = "MIME-Version: 1.0";
$headers[] = "Content-Type: text/html; charset=UTF-8";
$headers[] = "X-Mailer: Therapair Research Invitation";
$headers[] = "X-Priority: 1";

$headersString = implode("\r\n", $headers);

// Send email
echo "📧 Sending invitation email...\n";
echo "   To: {$toEmail}\n";
echo "   Subject: {$subject}\n";

$sent = mail($toEmail, $subject, $htmlMessage, $headersString);

if ($sent) {
    echo "\n✅ Email sent successfully!\n";
    echo "\n📋 Email Details:\n";
    echo "   Recipient: {$toEmail}\n";
    echo "   Survey Link: {$tokenData['survey_url']}\n";
    echo "   Sandbox Link: {$tokenData['sandbox_url']}\n";
    echo "   Landing Page: {$tokenData['landing_url']}\n";
    echo "\n💡 Check your inbox (and spam folder) for the email.\n";
} else {
    echo "\n❌ Failed to send email.\n";
    echo "   This might be due to:\n";
    echo "   - Server mail configuration\n";
    echo "   - Email address validation\n";
    echo "   - SMTP settings\n";
    echo "\n   You can manually copy the email from: tino-invitation-email.txt\n";
    exit(1);
}

