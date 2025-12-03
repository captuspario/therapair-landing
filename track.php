<?php
/**
 * Therapair Link Tracker
 * Tracks clicks in Notion before redirecting
 * Usage: track.php?uid={NOTION_PAGE_ID}&dest={destination_key}
 */

// Load configuration
if (file_exists(__DIR__ . '/config.php')) {
    require_once __DIR__ . '/config.php';
}

// Destinations map
$destinations = [
    'sandbox' => 'https://therapair.com.au/sandbox/sandbox-demo.html',
    'preferences' => 'https://therapair.com.au/email-preferences.html',
    'survey' => 'https://therapair.com.au/research/survey',
    'home' => 'https://therapair.com.au'
];

// Get params
$uid = isset($_GET['uid']) ? trim($_GET['uid']) : '';
$destKey = isset($_GET['dest']) ? trim($_GET['dest']) : 'home';
$redirectUrl = isset($destinations[$destKey]) ? $destinations[$destKey] : $destinations['home'];

// Preserve UTM parameters
$utmParams = [];
foreach (['utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term'] as $param) {
    if (isset($_GET[$param])) {
        $utmParams[$param] = $_GET[$param];
    }
}

// Add UTM params to redirect URL if present
if (!empty($utmParams)) {
    $separator = strpos($redirectUrl, '?') !== false ? '&' : '?';
    $redirectUrl .= $separator . http_build_query($utmParams);
}

// If no UID, just redirect (no tracking)
if (empty($uid)) {
    header("Location: $redirectUrl");
    exit;
}

// Track in Notion (Fire and Forget - fast timeout)
trackClickInNotion($uid, $destKey, $utmParams);

// Redirect immediately (don't wait for Notion API)
header("Location: $redirectUrl");
exit;

function trackClickInNotion($pageId, $destination = 'home', $utmParams = [])
{
    $notionToken = defined('NOTION_TOKEN') ? NOTION_TOKEN : '';

    if (empty($notionToken))
        return;

    $url = "https://api.notion.com/v1/pages/$pageId";
    $now = date('c'); // ISO 8601

    // Build properties to update
    $properties = [
        'Last Engagement Date' => [
            'date' => ['start' => $now]
        ]
    ];

    // Track specific link types
    switch ($destination) {
        case 'sandbox':
            $properties['Sandbox Clicked Date'] = [
                'date' => ['start' => $now]
            ];
            break;
        case 'preferences':
            $properties['Email Preferences Clicked Date'] = [
                'date' => ['start' => $now]
            ];
            break;
    }

    // Track last clicked link
    $linkName = ucfirst($destination);
    if (isset($utmParams['utm_campaign'])) {
        $linkName = str_replace('_', ' ', $utmParams['utm_campaign']);
    }
    $properties['Last Clicked Link'] = [
        'rich_text' => [
            ['text' => ['content' => $linkName]]
        ]
    ];

    // Increment click count (if property exists)
    // Note: This requires reading current value first, which we skip for speed
    // You can implement this later if needed

    $data = ['properties' => $properties];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $notionToken,
        'Content-Type: application/json',
        'Notion-Version: 2022-06-28'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_TIMEOUT, 1); // Very fast - don't block redirect

    curl_exec($ch);
    curl_close($ch);
}
?>