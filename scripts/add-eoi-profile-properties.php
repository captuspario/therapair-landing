<?php
/**
 * Add Profile Creation Properties to EOI DB
 * Adds status tracking for EOI → VIC Therapist profile creation
 */

require_once __DIR__ . '/../config.php';

echo "\n📋 Adding Profile Creation Properties to EOI DB\n";
echo str_repeat('=', 60) . "\n\n";

$databaseId = NOTION_DB_EOI;
$token = NOTION_TOKEN;

$properties = [
    [
        'name' => 'Profile Creation Status',
        'type' => 'select',
        'description' => 'Status of profile creation from this EOI entry',
        'options' => [
            'Pending',
            'In Progress',
            'Created',
            'Rejected',
            'Not Applicable',
        ],
    ],
    [
        'name' => 'Profile Creation Date',
        'type' => 'date',
        'description' => 'When profile was created from this EOI entry',
    ],
    [
        'name' => 'Profile Created By',
        'type' => 'rich_text',
        'description' => 'Who created the profile (admin name)',
    ],
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
    
    $propertyConfig = [];
    
    switch ($property['type']) {
        case 'select':
            $propertyConfig = [
                'select' => [
                    'options' => array_map(function($opt) {
                        return ['name' => $opt];
                    }, $property['options']),
                ],
            ];
            break;
        case 'date':
            $propertyConfig = ['date' => []];
            break;
        case 'rich_text':
            $propertyConfig = ['rich_text' => []];
            break;
    }
    
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

foreach ($properties as $property) {
    echo "   ➕ Adding: {$property['name']} ({$property['type']})...\n";
    if (add_property($databaseId, $property)) {
        echo "      ✅ Added successfully\n";
    } else {
        echo "      ❌ Failed\n";
    }
}

echo "\n✅ Profile creation properties added!\n\n";

