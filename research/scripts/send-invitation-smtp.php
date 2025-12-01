<?php
/**
 * Send research invitation email using SMTP (more reliable than mail())
 * Requires PHPMailer library
 * 
 * Usage: php send-invitation-smtp.php
 */

// Load token data
$tokenDataPath = __DIR__ . '/../tino-token-data.json';
if (!file_exists($tokenDataPath)) {
    $tokenDataPath = __DIR__ . '/tino-token-data.json';
}
if (!file_exists($tokenDataPath)) {
    $tokenDataPath = dirname(__DIR__) . '/tino-token-data.json';
}
if (!file_exists($tokenDataPath)) {
    die("Error: tino-token-data.json not found.\n");
}

$tokenData = json_decode(file_get_contents($tokenDataPath), true);
if (!$tokenData) {
    die("Error: Could not parse token data.\n");
}

// Try to use PHPMailer if available, otherwise fall back to mail()
$useSMTP = false;
$smtpHost = 'smtp.hostinger.com';
$smtpPort = 587;
$smtpUser = 'contact@therapair.com.au';
$smtpPass = ''; // Would need to be configured

// Check if PHPMailer is available
if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    $useSMTP = true;
    require_once __DIR__ . '/../../vendor/autoload.php';
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
}

// Email configuration
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; background: #f5f5f5; }
        .email-container { background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #2563eb 0%, #06b6d4 100%); color: white; padding: 30px; text-align: center; }
        .content { padding: 30px; }
        .button { display: inline-block; background: #2563eb; color: white; padding: 14px 28px; text-decoration: none; border-radius: 6px; margin: 15px 0; font-weight: 600; }
        .button:hover { background: #1d4ed8; }
        .link { color: #2563eb; text-decoration: none; word-break: break-all; }
        .link:hover { text-decoration: underline; }
        .footer { background: #f9fafb; padding: 20px; border-top: 1px solid #e5e7eb; font-size: 14px; color: #6b7280; text-align: center; }
        .note-box { background: #f0f9ff; padding: 15px; border-radius: 6px; border-left: 4px solid #2563eb; margin: 20px 0; }
        ul { padding-left: 20px; }
        li { margin: 8px 0; }
        .url-box { background: #f9fafb; padding: 12px; border-radius: 4px; margin: 10px 0; font-family: monospace; font-size: 12px; word-break: break-all; }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1 style="margin: 0; font-size: 24px;">Therapair Research Study</h1>
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
                <a href="' . htmlspecialchars($tokenData['survey_url']) . '" class="button" style="color: white;">Take the Research Survey</a>
            </div>
            
            <p><strong>Your Personalized Survey Link:</strong></p>
            <div class="url-box">' . htmlspecialchars($tokenData['survey_url']) . '</div>
            
            <p><strong>Try the Sandbox Demo:</strong></p>
            <div class="url-box">' . htmlspecialchars($tokenData['sandbox_url']) . '</div>
            
            <p><strong>Visit the Landing Page:</strong></p>
            <div class="url-box">' . htmlspecialchars($tokenData['landing_url']) . '</div>
            
            <div class="note-box">
                <strong>Note:</strong> This token is valid for 30 days and is linked to your email address (' . htmlspecialchars($tokenData['email']) . ').
            </div>
            
            <p>Your responses will help us build a platform that truly serves both therapists and clients.</p>
            
            <p>Best regards,<br>
            <strong>Therapair Team</strong></p>
        </div>
        <div class="footer">
            <p>Therapair Research Study<br>
            <a href="mailto:contact@therapair.com.au" style="color: #2563eb;">contact@therapair.com.au</a></p>
        </div>
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

echo "📧 Sending invitation email via " . ($useSMTP ? "SMTP" : "PHP mail()") . "...\n";
echo "   To: {$toEmail}\n";
echo "   Subject: {$subject}\n\n";

if ($useSMTP && !empty($smtpPass)) {
    // Use PHPMailer with SMTP
    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $smtpHost;
        $mail->SMTPAuth = true;
        $mail->Username = $smtpUser;
        $mail->Password = $smtpPass;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $smtpPort;
        
        $mail->setFrom($fromEmail, $fromName);
        $mail->addAddress($toEmail);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $htmlMessage;
        $mail->AltBody = $textMessage;
        
        $mail->send();
        echo "✅ Email sent successfully via SMTP!\n";
    } catch (Exception $e) {
        echo "❌ SMTP failed: {$mail->ErrorInfo}\n";
        echo "   Falling back to PHP mail()...\n";
        $useSMTP = false;
    }
}

if (!$useSMTP) {
    // Fall back to PHP mail()
    $headers = [];
    $headers[] = "From: {$fromName} <{$fromEmail}>";
    $headers[] = "Reply-To: {$fromEmail}";
    $headers[] = "MIME-Version: 1.0";
    $headers[] = "Content-Type: text/html; charset=UTF-8";
    $headers[] = "X-Mailer: Therapair Research Invitation";
    $headers[] = "X-Priority: 1";
    $headers[] = "List-Unsubscribe: <mailto:contact@therapair.com.au?subject=unsubscribe>";
    
    $sent = mail($toEmail, $subject, $htmlMessage, implode("\r\n", $headers));
    
    if ($sent) {
        echo "✅ Email sent via PHP mail() (may have deliverability issues)\n";
    } else {
        echo "❌ Failed to send email.\n";
        exit(1);
    }
}

echo "\n📋 Email Details:\n";
echo "   Recipient: {$toEmail}\n";
echo "   Survey Link: {$tokenData['survey_url']}\n";
echo "   Sandbox Link: {$tokenData['sandbox_url']}\n";
echo "   Landing Page: {$tokenData['landing_url']}\n";
echo "\n⚠️  IMPORTANT: PHP mail() has poor deliverability.\n";
echo "   The email may:\n";
echo "   - Go to spam/junk folder\n";
echo "   - Be blocked by email providers\n";
echo "   - Not arrive at all\n";
echo "\n💡 Recommendations:\n";
echo "   1. Check spam/junk folder\n";
echo "   2. Set up SMTP with authentication (see EMAIL-IMPROVEMENTS.md)\n";
echo "   3. Consider professional email service (SendGrid, Mailgun)\n";
echo "   4. Set up SPF/DKIM/DMARC DNS records\n";

