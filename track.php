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
    'sandbox' => 'https://therapair.com/sandbox', // Update with actual URL
    'survey' => 'https://therapair.com/survey',
    'home' => 'https://therapair.com'
];

// Get params
$uid = isset($_GET['uid']) ? trim($_GET['uid']) : '';
$destKey = isset($_GET['dest']) ? trim($_GET['dest']) : 'home';
$redirectUrl = isset($destinations[$destKey]) ? $destinations[$destKey] : $destinations['home'];

// If no UID, just redirect
if (empty($uid)) {
    header("Location: $redirectUrl");
    exit;
}

// Track in Notion (Fire and Forget if possible, but PHP is synchronous usually)
// We will try to be fast.
trackClickInNotion($uid);

// Redirect
header("Location: $redirectUrl");
exit;

function trackClickInNotion($pageId)
{
    $notionToken = defined('NOTION_TOKEN') ? NOTION_TOKEN : '';

    if (empty($notionToken))
        return;

    $url = "https://api.notion.com/v1/pages/$pageId";

    $data = [
        'properties' => [
            'Link Clicked Date' => [
                'date' => ['start' => date('c')] // ISO 8601
            ],
            'Status' => [
                'select' => ['name' => 'Clicked Link']
            ]
        ]
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $notionToken,
        'Content-Type: application/json',
        'Notion-Version: 2022-06-28'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_TIMEOUT, 2); // Don't wait too long

    curl_exec($ch);
    curl_close($ch);
}
?>