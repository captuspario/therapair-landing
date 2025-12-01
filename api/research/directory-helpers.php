<?php
require_once __DIR__ . '/bootstrap.php';

function find_directory_page_by_email(string $email): ?string
{
    $databaseId = (string) config_value('THERAPIST_DIRECTORY_DATABASE_ID', '');
    if ($databaseId === '') {
        return null;
    }

    $response = notion_request('POST', 'https://api.notion.com/v1/databases/' . $databaseId . '/query', [
        'filter' => [
            'property' => 'Email Address',
            'email' => ['equals' => $email],
        ],
        'page_size' => 1,
    ]);

    $results = $response['results'] ?? [];
    return $results[0]['id'] ?? null;
}

function patch_directory_page(string $pageId, array $properties): void
{
    if ($properties === []) {
        return;
    }

    notion_request('PATCH', 'https://api.notion.com/v1/pages/' . $pageId, ['properties' => $properties]);
}
