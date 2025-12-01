<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'GET') {
    json_response(405, [
        'success' => false,
        'error' => 'Method not allowed',
    ]);
}

$token = isset($_GET['token']) ? trim((string) $_GET['token']) : '';
if ($token === '') {
    json_response(400, [
        'success' => false,
        'error' => 'Missing session token',
    ]);
}

try {
    $payload = verify_research_token($token);
} catch (RuntimeException $exception) {
    json_response(401, [
        'success' => false,
        'error' => $exception->getMessage(),
    ]);
}

$therapist = [
    'therapist_id' => $payload['therapist_id'] ?? null,
    'therapist_name' => $payload['therapist_name'] ?? null,
    'first_name' => $payload['first_name'] ?? null,
    'practice_name' => $payload['practice_name'] ?? null,
    'email' => $payload['email'] ?? null,
    'directory_page_id' => $payload['directory_page_id'] ?? null,
    'therapist_research_id' => $payload['therapist_research_id'] ?? null,
];

$consentVersion = (string) config_value('RESEARCH_CONSENT_VERSION', '2025-11-13');
$consentLink = (string) config_value('RESEARCH_CONSENT_LINK', '/legal/privacy-policy.html');

json_response(200, [
    'success' => true,
    'data' => $therapist,
    'consent' => [
        'version' => $consentVersion,
        'link' => $consentLink,
    ],
    'cohort' => [
        'name' => $payload['cohort'] ?? 'vic-therapist-research',
        'invited_at' => isset($payload['iat']) ? (int) $payload['iat'] : null,
    ],
]);

