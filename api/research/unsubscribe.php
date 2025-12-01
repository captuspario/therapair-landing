<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/directory-helpers.php';

// Only allow GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    header('Allow: GET');
    exit;
}

// Get and validate email parameter
$email = filter_input(INPUT_GET, 'email', FILTER_VALIDATE_EMAIL);

if ($email === false || $email === null) {
    http_response_code(400);
    header('Content-Type: text/html; charset=utf-8');
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Invalid Unsubscribe Link - Therapair</title>
        <style>
            * { box-sizing: border-box; margin: 0; padding: 0; }
            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
                background-color: #f5f5f5;
                color: #1F2937;
                line-height: 1.6;
                padding: 20px;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
            }
            .container {
                background: #ffffff;
                border-radius: 8px;
                padding: 40px;
                max-width: 600px;
                width: 100%;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                text-align: center;
            }
            h1 { color: #1F2937; margin-bottom: 16px; font-size: 24px; }
            p { color: #6B7280; margin-bottom: 24px; }
            a { color: #3A6EA5; text-decoration: underline; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>Invalid Unsubscribe Link</h1>
            <p>The unsubscribe link you clicked is invalid or malformed.</p>
            <p>If you need to unsubscribe, please contact us at <a href="mailto:contact@therapair.com.au">contact@therapair.com.au</a>.</p>
            <p><a href="https://therapair.com.au">Return to Therapair homepage</a></p>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Store safe email for output
$safeEmail = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');

// Find the therapist directory page by email
$notFound = false;
try {
    $pageId = find_directory_page_by_email($email);
    if ($pageId === null) {
        $notFound = true;
    }
} catch (Throwable $exception) {
    error_log('Failed to query Notion directory for unsubscribe: ' . $exception->getMessage());
    $pageId = null;
    $notFound = true;
}

// Update Notion database to mark as unsubscribed
$statusProperty = (string) config_value('NOTION_DIRECTORY_RESEARCH_STATUS_PROPERTY', 'Research Status');
$properties = [];

if ($statusProperty !== '') {
    $properties[$statusProperty] = ['select' => ['name' => 'Unsubscribed']];
}

$followUpProperty = (string) config_value('NOTION_DIRECTORY_FUTURE_CONTACT_PROPERTY', 'Research Follow-up');
if ($followUpProperty !== '') {
    $properties[$followUpProperty] = ['rich_text' => [[
        'type' => 'text',
        'text' => ['content' => 'User unsubscribed via email link on ' . date('Y-m-d H:i:s')],
    ]]];
}

// Update the Notion page
$unsubscribed = false;
if (!$notFound && !empty($properties)) {
    try {
        notion_request('PATCH', 'https://api.notion.com/v1/pages/' . $pageId, ['properties' => $properties]);
        $unsubscribed = true;
    } catch (Exception $e) {
        // Log error but still show success message to user
        error_log('Failed to update Notion unsubscribe status: ' . $e->getMessage());
    }
}

// Send HTML response
header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Successfully Unsubscribed - Therapair</title>
    <meta name="robots" content="noindex, nofollow">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f5f5f5;
            color: #1F2937;
            line-height: 1.6;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background: #ffffff;
            border-radius: 8px;
            padding: 40px;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 32px;
            padding-bottom: 24px;
            border-bottom: 1px solid #E5E7EB;
        }

        .logo {
            color: #3A6EA5;
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .subtitle {
            color: #6B7280;
            font-size: 14px;
        }

        .success-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 24px;
            background-color: #10B981;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 32px;
        }

        h1 {
            color: #1F2937;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 16px;
            text-align: center;
        }

        .content {
            margin-bottom: 32px;
        }

        p {
            color: #374151;
            font-size: 16px;
            margin-bottom: 16px;
            line-height: 1.7;
        }

        .highlight {
            background-color: #F3F4F6;
            border-left: 4px solid #3A6EA5;
            padding: 16px 20px;
            margin: 24px 0;
            border-radius: 4px;
        }

        .highlight p {
            margin-bottom: 0;
            color: #1F2937;
        }

        .preference-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            margin: 28px 0 8px;
        }

        .preference-option {
            background: #F9FAFB;
            border: 1px solid #E5E7EB;
            border-radius: 10px;
            padding: 18px 20px;
            text-decoration: none;
            color: #1F2937;
            display: flex;
            flex-direction: column;
            gap: 8px;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .preference-option:hover,
        .preference-option:focus-visible {
            border-color: #3A6EA5;
            box-shadow: 0 12px 24px rgba(58, 110, 165, 0.12);
        }

        .preference-option h2 {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
            color: #1F2937;
        }

        .preference-option p {
            margin: 0;
            font-size: 0.95rem;
            color: #4B5563;
            line-height: 1.55;
        }

        .button-group {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 32px;
        }

        .button {
            display: inline-block;
            padding: 12px 24px;
            border-radius: 6px;
            text-decoration: none;
            text-align: center;
            font-weight: 600;
            font-size: 16px;
            transition: background-color 0.2s;
        }

        .button-primary {
            background-color: #3A6EA5;
            color: #ffffff;
        }

        .button-primary:hover {
            background-color: #2C5A8A;
        }

        .button-secondary {
            background-color: #F3F4F6;
            color: #374151;
            border: 1px solid #E5E7EB;
        }

        .button-secondary:hover {
            background-color: #E5E7EB;
        }

        .footer {
            margin-top: 40px;
            padding-top: 24px;
            border-top: 1px solid #E5E7EB;
            text-align: center;
        }

        .footer p {
            color: #6B7280;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .footer a {
            color: #3A6EA5;
            text-decoration: underline;
        }

        .footer-links {
            margin-top: 16px;
        }

        .footer-links a {
            color: #6B7280;
            text-decoration: none;
            font-size: 14px;
            margin: 0 8px;
        }

        .footer-links a:hover {
            color: #3A6EA5;
            text-decoration: underline;
        }

        @media (max-width: 640px) {
            .container {
                padding: 24px;
            }

            h1 {
                font-size: 20px;
            }

            p {
                font-size: 15px;
            }

            .button-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Therapair</div>
            <div class="subtitle">A non-profit initiative from Unison Mental Health</div>
        </div>

        <div class="success-icon">✓</div>

        <h1>You've Been Unsubscribed</h1>

        <div class="content">
            <?php if ($notFound): ?>
                <p>
                    We couldn't find <strong><?php echo $safeEmail; ?></strong> in our active research invite list,
                    but we've recorded your request and added the address to our do-not-contact list.
                </p>
            <?php else: ?>
                <p>
                    You have successfully unsubscribed from Therapair's research campaign emails.
                    We respect your privacy and will not send you any further messages from this sequence.
                </p>
            <?php endif; ?>

            <div class="highlight">
                <p>
                    <strong>What this means:</strong><br>
                    You will no longer receive research invitation emails or follow-up messages from this campaign.
                </p>
            </div>

            <p>
                If you change your mind in the future, or if you'd like to receive different types of communications from Therapair,
                please don't hesitate to reach out to us.
            </p>

            <div class="preference-options" role="list">
                <a class="preference-option" href="mailto:contact@therapair.com.au?subject=Therapair%20research%20preferences&body=Hi%20Therapair%20team%2C%0A%0AI'd%20like%20to%20keep%20receiving%20research%20updates%20but%20at%20a%20lower%20frequency.%0A%0AThanks!">
                    <h2>Fewer updates</h2>
                    <p>Prefer a monthly digest instead of immediate invitations? Let us know and we’ll adjust the cadence.</p>
                </a>
                <a class="preference-option" href="mailto:contact@therapair.com.au?subject=Pause%20Therapair%20research%20emails&body=Hi%20Therapair%20team%2C%0A%0APlease%20pause%20my%20research%20emails%20for%2030%20days.%0A%0AThanks!">
                    <h2>Pause for 30 days</h2>
                    <p>Need a breather? Ask us to pause your participation and we’ll reach out again later.</p>
                </a>
                <a class="preference-option" href="mailto:contact@therapair.com.au?subject=Update%20my%20Therapair%20research%20profile&body=Hi%20Therapair%20team%2C%0A%0AI'd%20like%20to%20update%20my%20contact%20details%20or%20practice%20information.%0A%0AThanks!">
                    <h2>Update my details</h2>
                    <p>Changed practice, email, or preferred contact method? We’ll refresh your profile.</p>
                </a>
            </div>
        </div>

        <div class="button-group">
            <a href="https://therapair.com.au" class="button button-primary">
                Visit Therapair Homepage
            </a>
            <a href="mailto:contact@therapair.com.au" class="button button-secondary">
                Contact Us
            </a>
        </div>

        <div class="footer">
            <p>
                <strong>Need help?</strong> If you have any questions or concerns, we're here to help.
            </p>
            <div class="footer-links">
                <a href="https://therapair.com.au/legal/privacy-policy.html">Privacy Policy</a>
                <span>•</span>
                <a href="https://therapair.com.au/legal/consent-removal.html">Consent & Removal</a>
                <span>•</span>
                <a href="mailto:contact@therapair.com.au">Contact Us</a>
            </div>
        </div>
    </div>
</body>
</html>
