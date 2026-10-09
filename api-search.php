<?php

declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';

$query = trim((string) ($_GET['q'] ?? ''));
header('Content-Type: application/json; charset=UTF-8');

if ($query === '') {
    echo json_encode(['items' => []], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    exit;
}

$datasetStore = new JsonDatasetStore(new JsonFileImporter(), __DIR__ . '/data');
$provider = new JsonSearchIndexProvider($datasetStore, new SearchIndexBuilder());
$normalizedQuery = mb_strtolower($query, 'UTF-8');
$items = array_values(array_filter(
    $provider->getIndex(),
    static fn (array $item): bool => str_contains(
        mb_strtolower(implode(' ', [
            (string) ($item['search'] ?? ''),
            (string) ($item['title'] ?? ''),
            (string) ($item['description'] ?? ''),
            (string) ($item['section'] ?? '')
        ]), 'UTF-8'),
        $normalizedQuery
    )
));

echo json_encode(['items' => array_slice($items, 0, 30)], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
