<?php
/**
 * Add Research Survey Clicked Date Property to EOI DB
 * Tracks when therapists click the research survey link from EOI confirmation email
 */

require_once __DIR__ . '/../config.php';

echo "\n📋 Adding Research Survey Tracking Property to EOI DB\n";
echo str_repeat('=', 60) . "\n\n";

$databaseId = NOTION_DB_EOI;
$token = NOTION_TOKEN;

$property = [
    'name' => 'Research Survey Clicked Date',
    'type' => 'date',
    'description' => 'When research survey link was clicked from EOI confirmation email',
];

function add_property(string $databaseId, array $property): bool
{
    global $token;
    $url = "https://api.notion.com/v1/databases/$databaseId";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json',
        'Notion-Version: 2022-06-28',
    ]);
    
    $propertyConfig = ['date' => []];
    
    $payload = [
        'properties' => [
            $property['name'] => $propertyConfig,
        ],
    ];
    
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    $response = curl_exec($ch);
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($statusCode === 200) {
        return true;
    }
    
    $error = json_decode($response, true);
    $message = $error['message'] ?? 'Unknown error';
    
    // If property already exists, that's okay
    if (strpos($message, 'already exists') !== false || strpos($message, 'duplicate') !== false) {
        return true;
    }
    
    echo "      ⚠️  Error: $message\n";
    return false;
}

echo "📊 EOI DB\n";
echo "   Database ID: $databaseId\n\n";

echo "   ➕ Adding: {$property['name']} ({$property['type']})...\n";
if (add_property($databaseId, $property)) {
    echo "      ✅ Added successfully\n";
} else {
    echo "      ❌ Failed\n";
}

echo "\n✅ Research Survey tracking property added!\n\n";

