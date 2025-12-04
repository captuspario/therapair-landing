<?php
/**
 * Test Survey Submission Endpoint
 * Simulates a survey submission to test the API
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../api/research/bootstrap.php';

echo "\n🧪 Testing Survey Submission Endpoint\n";
echo str_repeat('=', 60) . "\n\n";

// Create a test payload (minimal valid submission)
$testPayload = [
    'token' => 'preview', // Use preview token for testing
    'consent' => [
        'accepted' => true,
        'timestamp' => date('c'),
    ],
    'survey' => [
        'profession' => 'Psychologist',
        'years_practice' => '5-10 years',
        'client_types' => ['Adults'],
        'modalities' => ['CBT'],
        'clients_find_you' => ['Referrals'],
        'match_factors' => ['Shared core values'],
        'biggest_gap' => 'Test gap',
        'screens_clients' => 'Yes',
        'open_to_sharing' => 'Yes',
        'questions_matter' => ['Communication style'],
        'personality_test' => 'Yes',
        'too_personal' => ['None'],
        'profile_detail_level' => '5',
        'onboarding_time' => '5 min',
        'free_listing_interest' => 'Yes',
        'profile_intent' => 'Yes',
        'future_contact' => 'No',
    ],
    'metadata' => [],
];

echo "📤 Sending test submission...\n";
echo "   Token: preview (test mode)\n";
echo "   Database: " . THERAPIST_RESEARCH_DATABASE_ID . "\n\n";

// Simulate the endpoint
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = [];

// Capture output
ob_start();

try {
    // Include the response endpoint
    require __DIR__ . '/../api/research/response.php';
} catch (Exception $e) {
    $output = ob_get_clean();
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Output: " . $output . "\n";
    exit(1);
}

$output = ob_get_clean();
$result = json_decode($output, true);

if ($result && isset($result['success'])) {
    if ($result['success']) {
        echo "✅ Submission successful!\n";
        echo "   Record ID: " . ($result['record_id'] ?? 'N/A') . "\n";
        if (isset($result['preview'])) {
            echo "   Mode: Preview (no database entry created)\n";
        }
    } else {
        echo "❌ Submission failed\n";
        echo "   Error: " . ($result['error'] ?? 'Unknown error') . "\n";
    }
} else {
    echo "⚠️  Unexpected response format\n";
    echo "   Output: " . substr($output, 0, 500) . "\n";
}

echo "\n";
