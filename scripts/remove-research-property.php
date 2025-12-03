<?php
/**
 * Remove a property from the Therapist Research Notion database.
 * Specifically used to remove the '23. Trust AI Matching' property.
 *
 * Usage (via SSH on Hostinger):
 *   php /home/u549396201/domains/therapair.com.au/public_html/scripts/remove-research-property.php
 *
 * Usage (via browser):
 *   https://therapair.com.au/scripts/remove-research-property.php
 */

require_once __DIR__ . '/../config.php';

header('Content-Type: text/plain; charset=utf-8');

$notionToken = defined('NOTION_TOKEN') ? NOTION_TOKEN : '';
$researchDbId = defined('THERAPIST_RESEARCH_DATABASE_ID') ? THERAPIST_RESEARCH_DATABASE_ID : '';

echo "🧪 Remove Research Property\n";
echo "==========================\n\n";

echo "NOTION_TOKEN set: " . (!empty($notionToken) ? 'YES' : 'NO') . "\n";
echo "THERAPIST_RESEARCH_DATABASE_ID: " . ($researchDbId ?: 'EMPTY') . "\n\n";

if (empty($notionToken) || empty($researchDbId)) {
    echo "❌ Missing NOTION_TOKEN or THERAPIST_RESEARCH_DATABASE_ID in config.php\n";
    exit(1);
}

$propertyToRemove = '23. Trust AI Matching';

echo "Attempting to remove property: $propertyToRemove\n\n";

$payload = [
    'properties' => [
        $propertyToRemove => null,
    ],
];

$ch = curl_init("https://api.notion.com/v1/databases/$researchDbId");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
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
    echo "\n✅ Successfully requested removal of property '$propertyToRemove'.\n";
    echo "Check the Therapist Research database schema in Notion to confirm it's gone.\n";
} else {
    echo "\n❌ Failed to remove property. See response above.\n";
}


