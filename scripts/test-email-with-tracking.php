<?php
/**
 * Test Email with Proper Template and Tracking
 * Standalone test that uses the actual email functions
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../email-template-base.php';

$testEmail = 'tinokuhn@gmail.com';
$testSubject = 'Test EOI Confirmation Email - Therapist';

echo "📧 Testing Email with Proper Template and Tracking\n";
echo "==================================================\n\n";

// Copy the formatUserEmail function (therapist case)
function formatUserEmailTherapist($data) {
    require_once __DIR__ . '/../email-template-base.php';
    
    $darkNavy = '#0F1E4B';
    $midBlue = '#3D578A';
    $darkGrey = '#4A5568';
    
    $name = !empty($data['full_name']) ? htmlspecialchars($data['full_name']) : 'there';
    
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
        
        <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 0 0 16px 0;">
            <strong>What happens next?</strong>
        </p>
        <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 0 0 16px 0;">
            We\'re currently in a pre-MVP phase, building the platform with input from therapists like you. We\'ll email you when onboarding is ready in the coming months, and you\'ll be among the first to hear about pilot opportunities.
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
    
    return getEmailTemplate($content, 'Thanks for your interest in Therapair');
}

// Copy the addTrackingToEmailLinks function
function addTrackingToEmailLinks($emailHtml, $email, $audience) {
    $trackBase = 'https://therapair.com.au/track.php';
    $utmSource = 'email';
    $utmMedium = 'eoi_confirmation';
    $utmContent = $audience;
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
    
    return $emailHtml;
}

// Copy the sendEmailViaResend function
function sendEmailViaResend($to, $subject, $html, $fromEmail, $fromName, $replyTo = '') {
    $RESEND_API_KEY = defined('RESEND_API_KEY') ? RESEND_API_KEY : '';
    $USE_RESEND = defined('USE_RESEND') ? USE_RESEND : true;
    
    if (!$USE_RESEND || empty($RESEND_API_KEY)) {
        echo "❌ Resend disabled or API key missing\n";
        return false;
    }
    
    $fromAddress = $fromName . ' <' . $fromEmail . '>';
    $emailData = [
        'from' => $fromAddress,
        'to' => $to,
        'subject' => $subject,
        'html' => $html,
        'track_opens' => true,
        'track_clicks' => true
    ];
    
    if (!empty($replyTo)) {
        $emailData['reply_to'] = $replyTo;
    }
    
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
            echo "✅ Resend email sent successfully!\n";
            echo "   Message ID: " . $responseData['id'] . "\n";
            return true;
        }
    }
    
    $responseData = json_decode($response, true);
    $errorMessage = $responseData['message'] ?? 'Unknown error';
    echo "❌ Resend email failed!\n";
    echo "   HTTP Code: {$httpCode}\n";
    echo "   Error: {$errorMessage}\n";
    return false;
}

// Generate email
$formData = [
    'full_name' => 'Test User'
];

$userMessage = formatUserEmailTherapist($formData);

// Check before tracking
echo "Before adding tracking:\n";
if (preg_match('/href="([^"]*sandbox[^"]*)"/', $userMessage, $matches)) {
    echo "   Sandbox URL: " . $matches[1] . "\n";
}
echo "\n";

// Add tracking
$userMessage = addTrackingToEmailLinks($userMessage, $testEmail, 'therapist');

// Check after tracking
echo "After adding tracking:\n";
if (preg_match('/href="([^"]*sandbox[^"]*)"/', $userMessage, $matches)) {
    $sandboxUrl = $matches[1];
    echo "   Sandbox URL: " . $sandboxUrl . "\n";
    if (strpos($sandboxUrl, 'track.php') !== false) {
        echo "   ✅ Tracking added correctly!\n";
    } else {
        echo "   ❌ Tracking NOT added!\n";
    }
}
echo "\n";

// Send email
$FROM_EMAIL = defined('FROM_EMAIL') ? FROM_EMAIL : 'contact@therapair.com.au';
$FROM_NAME = defined('FROM_NAME') ? FROM_NAME : 'Therapair Team';
$ADMIN_EMAIL = defined('ADMIN_EMAIL') ? ADMIN_EMAIL : 'contact@therapair.com.au';

echo "Sending email...\n";
echo "   To: $testEmail\n";
echo "   Subject: $testSubject\n\n";

$result = sendEmailViaResend(
    $testEmail,
    $testSubject,
    $userMessage,
    $FROM_EMAIL,
    $FROM_NAME,
    $ADMIN_EMAIL
);

if ($result) {
    echo "\n✅ Test email sent with proper template and tracking!\n";
    echo "   Please check your inbox: $testEmail\n";
} else {
    echo "\n❌ Test email failed to send!\n";
}

