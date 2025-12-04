<?php

define('APPROOT', dirname(__DIR__, 1));
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/directory-helpers.php';

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

if ($eventType === 'opened') {
    log_email_engagement($email, 'Opened', $payload);
    json_response(200, ['success' => true, 'status' => 'logged']);
}

if ($eventType === 'clicked') {
    log_email_engagement($email, 'Clicked', $payload);
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

function log_email_engagement(string $email, string $eventType, array $payload): void
{
    $url = $payload['url'] ?? '';
    $params = parse_url_query($url);
    $utmEmail = $params['utm_email'] ?? $payload['metadata']['utm']['utm_email'] ?? 'unknown';
    $utmContent = $params['utm_content'] ?? $payload['metadata']['utm']['utm_content'] ?? 'unknown';
    $utmVariant = $params['utm_variant'] ?? $payload['metadata']['utm']['utm_variant'] ?? '';
    $utmExperiment = $params['utm_experiment'] ?? $payload['metadata']['utm']['utm_experiment'] ?? '';

    $label = sprintf('%s (email %s)', $eventType, $utmEmail);
    $noteParts = ['content=' . $utmContent];
    if (!empty($params['token'])) {
        $noteParts[] = 'token_present';
    }
    if (!empty($utmVariant)) {
        $noteParts[] = 'variant=' . $utmVariant;
    }
    if (!empty($utmExperiment)) {
        $noteParts[] = 'experiment=' . $utmExperiment;
    }

    $pageId = find_directory_page_by_email($email);
    if ($pageId === null) {
        return;
    }

    $properties = [];
    
    // Update Research Status
    $statusProp = (string) config_value('NOTION_DIRECTORY_RESEARCH_STATUS_PROPERTY', 'Research Status');
    if ($statusProp !== '') {
        $properties[$statusProp] = ['select' => ['name' => $label]];
    }

    // Track email opens
    if ($eventType === 'Opened') {
        $properties['Research Email Opened'] = ['checkbox' => true];
        $properties['Research Email Opened Date'] = ['date' => ['start' => date('c')]];
        
        // Increment opens count
        $currentOpens = get_property_value($pageId, 'Research Email Opens Count', 'number') ?? 0;
        $properties['Research Email Opens Count'] = ['number' => $currentOpens + 1];
    }

    // Track survey clicks
    if ($eventType === 'Clicked' && ($utmContent === 'survey' || strpos($url, '/research/survey') !== false)) {
        $properties['Research Survey Clicked'] = ['checkbox' => true];
        $properties['Research Survey Clicked Date'] = ['date' => ['start' => date('c')]];
        
        // Increment clicks count
        $currentClicks = get_property_value($pageId, 'Research Survey Clicks Count', 'number') ?? 0;
        $properties['Research Survey Clicks Count'] = ['number' => $currentClicks + 1];
        
        // Update status to "Clicked Survey"
        if ($statusProp !== '') {
            $properties[$statusProp] = ['select' => ['name' => 'Clicked Survey']];
        }
    }

    // Track sandbox clicks
    if ($eventType === 'Clicked' && ($utmContent === 'sandbox_demo' || strpos($url, '/sandbox') !== false)) {
        $properties['Research Sandbox Clicked'] = ['checkbox' => true];
        $properties['Research Sandbox Clicked Date'] = ['date' => ['start' => date('c')]];
        
        // Update status to "Clicked Sandbox"
        if ($statusProp !== '') {
            $properties[$statusProp] = ['select' => ['name' => 'Clicked Sandbox']];
        }
    }

    $latestProp = (string) config_value('NOTION_DIRECTORY_LATEST_SURVEY_PROPERTY', 'Latest Survey Date');
    if ($latestProp !== '') {
        $properties[$latestProp] = ['date' => ['start' => date('c')]];
    }

    $engagementProp = (string) config_value('NOTION_RESEARCH_ENGAGEMENT_SOURCE_PROPERTY', 'Research Source Notes');
    if ($engagementProp !== '') {
        $properties[$engagementProp] = ['rich_text' => [[
            'type' => 'text',
            'text' => ['content' => implode(', ', $noteParts)],
        ]]];
    }

    if ($properties !== []) {
        patch_directory_page($pageId, $properties);
    }
}

/**
 * Get current property value from Notion page
 */
function get_property_value(string $pageId, string $propertyName, string $propertyType): ?int
{
    $url = "https://api.notion.com/v1/pages/$pageId";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . config_value('NOTION_TOKEN', ''),
        'Notion-Version: 2022-06-28'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200) {
        return null;
    }
    
    $data = json_decode($response, true);
    $props = $data['properties'] ?? [];
    
    if (!isset($props[$propertyName])) {
        return null;
    }
    
    $prop = $props[$propertyName];
    
    if ($propertyType === 'number' && isset($prop['number'])) {
        return (int) $prop['number'];
    }
    
    return null;
}

function parse_url_query(string $url): array
{
    if ($url === '') {
        return [];
    }
    $query = parse_url($url, PHP_URL_QUERY) ?? '';
    if ($query === '') {
        return [];
    }
    parse_str($query, $params);
    return array_map('trim', $params);
}
