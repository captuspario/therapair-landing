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

$titleProperty = (string) config_value('NOTION_RESEARCH_TITLE_PROPERTY', 'Respondent ID');
$submittedAt = null;
if (!empty($consent['timestamp']) && is_string($consent['timestamp'])) {
    $submittedAt = $consent['timestamp'];
} else {
    $submittedAt = date('c');
}
$properties = [];
set_title_property($properties, $titleProperty, build_title_value($tokenData, $sessionId, $submittedAt));

set_rich_text_property($properties, (string) config_value('NOTION_RESEARCH_THERAPIST_NAME_PROPERTY', 'Therapist Name'), $tokenData['therapist_name'] ?? null);
set_rich_text_property($properties, (string) config_value('NOTION_RESEARCH_THERAPIST_ID_PROPERTY', 'Therapist ID'), $tokenData['therapist_id'] ?? null);
set_rich_text_property($properties, (string) config_value('NOTION_RESEARCH_DIRECTORY_ID_PROPERTY', 'Directory Page ID'), $tokenData['directory_page_id'] ?? null);
set_rich_text_property($properties, (string) config_value('NOTION_RESEARCH_THERAPIST_EMAIL_PROPERTY', 'Therapist Email'), $tokenData['email'] ?? null);

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

set_select_property($properties, (string) config_value('NOTION_RESEARCH_SCREENS_CLIENTS_PROPERTY', '14. Screening Clients'), $survey['screens_clients']);
set_select_property($properties, '15. Open to Sharing', $survey['open_to_sharing']);

set_multi_select_property($properties, '16. Which Questions Matter', $survey['questions_matter']);
set_rich_text_property($properties, '17. Which Questions Matter Other', $survey['questions_matter_other'] ?? null);

set_select_property($properties, '18. Personality Test', $survey['personality_test']);

set_multi_select_property($properties, '19. Too Personal', $survey['too_personal']);
set_rich_text_property($properties, '20. Too Personal Other', $survey['too_personal_other'] ?? null);

set_select_property($properties, '21. Profile Detail Level', $survey['profile_detail_level']);
set_select_property($properties, '22. Onboarding Time', $survey['onboarding_time']);

set_select_property($properties, '24. Free Listing Interest', $survey['free_listing_interest']);

set_select_property($properties, (string) config_value('NOTION_RESEARCH_PROFILE_INTENT_PROPERTY', 'Profile Intent'), $survey['profile_intent']);
set_select_property($properties, (string) config_value('NOTION_RESEARCH_PROFILE_READY_PROPERTY', 'Profile Ready'), map_profile_ready($survey['profile_intent']));

set_select_property($properties, '25. Future Contact', $survey['future_contact']);
if (!empty($survey['email'])) {
    $properties['1. Email'] = ['email' => $survey['email']];
}

// Pricing fields - using number properties for numeric values
set_number_property($properties, '26. Fee Per Match (Practitioner View)', $survey['value_fee_per_match'] ?? null);
set_number_property($properties, '27. Monthly Subscription (Practitioner View)', $survey['value_monthly_subscription'] ?? null);
set_rich_text_property($properties, '28. Comments', $survey['comments'] ?? null);

set_select_property($properties, '27. Consent Status', 'Granted');
$properties['Submitted'] = [
    'date' => ['start' => $submittedAt],
];

set_rich_text_property($properties, (string) config_value('NOTION_RESEARCH_CONSENT_VERSION_PROPERTY', 'Consent Version'), $consent['version'] ?? null);
$consentTimestampProperty = (string) config_value('NOTION_RESEARCH_CONSENT_TIMESTAMP_PROPERTY', 'Consent Timestamp');
if ($consentTimestampProperty !== '' && !empty($consent['timestamp']) && is_string($consent['timestamp'])) {
    $properties[$consentTimestampProperty] = [
        'date' => ['start' => $consent['timestamp']],
    ];
}

$sessionIdProperty = (string) config_value('NOTION_RESEARCH_SESSION_ID_PROPERTY', 'Survey Session ID');
if ($sessionIdProperty !== '' && !empty($sessionId)) {
    set_rich_text_property($properties, $sessionIdProperty, $sessionId);
}

set_multi_select_property(
    $properties,
    (string) config_value('NOTION_RESEARCH_ENGAGEMENT_SOURCE_PROPERTY', 'Engagement Source'),
    build_engagement_tags($metadata)
);

set_rich_text_property(
    $properties,
    (string) config_value('NOTION_RESEARCH_SOURCE_NOTES_PROPERTY', 'Source Notes'),
    summarise_source_notes($metadata)
);

if (!empty($metadata['ip_hash'])) {
    set_rich_text_property($properties, (string) config_value('NOTION_RESEARCH_IP_HASH_PROPERTY', 'IP Hash'), $metadata['ip_hash']);
}

$notionPayload = [
    'parent' => ['database_id' => $researchDatabaseId],
    'properties' => array_filter($properties, static fn ($value) => $value !== null),
];

try {
    $researchRecord = notion_request('POST', 'https://api.notion.com/v1/pages', $notionPayload);
} catch (RuntimeException $exception) {
    error_log('[Therapair research] Notion create failed: ' . $exception->getMessage());
    json_response(502, [
        'success' => false,
        'error' => 'Saving to research database failed. Please retry shortly.',
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
    'record_id' => $researchRecord['id'] ?? null,
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

