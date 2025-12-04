<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    json_response(405, [
        'success' => false,
        'error' => 'Method not allowed',
    ]);
}

try {
    $payload = get_json_body();
} catch (RuntimeException $exception) {
    json_response(400, [
        'success' => false,
        'error' => $exception->getMessage(),
    ]);
}

$token = isset($payload['token']) ? trim((string) $payload['token']) : '';
if ($token === '') {
    json_response(400, [
        'success' => false,
        'error' => 'Missing research token',
    ]);
}

try {
    $tokenData = verify_research_token($token);
} catch (RuntimeException $exception) {
    json_response(401, [
        'success' => false,
        'error' => $exception->getMessage(),
    ]);
}

// Check if this is a preview token - do this early to skip validation
$isPreview = ($token === 'preview' || 
              ($tokenData['therapist_research_id'] ?? null) === 'preview' ||
              ($tokenData['therapist_id'] ?? null) === 'VIC-PREVIEW-0000');

// For preview tokens, skip validation and Notion operations, return success immediately
if ($isPreview) {
    $consent = $payload['consent'] ?? null;
    $sessionId = isset($payload['session_id']) ? trim((string) $payload['session_id']) : null;
    
    json_response(200, [
        'success' => true,
        'record_id' => 'preview-' . time(),
        'preview' => true,
    ]);
}

$consent = $payload['consent'] ?? null;
if (!is_array($consent) || empty($consent['accepted'])) {
    json_response(400, [
        'success' => false,
        'error' => 'Consent must be granted before submitting responses',
    ]);
}

$survey = $payload['survey'] ?? null;
if (!is_array($survey)) {
    json_response(400, [
        'success' => false,
        'error' => 'Survey responses are missing',
    ]);
}

$requiredSingles = [
    'profession',
    'years_practice',
    'biggest_gap',
    'screens_clients',
    'open_to_sharing',
    'personality_test',
    'profile_detail_level',
    'onboarding_time',
    'free_listing_interest',
    'profile_intent',
    'future_contact',
];

foreach ($requiredSingles as $field) {
    if (empty($survey[$field]) || !is_string($survey[$field])) {
        json_response(422, [
            'success' => false,
            'error' => sprintf('Missing response for %s', str_replace('_', ' ', $field)),
        ]);
    }
}

$requiredArrays = [
    'client_types',
    'modalities',
    'clients_find_you',
    'match_factors',
    'questions_matter',
    'too_personal',
];

foreach ($requiredArrays as $field) {
    if (empty($survey[$field]) || !is_array($survey[$field])) {
        json_response(422, [
            'success' => false,
            'error' => sprintf('Please choose at least one option for %s', str_replace('_', ' ', $field)),
        ]);
    }
}

if (
    isset($survey['future_contact'])
    && $survey['future_contact'] === 'Yes'
    && (empty($survey['email']) || !filter_var($survey['email'], FILTER_VALIDATE_EMAIL))
)
{
    json_response(422, [
        'success' => false,
        'error' => 'Please provide a valid email address so we can contact you.',
    ]);
}

$metadata = $payload['metadata'] ?? [];
if (!is_array($metadata)) {
    $metadata = [];
}
$metadata['ip_hash'] = build_ip_hash();
$sessionId = isset($payload['session_id']) ? trim((string) $payload['session_id']) : null;

$researchDatabaseId = (string) config_value('THERAPIST_RESEARCH_DATABASE_ID', '');
if ($researchDatabaseId === '') {
    json_response(500, [
        'success' => false,
        'error' => 'Research database ID not configured',
    ]);
}

$titleProperty = (string) config_value('NOTION_RESEARCH_TITLE_PROPERTY', 'Profession');
$submittedAt = null;
if (!empty($consent['timestamp']) && is_string($consent['timestamp'])) {
    $submittedAt = $consent['timestamp'];
} else {
    $submittedAt = date('c');
}
$properties = [];
// Title property is "Profession" - use the actual profession value from survey
$titleValue = $survey['profession'] ?? 'Unknown';
set_title_property($properties, $titleProperty, $titleValue);

// Skip optional properties that don't exist in the Notion database
// These properties are not in the current schema:
// - Therapist Name
// - Therapist ID  
// - Therapist Email
// - Directory Page ID

set_select_property($properties, '2. Profession', $survey['profession']);
set_rich_text_property($properties, '3. Profession Other', $survey['profession_other'] ?? null);
set_select_property($properties, '4. Years in Practice', $survey['years_practice']);

set_multi_select_property($properties, '5. Client Types', $survey['client_types']);
set_rich_text_property($properties, '6. Client Types Other', $survey['client_types_other'] ?? null);

