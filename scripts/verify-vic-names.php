<?php
/**
 * Verify VIC Therapist Database Name Properties
 * Check specific entries to ensure they're correct
 */

require_once __DIR__ . '/../config.php';

$notionToken = defined('NOTION_TOKEN') ? NOTION_TOKEN : '';
$vicDbId = defined('NOTION_DB_USER_TESTING') ? NOTION_DB_USER_TESTING : '28c5c25944da80a48d85fd43119f4ec1';

if (empty($notionToken)) {
    die("❌ Error: NOTION_TOKEN not configured\n");
}

// Search for specific entries
$searchTerms = ['Anne Louise Thorbecke', 'Anne Robertson', 'Kah Yan'];

echo "🔍 Verifying specific entries...\n\n";

foreach ($searchTerms as $searchTerm) {
    $url = "https://api.notion.com/v1/databases/$vicDbId/query";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $notionToken,
        'Content-Type: application/json',
        'Notion-Version: 2022-06-28'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'filter' => [
            'or' => [
                [
                    'property' => 'First Name',
                    'title' => ['contains' => $searchTerm]
                ],
                [
                    'property' => 'Fullname',
                    'rich_text' => ['contains' => $searchTerm]
                ],
                [
                    'property' => 'Last Name',
                    'rich_text' => ['contains' => $searchTerm]
                ]
            ]
        ]
    ]));

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        $data = json_decode($response, true);
        $results = $data['results'] ?? [];
        
        foreach ($results as $therapist) {
            $props = $therapist['properties'] ?? [];
            
            $firstName = '';
            $fullname = '';
            $lastName = '';
            
            if (isset($props['First Name']['title']) && !empty($props['First Name']['title'])) {
                $firstName = trim($props['First Name']['title'][0]['text']['content'] ?? '');
            }
            
            if (isset($props['Fullname']['rich_text']) && !empty($props['Fullname']['rich_text'])) {
                $fullname = trim($props['Fullname']['rich_text'][0]['text']['content'] ?? '');
            }
            
            if (isset($props['Last Name']['rich_text']) && !empty($props['Last Name']['rich_text'])) {
                $lastName = trim($props['Last Name']['rich_text'][0]['text']['content'] ?? '');
            }
            
            echo "📋 Entry: $searchTerm\n";
            echo "   First Name: \"$firstName\"\n";
            echo "   Last Name: \"$lastName\"\n";
            echo "   Fullname: \"$fullname\"\n";
            
            // Verify correctness
            $expectedFullname = trim($firstName . ' ' . $lastName);
            $isCorrect = strtolower($fullname) === strtolower($expectedFullname);
            
            if ($isCorrect) {
                echo "   ✅ Correct: Fullname matches First + Last\n";
            } else {
                echo "   ⚠️  Issue: Fullname should be \"$expectedFullname\" but is \"$fullname\"\n";
            }
            
            // Check if first name contains last name
            $firstNameWords = explode(' ', $firstName);
            if (count($firstNameWords) >= 3) {
                echo "   ⚠️  Warning: First Name has 3+ words (might contain last name)\n";
            }
            
            echo "\n";
        }
    }
}

echo "✅ Verification complete!\n";

