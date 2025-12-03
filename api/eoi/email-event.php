<?php
/**
 * EOI Email Event Webhook Handler
 * Tracks email opens and clicks for EOI confirmation emails
 * Updates Notion EOI database with engagement data
 */

define('APPROOT', dirname(__DIR__, 2));
require_once APPROOT . '/config.php';
require_once __DIR__ . '/../research/bootstrap.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    json_response(405, ['success' => false, 'error' => 'Method not allowed']);
}

$secret = (string) config_value('RESEND_WEBHOOK_SECRET', '');
if ($secret === '') {
    json_response(500, ['success' => false, 'error' => 'Webhook secret not configured']);
}

$rawPayload = file_get_contents('php://input');
$signatureHeader = $_SERVER['HTTP_RESEND_SIGNATURE'] ?? '';
if (!verify_resend_signature($rawPayload, $signatureHeader, $secret)) {
    json_response(403, ['success' => false, 'error' => 'Invalid webhook signature']);
}

try {
    $payload = json_decode($rawPayload, true, 512, JSON_THROW_ON_ERROR);
} catch (JsonException $exception) {
    json_response(400, ['success' => false, 'error' => 'Invalid JSON payload']);
}

$eventType = $payload['event'] ?? $payload['type'] ?? '';
if ($eventType === '') {
    json_response(200, ['success' => true]);
}

$email = get_event_email($payload);
if ($email === '') {
    json_response(400, ['success' => false, 'error' => 'No recipient email provided']);
}

// Handle email events
if ($eventType === 'opened') {
    log_eoi_email_engagement($email, 'opened', $payload);
    json_response(200, ['success' => true, 'status' => 'logged']);
}

if ($eventType === 'clicked') {
    log_eoi_email_engagement($email, 'clicked', $payload);
    json_response(200, ['success' => true, 'status' => 'logged']);
}

json_response(200, ['success' => true, 'status' => 'unhandled']);

/**
 * Verify Resend webhook signature.
 */
function verify_resend_signature(string $payload, string $header, string $secret): bool
{
    if ($header === '') {
        return false;
    }

    $parts = [];
    foreach (explode(',', $header) as $segment) {
        [$key, $value] = array_map('trim', explode('=', $segment, 2) + ['', '']);
        $parts[$key] = $value;
    }

    if (empty($parts['t']) || empty($parts['v1'])) {
        return false;
    }

    $signed = $parts['t'] . '.' . $payload;
    $expected = hash_hmac('sha256', $signed, $secret);

    return hash_equals($expected, $parts['v1']);
}

function get_event_email(array $payload): string
{
    $message = $payload['message'] ?? [];
    if (!empty($message['email'])) {
        return (string) $message['email'];
    }
    if (!empty($message['to'])) {
        if (is_array($message['to'])) {
            return (string) reset($message['to']);
        }
        return (string) $message['to'];
    }
    if (!empty($payload['email'])) {
        return (string) $payload['email'];
    }
    return '';
}

/**
 * Log email engagement to Notion EOI database
 */
function log_eoi_email_engagement(string $email, string $eventType, array $payload): void
{
    $notionToken = defined('NOTION_TOKEN') ? NOTION_TOKEN : '';
    $notionDbEoi = defined('NOTION_DB_EOI') ? NOTION_DB_EOI : '';
    
    if (empty($notionToken) || empty($notionDbEoi)) {
        error_log("EOI email tracking: Missing Notion config. Token=" . (!empty($notionToken) ? 'SET' : 'EMPTY') . ", DB=" . (!empty($notionDbEoi) ? 'SET' : 'EMPTY'));
        return;
    }
    
    // Find EOI entry by email
    $pageId = find_eoi_page_by_email($email, $notionToken, $notionDbEoi);
    if ($pageId === null) {
        error_log("EOI email tracking: Could not find EOI entry for email: $email");
        return;
    }
    
    $now = date('c'); // ISO 8601
    $properties = [];
    
    // Always update Last Engagement Date
    $properties['Last Engagement Date'] = [
        'date' => ['start' => $now]
    ];
    
    if ($eventType === 'opened') {
        // Track email open
        $properties['Email Opened Date'] = [
            'date' => ['start' => $now]
        ];
        
        // Increment open count (if property exists)
        // Note: This requires reading current value first, which we skip for speed
        // You can implement this later if needed
    }
    
    if ($eventType === 'clicked') {
        // Extract destination from URL
        $url = $payload['url'] ?? '';
        $destination = extract_destination_from_url($url);
        
        // Track specific link types
        switch ($destination) {
            case 'sandbox':
                $properties['Sandbox Clicked Date'] = [
                    'date' => ['start' => $now]
                ];
                break;
            case 'preferences':
                $properties['Email Preferences Clicked Date'] = [
                    'date' => ['start' => $now]
                ];
                break;
        }
        
        // Track last clicked link
        $linkName = ucfirst($destination ?: 'unknown');
        $properties['Last Clicked Link'] = [
            'rich_text' => [
                ['text' => ['content' => $linkName]]
            ]
        ];
    }
    
    // Update Notion page
    update_notion_page($pageId, $properties, $notionToken);
}

/**
 * Find EOI page by email address
 */
function find_eoi_page_by_email(string $email, string $notionToken, string $notionDbId): ?string
{
    $url = "https://api.notion.com/v1/databases/$notionDbId/query";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $notionToken,
        'Content-Type: application/json',
        'Notion-Version: 2022-06-28'
    ]);
    
    $filter = [
        'property' => 'Email',
        'email' => ['equals' => $email]
    ];
    
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'filter' => $filter,
        'page_size' => 1
    ]));
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200) {
        error_log("EOI email tracking: Failed to query Notion. HTTP $httpCode: $response");
        return null;
    }
    
    $data = json_decode($response, true);
    if (empty($data['results']) || empty($data['results'][0]['id'])) {
        return null;
    }
    
    return $data['results'][0]['id'];
}

/**
 * Extract destination from tracking URL
 */
function extract_destination_from_url(string $url): string
{
    if (empty($url)) {
        return '';
    }
    
    $parsed = parse_url($url);
    if (empty($parsed['query'])) {
        return '';
    }
    
    parse_str($parsed['query'], $params);
    return $params['dest'] ?? '';
}

/**
 * Update Notion page properties
 */
function update_notion_page(string $pageId, array $properties, string $notionToken): void
{
    if (empty($properties)) {
        return;
    }
    
    $url = "https://api.notion.com/v1/pages/$pageId";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $notionToken,
        'Content-Type: application/json',
        'Notion-Version: 2022-06-28'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['properties' => $properties]));
    curl_setopt($ch, CURLOPT_TIMEOUT, 2); // Fast timeout - don't block
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200) {
        error_log("EOI email tracking: Failed to update Notion page $pageId. HTTP $httpCode: $response");
    }
}