set_multi_select_property($properties, '7. Modalities', $survey['modalities']);
set_rich_text_property($properties, '8. Modalities Other', $survey['modalities_other'] ?? null);

set_multi_select_property($properties, '9. How Clients Find You', $survey['clients_find_you']);
set_rich_text_property($properties, '10. How Clients Find You Other', $survey['clients_find_you_other'] ?? null);

set_multi_select_property($properties, '11. Great Match Factors', $survey['match_factors']);
set_rich_text_property($properties, '12. Great Match Other', $survey['match_factors_other'] ?? null);

set_rich_text_property($properties, '13. Biggest Gap', $survey['biggest_gap']);

// Use hardcoded property name that exists in Notion
set_select_property($properties, '14. Screening Clients', $survey['screens_clients']);
set_select_property($properties, '15. Open to Sharing', $survey['open_to_sharing']);

set_multi_select_property($properties, '16. Which Questions Matter', $survey['questions_matter']);
set_rich_text_property($properties, '17. Which Questions Matter Other', $survey['questions_matter_other'] ?? null);

set_select_property($properties, '18. Personality Test', $survey['personality_test']);

set_multi_select_property($properties, '19. Too Personal', $survey['too_personal']);
set_rich_text_property($properties, '20. Too Personal Other', $survey['too_personal_other'] ?? null);

set_select_property($properties, '21. Profile Detail Level', $survey['profile_detail_level']);
set_select_property($properties, '22. Onboarding Time', $survey['onboarding_time']);

set_select_property($properties, '24. Free Listing Interest', $survey['free_listing_interest']);

// Skip Profile Intent and Profile Ready - these properties don't exist in the database
// set_select_property($properties, 'Profile Intent', $survey['profile_intent']);
// set_select_property($properties, 'Profile Ready', map_profile_ready($survey['profile_intent']));

set_select_property($properties, '25. Future Contact', $survey['future_contact']);
if (!empty($survey['email'])) {
    $properties['1. Email'] = ['email' => $survey['email']];
}

// Pricing fields - summarise slider values into existing Notes field
// so we don't depend on adding new Notion properties.
$pricingSummaryParts = [];
if (!empty($survey['value_fee_per_match'])) {
    $pricingSummaryParts[] = 'Fee per match (AUD): $' . $survey['value_fee_per_match'];
}
if (!empty($survey['value_monthly_subscription'])) {
    $pricingSummaryParts[] = 'Monthly subscription (AUD): $' . $survey['value_monthly_subscription'];
}
$pricingSummary = $pricingSummaryParts !== [] ? implode(' | ', $pricingSummaryParts) : null;

// Map free-text and pricing into existing research database fields:
// 26. Comments (rich_text)   -> free-text comments from survey
// Pricing (rich_text)        -> dedicated pricing field (pricing summary only)
// Note: Pricing removed from Notes field to avoid duplication
set_rich_text_property($properties, '26. Comments', $survey['comments'] ?? null);
if ($pricingSummary !== null) {
    // Only write to dedicated 'Pricing' field (removed from Notes to avoid duplication)
    set_rich_text_property($properties, 'Pricing', $pricingSummary);
}

// Consent status (existing select property in Notion)
set_select_property($properties, '27. Consent Status', 'Granted');
$properties['Submitted'] = [
    'date' => ['start' => $submittedAt],
];

// Update therapist directory with survey completion
// Extract email and variant from token if available
if (!empty($survey['token'])) {
    try {
        require_once __DIR__ . '/bootstrap.php';
        require_once __DIR__ . '/directory-helpers.php';
        
        // Decode token to get email and variant
        $tokenParts = explode('.', $survey['token']);
        if (count($tokenParts) === 3) {
            $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $tokenParts[1])), true);
            $email = $payload['email'] ?? '';
            $variant = $payload['variant'] ?? '';
            
            if (!empty($email)) {
                $pageId = find_directory_page_by_email($email);
                if ($pageId !== null) {
                    $updateProperties = [
                        'Research Survey Completed' => ['checkbox' => true],
                        'Research Survey Completed Date' => ['date' => ['start' => $submittedAt]],
                        'Research Status' => ['select' => ['name' => 'Completed Survey']],
                    ];
                    
                    // Ensure variant is set if we have it
                    if (!empty($variant)) {
                        $updateProperties['Research Variant'] = ['select' => ['name' => $variant]];
                    }
                    
                    patch_directory_page($pageId, $updateProperties);
                }
            }
        }
    } catch (Exception $e) {
        // Silently fail - survey submission should still succeed
        error_log("Failed to update directory with survey completion: " . $e->getMessage());
    }
}

