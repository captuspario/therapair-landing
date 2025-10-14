<?php
/**
 * Email Preferences Update Handler
 * Updates user preferences in Notion database
 */

// Set content type for JSON response
header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Load configuration
require_once __DIR__ . '/config.php';

// Get form data
$email = trim($_POST['email'] ?? '');
$preferences = $_POST['preferences'] ?? [];
$unsubscribe = isset($_POST['unsubscribe']) && $_POST['unsubscribe'] === '1';

// Validate email
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Valid email address required']);
    exit;
}

// Prepare data for Notion
$notionData = [
    'email' => $email,
    'unsubscribed' => $unsubscribe,
    'email_preferences' => $unsubscribe ? [] : $preferences,
    'last_updated' => date('Y-m-d H:i:s')
];

// Update Notion database
$result = updateNotionPreferences($notionData);

if ($result) {
    echo json_encode(['success' => true, 'message' => 'Preferences updated successfully']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to update preferences']);
}

/**
 * Update preferences in Notion database
 */
function updateNotionPreferences($data) {
    // For MVP, we'll log the update and can integrate with Notion API later
    $logMessage = date('Y-m-d H:i:s') . " - Preference Update: " . json_encode($data) . "\n";
    file_put_contents('preference_updates.log', $logMessage, FILE_APPEND);
    
    // TODO: Integrate with Notion API
    // This is where we'll add the actual Notion API call
    
    return true; // For now, always return success
}

/**
 * Send confirmation email to user
 */
function sendPreferenceConfirmation($email, $preferences, $unsubscribed) {
    $subject = $unsubscribed ? 'You\'ve been unsubscribed from Therapair' : 'Your email preferences have been updated';
    
    if ($unsubscribed) {
        $message = "
        <html>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                <h2 style='color: #2563eb;'>You've been unsubscribed</h2>
                <p>You have successfully unsubscribed from all Therapair communications.</p>
                <p>If you change your mind, you can always update your preferences at:</p>
                <p><a href='https://therapair.com.au/email-preferences.html' style='color: #2563eb;'>https://therapair.com.au/email-preferences.html</a></p>
                <p>Best regards,<br>Therapair Team</p>
            </div>
        </body>
        </html>";
    } else {
        $preferenceList = implode(', ', $preferences);
        $message = "
        <html>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                <h2 style='color: #2563eb;'>Your email preferences have been updated</h2>
                <p>Thank you for updating your email preferences. You will now receive:</p>
                <ul style='list-style-type: disc; margin-left: 20px;'>
                    <li>" . implode('</li><li>', $preferences) . "</li>
                </ul>
                <p>You can change these preferences at any time by visiting:</p>
                <p><a href='https://therapair.com.au/email-preferences.html' style='color: #2563eb;'>https://therapair.com.au/email-preferences.html</a></p>
                <p>Best regards,<br>Therapair Team</p>
            </div>
        </body>
        </html>";
    }
    
    $headers = "From: Therapair <noreply@therapair.com.au>\r\n";
    $headers .= "Reply-To: contact@therapair.com.au\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    return mail($email, $subject, $message, $headers);
}

// Send confirmation email
sendPreferenceConfirmation($email, $preferences, $unsubscribe);
?>

