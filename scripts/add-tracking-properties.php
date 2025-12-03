<?php
/**
 * Add Tracking Properties to Notion EOI Database
 * This script adds the required tracking properties for email tracking
 */

require_once __DIR__ . '/../config.php';

$notionToken = defined('NOTION_TOKEN') ? NOTION_TOKEN : '';
$notionDbEoi = defined('NOTION_DB_EOI') ? NOTION_DB_EOI : '';

if (empty($notionToken)) {
    die("❌ ERROR: NOTION_TOKEN not set in config.php\n");
}

if (empty($notionDbEoi)) {
    die("❌ ERROR: NOTION_DB_EOI not set in config.php\n");
}

echo "📋 Adding tracking properties to Notion EOI database...\n";
echo "   Database ID: $notionDbEoi\n\n";

// Properties to add
$propertiesToAdd = [
    [
        'name' => 'Last Engagement Date',
        'type' => 'date',
        'description' => 'Most recent engagement timestamp (opens/clicks)'
    ],
    [
        'name' => 'Email Opened Date',
        'type' => 'date',
        'description' => 'When email was first opened'
    ],
    [
        'name' => 'Sandbox Clicked Date',
        'type' => 'date',
        'description' => 'When sandbox demo link was clicked'
    ],
    [
        'name' => 'Email Preferences Clicked Date',
        'type' => 'date',
        'description' => 'When email preferences link was clicked'
    ],
    [
        'name' => 'Last Clicked Link',
        'type' => 'rich_text',
        'description' => 'Name of the last link clicked'
    ],
    [
        'name' => 'Email Opens Count',
        'type' => 'number',
        'description' => 'Total number of email opens (optional)'
    ],
    [
        'name' => 'Email Clicks Count',
        'type' => 'number',
        'description' => 'Total number of link clicks (optional)'
    ]
];

// First, get current database schema to check existing properties
echo "🔍 Checking existing properties...\n";
$url = "https://api.notion.com/v1/databases/$notionDbEoi";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $notionToken,
    'Notion-Version: 2022-06-28'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    die("❌ ERROR: Failed to fetch database schema. HTTP $httpCode\nResponse: $response\n");
}

$database = json_decode($response, true);
$existingProperties = $database['properties'] ?? [];

echo "   Found " . count($existingProperties) . " existing properties\n\n";

// Check which properties already exist
$propertiesToCreate = [];
foreach ($propertiesToAdd as $prop) {
    $propName = $prop['name'];
    if (isset($existingProperties[$propName])) {
        echo "✅ Property '$propName' already exists\n";
    } else {
        $propertiesToCreate[] = $prop;
        echo "➕ Will add property '$propName' (type: {$prop['type']})\n";
    }
}

if (empty($propertiesToCreate)) {
    echo "\n✅ All tracking properties already exist! No changes needed.\n";
    exit(0);
}

echo "\n📝 Adding " . count($propertiesToCreate) . " new properties...\n\n";

// Build properties object for Notion API
$newProperties = [];
foreach ($propertiesToCreate as $prop) {
    $propName = $prop['name'];
    $propType = $prop['type'];
    
    switch ($propType) {
        case 'date':
            $newProperties[$propName] = [
                'date' => (object)[]  // Empty object, not array
            ];
            break;
        case 'rich_text':
            $newProperties[$propName] = [
                'rich_text' => (object)[]  // Empty object, not array
            ];
            break;
        case 'number':
            $newProperties[$propName] = [
                'number' => (object)[]  // Empty object, not array
            ];
            break;
        default:
            echo "⚠️  Skipping unknown type '$propType' for property '$propName'\n";
            continue 2;
    }
}

// Update database with new properties
$url = "https://api.notion.com/v1/databases/$notionDbEoi";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $notionToken,
    'Content-Type: application/json',
    'Notion-Version: 2022-06-28'
]);

$payload = [
    'properties' => $newProperties
];

curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($httpCode === 200) {
    echo "✅ Successfully added " . count($propertiesToCreate) . " properties to Notion database!\n\n";
    echo "📊 Added properties:\n";
    foreach ($propertiesToCreate as $prop) {
        echo "   - {$prop['name']} ({$prop['type']})\n";
    }
    echo "\n✅ Tracking properties setup complete!\n";
} else {
    $errorData = json_decode($response, true);
    $errorMessage = $errorData['message'] ?? 'Unknown error';
    echo "❌ ERROR: Failed to add properties. HTTP $httpCode\n";
    echo "   Error: $errorMessage\n";
    if ($curlError) {
        echo "   cURL Error: $curlError\n";
    }
    echo "\n   Response: " . substr($response, 0, 500) . "\n";
    exit(1);
}

echo "\n🎉 Next steps:\n";
echo "   1. Configure Resend webhook (see QA-IMPLEMENTATION-COMPLETE.md)\n";
echo "   2. Deploy code changes\n";
echo "   3. Run QA tests\n";