// Skip optional tracking properties that don't exist in the Notion database:
// - Consent Version
// - Consent Timestamp
// - Survey Session ID
// - Engagement Source
// - Source Notes
// - IP Hash
// These can be added later if needed, but for now we'll skip them to ensure submissions work

// Filter out null values and empty arrays
$filteredProperties = [];
foreach ($properties as $key => $value) {
    if ($value === null) {
        continue;
    }
    // Skip empty arrays for multi-select
    if (is_array($value) && isset($value['multi_select']) && empty($value['multi_select'])) {
        continue;
    }
    // Skip empty rich_text arrays
    if (is_array($value) && isset($value['rich_text']) && empty($value['rich_text'])) {
        continue;
    }
    $filteredProperties[$key] = $value;
}

$notionPayload = [
    'parent' => ['database_id' => $researchDatabaseId],
    'properties' => $filteredProperties,
];

// Log payload for debugging (remove sensitive data in production)
error_log('[Therapair research] Attempting to create Notion page with ' . count($filteredProperties) . ' properties');

$researchRecordId = null;
try {
    $researchRecord = notion_request('POST', 'https://api.notion.com/v1/pages', $notionPayload);
    $researchRecordId = $researchRecord['id'] ?? null;
    error_log('[Therapair research] Successfully created Notion page: ' . ($researchRecordId ?? 'unknown'));
    
    // Set relation to VIC Therapist DB if we have the directory page ID
    $directoryPageId = $tokenData['directory_page_id'] ?? null;
    if ($researchRecordId && $directoryPageId) {
        try {
            // Update Research DB entry with relation to therapist
            notion_request('PATCH', 'https://api.notion.com/v1/pages/' . $researchRecordId, [
                'properties' => [
                    'Related Therapist' => [
                        'relation' => [
                            ['id' => $directoryPageId],
                        ],
                    ],
                ],
            ]);
            
            // Update VIC Therapist DB entry with relation to survey response
            patch_directory_page($directoryPageId, [
                'Related Survey Response' => [
                    'relation' => [
                        ['id' => $researchRecordId],
                    ],
                ],
            ]);
        } catch (Exception $e) {
            // Silently fail - relations are optional
            error_log('[Therapair research] Failed to set relation: ' . $e->getMessage());
        }
    }
} catch (RuntimeException $exception) {
    // Log full error details for debugging
    $errorDetails = [
        'message' => $exception->getMessage(),
        'code' => $exception->getCode(),
        'property_count' => count($filteredProperties),
        'property_names' => array_keys($filteredProperties),
    ];
    error_log('[Therapair research] Notion create failed: ' . json_encode($errorDetails, JSON_PRETTY_PRINT));
    error_log('[Therapair research] Full payload (first 2000 chars): ' . substr(json_encode($notionPayload, JSON_PRETTY_PRINT), 0, 2000));
    
    // Return more detailed error message for debugging
    $errorMessage = 'Saving to research database failed. Please retry shortly.';
    if ($exception->getCode() >= 400 && $exception->getCode() < 500) {
        // Client error - likely a property issue, include the Notion error message
        $errorMessage = 'There was an issue saving your responses: ' . $exception->getMessage();
    }
    
    json_response(502, [
        'success' => false,
        'error' => $errorMessage,
    ]);
}

$directoryPageId = $tokenData['directory_page_id'] ?? $metadata['therapist_directory_id'] ?? null;
if ($directoryPageId) {
    $directoryProperties = build_directory_properties($survey, $consent);

    if (!empty($directoryProperties)) {
        try {
            notion_request(
                'PATCH',
                'https://api.notion.com/v1/pages/' . $directoryPageId,
                ['properties' => $directoryProperties]
            );
        } catch (RuntimeException $exception) {
            error_log('[Therapair research] Directory update failed: ' . $exception->getMessage());
            // Do not fail submission if directory update fails; continue.
        }
    }
}

json_response(200, [
    'success' => true,
    'record_id' => $researchRecordId,
]);

/**
 * Build title value for Notion entry.
 */
function build_title_value(array $tokenData, ?string $sessionId, ?string $submittedAt): string
{
    $parts = [];
    if (!empty($submittedAt)) {
        $parts[] = date('Y-m-d', strtotime($submittedAt));
    }
    if (!empty($tokenData['therapist_name'])) {
        $parts[] = $tokenData['therapist_name'];
    }
    if (!empty($tokenData['therapist_id'])) {
        $parts[] = '[' . $tokenData['therapist_id'] . ']';
    } elseif (!empty($sessionId)) {
        $parts[] = '[' . $sessionId . ']';
    }

    return $parts !== [] ? implode(' ', $parts) : 'Therapair Therapist Research';
}

