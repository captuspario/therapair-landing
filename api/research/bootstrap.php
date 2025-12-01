<?php
declare(strict_types=1);

/**
 * Shared bootstrap for Therapair research API endpoints.
 */

if (!defined('THERAPAIR_APP_ROOT')) {
    define('THERAPAIR_APP_ROOT', dirname(__DIR__, 2));
}

// Load project configuration if available (contains NOTION credentials, etc.)
$configPath = THERAPAIR_APP_ROOT . '/config.php';
if (file_exists($configPath)) {
    /** @noinspection PhpIncludeInspection */
    require_once $configPath;
}

/**
 * Respond with JSON and terminate execution.
 *
 * @param int   $statusCode HTTP status code
 * @param array $payload    Response payload
 */
function json_response(int $statusCode, array $payload): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Retrieve configuration value from defined constant or environment.
 *
 * @param string     $key
 * @param mixed|null $default
 * @return mixed|null
 */
/**
 * Retrieve configuration value from defined constant or environment.
 *
 * @param string $key
 * @param mixed  $default
 * @return mixed
 */
function config_value(string $key, $default = null)
{
    if (defined($key)) {
        /** @noinspection PhpConstantNamingConventionInspection */
        return constant($key);
    }

    $env = getenv($key);
    if ($env !== false && $env !== '') {
        return $env;
    }

    return $default;
}

/**
 * Decode a base64 URL-safe string.
 *
 * @param string $value
 * @return string
 */
function base64url_decode(string $value): string
{
    $padding = 4 - (strlen($value) % 4);
    if ($padding < 4) {
        $value .= str_repeat('=', $padding);
    }

    return (string) base64_decode(strtr($value, '-_', '+/'), true);
}

/**
 * Encode binary data into URL-safe base64.
 *
 * @param string $value
 * @return string
 */
function base64url_encode(string $value): string
{
    return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
}

/**
 * Verify a signed JWT-like token produced for therapist research invites.
 *
 * @param string $token
 * @return array
 * @throws RuntimeException
 */
function verify_research_token(string $token): array
{
    if ($token === 'preview') {
        return [
            'therapist_id' => 'VIC-PREVIEW-0000',
            'therapist_name' => 'Preview Therapist',
            'first_name' => 'Preview',
            'practice_name' => 'Preview Practice',
            'email' => 'preview@example.com',
            'directory_page_id' => null,
            'therapist_research_id' => 'preview',
            'exp' => time() + 86400,
        ];
    }

    $secret = (string) config_value('RESEARCH_TOKEN_SECRET', '');
    if ($secret === '') {
        throw new RuntimeException('Token secret is not configured');
    }

    $parts = explode('.', $token);
    if (count($parts) !== 3) {
        throw new RuntimeException('Malformed token');
    }

    [$headerB64, $payloadB64, $signatureB64] = $parts;
    $signedPortion = $headerB64 . '.' . $payloadB64;
    $expectedSignature = base64url_encode(hash_hmac('sha256', $signedPortion, $secret, true));

    if (!hash_equals($expectedSignature, $signatureB64)) {
        throw new RuntimeException('Invalid token signature');
    }

    $payloadJson = base64url_decode($payloadB64);
    if ($payloadJson === '') {
        throw new RuntimeException('Invalid token payload');
    }

    $payload = json_decode($payloadJson, true, 512, JSON_THROW_ON_ERROR);
    if (!is_array($payload)) {
        throw new RuntimeException('Token payload is not valid JSON');
    }

    if (isset($payload['exp']) && (int) $payload['exp'] < time()) {
        throw new RuntimeException('Token has expired');
    }

    return $payload;
}

/**
 * Safely access JSON request body.
 *
 * @return array
 */
function get_json_body(): array
{
    $raw = file_get_contents('php://input');
    if ($raw === false || $raw === '') {
        return [];
    }

    try {
        $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        return is_array($decoded) ? $decoded : [];
    } catch (JsonException $exception) {
        throw new RuntimeException('Invalid JSON payload: ' . $exception->getMessage(), 0, $exception);
    }
}

/**
 * Build a SHA256 hash of the client IP address with optional salting.
 *
 * @return string|null
 */
function build_ip_hash(): ?string
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? null;
    if (!$ip) {
        return null;
    }

    $salt = (string) config_value('RESEARCH_IP_SALT', '');
    return hash('sha256', $ip . $salt);
}

/**
 * Execute a Notion API request.
 *
 * @param string     $method
 * @param string     $url
 * @param array|null $payload
 * @return array
 */
function notion_request(string $method, string $url, ?array $payload = null): array
{
    $token = (string) config_value('NOTION_TOKEN', '');
    if ($token === '') {
        throw new RuntimeException('Notion token is not configured');
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json',
        'Notion-Version: 2022-06-28',
    ]);

    if ($payload !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    $response = curl_exec($ch);
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($response === false) {
        throw new RuntimeException('Unable to reach Notion API: ' . $curlError);
    }

    $decoded = json_decode($response, true);
    if ($statusCode >= 400) {
        $message = $decoded['message'] ?? $response;
        throw new RuntimeException('Notion API error: ' . $message, $statusCode);
    }

    return is_array($decoded) ? $decoded : [];
}

/**
 * Helper to format multi-select payloads for Notion.
 *
 * @param array $values
 * @return array
 */
function notion_multi_select(array $values): array
{
    $unique = array_values(array_unique(array_filter(array_map(
        static fn ($value) => is_string($value) ? trim($value) : '',
        $values
    ))));

    return array_map(
        static fn ($value) => ['name' => mb_substr($value, 0, 90)],
        $unique
    );
}

/**
 * Helper to format rich text payloads for Notion.
 *
 * @param string|null $content
 * @return array|null
 */
function notion_rich_text(?string $content): ?array
{
    $text = $content !== null ? trim($content) : '';
    if ($text === '') {
        return null;
    }

    return [
        [
            'type' => 'text',
            'text' => [
                'content' => mb_substr($text, 0, 2000),
            ],
        ],
    ];
}

