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

// Step 1: Fetch database schema to detect the actual title property name
echo "🔍 Fetching database schema to detect title property...\n";
$schemaCh = curl_init("https://api.notion.com/v1/databases/$researchDbId");
curl_setopt($schemaCh, CURLOPT_RETURNTRANSFER, true);
curl_setopt($schemaCh, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $notionToken,
    'Notion-Version: 2022-06-28',
]);
$schemaResponse = curl_exec($schemaCh);
$schemaCode = curl_getinfo($schemaCh, CURLINFO_HTTP_CODE);
curl_close($schemaCh);

if ($schemaCode !== 200) {
    echo "❌ Failed to fetch database schema. HTTP $schemaCode\n";
    echo "Response: $schemaResponse\n";
    exit(1);
}

$schema = json_decode($schemaResponse, true);
$propertiesDef = $schema['properties'] ?? [];

echo "   Found " . count($propertiesDef) . " properties in schema.\n";

$detectedTitleProperty = null;
foreach ($propertiesDef as $name => $def) {
    $type = $def['type'] ?? '';
    echo "   - $name (type: $type)\n";
    if ($type === 'title' && $detectedTitleProperty === null) {
        $detectedTitleProperty = $name;
    }
}

if ($detectedTitleProperty === null) {
    echo "\n❌ Could not detect a title property in the database schema.\n";
    exit(1);
}

echo "\n✅ Detected title property: $detectedTitleProperty\n\n";

$now = date('c');
$title = 'Debug Research Write ' . $now;

// Minimal properties: only use the detected title property to avoid schema issues.
$properties = [
    $detectedTitleProperty => [
        'title' => [
            [
                'text' => ['content' => $title],
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

echo "\nHTTP Status: $httpCode\n";
echo "Response: $response\n";

if ($httpCode === 200) {
    $data = json_decode($response, true);
    $pageId = $data['id'] ?? '(unknown)';
    echo "\n✅ Successfully created a page in the research database.\n";
    echo "   Page ID: $pageId\n";
    echo "   Title: $title\n";
    echo "\nYou should now see this row in the Therapist Research database (ID: $researchDbId).\n";
    echo "Filter/search by the title value above or open:\n";
    echo "   https://notion.so/" . str_replace('-', '', $pageId) . "\n";
} else {
    echo "\n❌ Failed to create page in Notion.\n";
}