function set_title_property(array &$properties, string $name, string $content): void
{
    if ($name === '') {
        return;
    }

    $properties[$name] = [
        'title' => [
            [
                'text' => [
                    'content' => mb_substr($content, 0, 200),
                ],
            ],
        ],
    ];
}

function set_select_property(array &$properties, string $name, ?string $value): void
{
    if ($name === '' || $value === null || trim($value) === '') {
        return;
    }

    $properties[$name] = [
        'select' => ['name' => mb_substr(trim($value), 0, 90)],
    ];
}

function set_multi_select_property(array &$properties, string $name, ?array $values): void
{
    if ($name === '' || empty($values) || !is_array($values)) {
        return;
    }

    $options = notion_multi_select($values);
    if ($options === []) {
        return;
    }

    $properties[$name] = ['multi_select' => $options];
}

function set_rich_text_property(array &$properties, string $name, ?string $value): void
{
    if ($name === '') {
        return;
    }

    $richText = notion_rich_text($value);
    if ($richText === null) {
        return;
    }

    $properties[$name] = ['rich_text' => $richText];
}

function set_number_property(array &$properties, string $name, ?string $value): void
{
    if ($name === '' || $value === null || trim($value) === '') {
        return;
    }

    $number = (int) preg_replace('/[^\d]/', '', $value);
    if ($number <= 0) {
        return;
    }

    $properties[$name] = ['number' => $number];
}

function build_engagement_tags(array $metadata): array
{
    $tags = ['vic-therapist-research'];

    if (!empty($metadata['utm']) && is_array($metadata['utm'])) {
        foreach ($metadata['utm'] as $key => $value) {
            if (!is_string($value) || $value === '') {
                continue;
            }
            $tags[] = sprintf('%s:%s', $key, mb_substr($value, 0, 45));
        }
    }

    if (!empty($metadata['sandbox_visit'])) {
        $tags[] = 'sandbox-visitor';
    }

    return array_values(array_unique($tags));
}

function summarise_source_notes(array $metadata): ?string
{
    $items = [];

    if (!empty($metadata['landing_path'])) {
        $items[] = 'Path: ' . $metadata['landing_path'];
    }

    if (!empty($metadata['referrer'])) {
        $items[] = 'Referrer: ' . $metadata['referrer'];
    }

    if (!empty($metadata['utm']) && is_array($metadata['utm'])) {
        foreach ($metadata['utm'] as $key => $value) {
            if (!is_string($value) || $value === '') {
                continue;
            }
            $items[] = strtoupper($key) . ': ' . $value;
        }
    }

    if (!empty($metadata['user_agent'])) {
        $items[] = 'Agent: ' . mb_substr($metadata['user_agent'], 0, 120);
    }

    if (!empty($metadata['ip_hash'])) {
        $items[] = 'IP Hash: ' . $metadata['ip_hash'];
    }

    return $items !== [] ? implode(" • ", $items) : null;
}

function map_profile_ready(?string $intent): ?string
{
    if (!is_string($intent)) {
        return null;
    }

    switch ($intent) {
        case 'Yes':
            return 'Ready';
        case 'Maybe later':
            return 'Later';
        case 'No':
            return 'Declined';
        default:
            return null;
    }
}

function build_directory_properties(array $survey, array $consent): array
{
    $properties = [];

    $researchStatusProperty = (string) config_value('NOTION_DIRECTORY_RESEARCH_STATUS_PROPERTY', 'Research Status');
    set_select_property($properties, $researchStatusProperty, 'Completed');

    $latestSurveyProperty = (string) config_value('NOTION_DIRECTORY_LATEST_SURVEY_PROPERTY', 'Latest Survey Date');
    if (!empty($consent['timestamp']) && is_string($consent['timestamp'])) {
        $properties[$latestSurveyProperty] = [
            'date' => ['start' => $consent['timestamp']],
        ];
    }

    $profileIntentProperty = (string) config_value('NOTION_DIRECTORY_PROFILE_INTENT_PROPERTY', 'Profile Intent');
    set_select_property($properties, $profileIntentProperty, $survey['profile_intent'] ?? null);

    $profileReadyProperty = (string) config_value('NOTION_DIRECTORY_PROFILE_READY_PROPERTY', 'Profile Ready');
    set_select_property($properties, $profileReadyProperty, map_profile_ready($survey['profile_intent'] ?? null));

    $futureContactProperty = (string) config_value('NOTION_DIRECTORY_FUTURE_CONTACT_PROPERTY', 'Research Follow-up');
    set_select_property($properties, $futureContactProperty, $survey['future_contact'] ?? null);

    return array_filter($properties, static fn ($value) => $value !== null);
}

