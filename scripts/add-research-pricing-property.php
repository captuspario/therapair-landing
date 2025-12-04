<?php
/**
 * Add a dedicated 'Pricing' property to the Therapist Research Notion database.
 * Database: THERAPIST_RESEARCH_DATABASE_ID (User Research - Survey Responses)
 *
 * Property:
 *   - Name: Pricing
 *   - Type: rich_text
 *
 * Usage (via SSH on Hostinger):
 *   php /home/u549396201/domains/therapair.com.au/public_html/scripts/add-research-pricing-property.php
 *
 * Usage (via browser):
 *   https://therapair.com.au/scripts/add-research-pricing-property.php
 */

require_once __DIR__ . '/../config.php';

$notionToken = defined('NOTION_TOKEN') ? NOTION_TOKEN : '';
$researchDbId = defined('THERAPIST_RESEARCH_DATABASE_ID') ? THERAPIST_RESEARCH_DATABASE_ID : '';

header('Content-Type: text/plain; charset=utf-8');

echo "📋 Add 'Pricing' Property to Research DB\n";
echo "=======================================\n\n";

echo "NOTION_TOKEN set: " . (!empty($notionToken) ? 'YES' : 'NO') . "\n";
echo "THERAPIST_RESEARCH_DATABASE_ID: " . ($researchDbId ?: 'EMPTY') . "\n\n";

if (empty($notionToken) || empty($researchDbId)) {
    echo "❌ Missing NOTION_TOKEN or THERAPIST_RESEARCH_DATABASE_ID in config.php\n";
    exit(1);
}

// 1) Fetch current database schema
echo "🔍 Fetching existing properties...\n";
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
$existingProperties = $schema['properties'] ?? [];

echo "   Found " . count($existingProperties) . " properties\n";

if (isset($existingProperties['Pricing'])) {
    echo "\n✅ Property 'Pricing' already exists. No changes needed.\n";
    exit(0);
}

echo "➕ Will add property 'Pricing' (type: rich_text)\n\n";

// 2) Build payload to add 'Pricing' as rich_text
$newProperties = [
    'Pricing' => [
        'rich_text' => (object)[],
    ],
];

$payload = [
    'properties' => $newProperties,
];

$ch = curl_init("https://api.notion.com/v1/databases/$researchDbId");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $notionToken,
    'Content-Type: application/json',
    'Notion-Version: 2022-06-28',
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

echo "HTTP Status: $httpCode\n";
echo "Response: $response\n";

if ($httpCode === 200) {
    echo "\n✅ Successfully added 'Pricing' property to research database.\n";
    echo "You should now see a new column named 'Pricing' (rich text) in the User Research - Survey Responses database.\n";
} else {
    echo "\n❌ Failed to add 'Pricing' property. See error above.\n";
    if ($curlError) {
        echo "cURL Error: $curlError\n";
    }
}


