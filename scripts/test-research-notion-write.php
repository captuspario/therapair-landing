<?php
/**
 * Test script: write a debug entry into the Therapist Research Notion database
 * Database ID: THERAPIST_RESEARCH_DATABASE_ID (expected: 2995c25944da80a5b5d1f0eb9db74a36)
 *
 * Usage (local via SSH on Hostinger):
 *   php /home/u549396201/domains/therapair.com.au/public_html/scripts/test-research-notion-write.php
 *
 * Usage (via browser):
 *   https://therapair.com.au/scripts/test-research-notion-write.php
 */

require_once __DIR__ . '/../config.php';

header('Content-Type: text/plain; charset=utf-8');

$notionToken = defined('NOTION_TOKEN') ? NOTION_TOKEN : '';
$researchDbId = defined('THERAPIST_RESEARCH_DATABASE_ID') ? THERAPIST_RESEARCH_DATABASE_ID : '';

echo "🧪 Test Research Notion Write\n";
echo "==============================\n\n";

echo "NOTION_TOKEN set: " . (!empty($notionToken) ? 'YES' : 'NO') . "\n";
echo "THERAPIST_RESEARCH_DATABASE_ID: " . ($researchDbId ?: 'EMPTY') . "\n\n";

if (empty($notionToken) || empty($researchDbId)) {
    echo "❌ Missing NOTION_TOKEN or THERAPIST_RESEARCH_DATABASE_ID in config.php\n";
    exit(1);
}

$now = date('c');
$title = 'Debug Research Write ' . $now;

// Minimal properties: only use the title property to avoid schema issues.
$titleProperty = defined('NOTION_RESEARCH_TITLE_PROPERTY') ? NOTION_RESEARCH_TITLE_PROPERTY : 'Respondent ID';

$properties = [
    $titleProperty => [
        'title' => [
            [
                'text' => ['content' => $title],
            ],
        ],
    ],
];

// Optional: add a debug comments field if it exists; if not, Notion will ignore it.
$properties['29. Comments'] = [
    'rich_text' => [
        [
            'text' => [
                'content' => 'Automated debug entry created by test-research-notion-write.php at ' . $now,
            ],
        ],
    ],
];

$payload = [
    'parent' => ['database_id' => $researchDbId],
    'properties' => $properties,
];

$ch = curl_init('https://api.notion.com/v1/pages');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $notionToken,
    'Content-Type: application/json',
    'Notion-Version: 2022-06-28',
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: $httpCode\n";
echo "Response: $response\n";

if ($httpCode === 200) {
    $data = json_decode($response, true);
    $pageId = $data['id'] ?? '(unknown)';
    echo "\n✅ Successfully created a page in the research database.\n";
    echo "   Page ID: $pageId\n";
    echo "   Title (Respondent ID): $title\n";
    echo "\nYou should now see this row in the Therapist Research database (ID: $researchDbId).\n";
    echo "Filter/search by the Respondent ID value above or open:\n";
    echo "   https://notion.so/" . str_replace('-', '', $pageId) . "\n";
} else {
    echo "\n❌ Failed to create page in Notion.\n";
}


