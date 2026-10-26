<?php

declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/database/DatabaseConnection.php';

$query = trim((string) ($_GET['q'] ?? ''));
header('Content-Type: application/json; charset=UTF-8');

if ($query === '') {
    echo json_encode(['items' => []], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    exit;
}

DatabaseConnection::loadEnvironment();
$dataSource = strtoupper(trim((string) (DatabaseConnection::getEnvironment('DATA_SOURCE') ?: 'JSON')));
if (!in_array($dataSource, ['JSON', 'MYSQL'], true)) {
    throw new RuntimeException('DATA_SOURCE deve essere JSON oppure MYSQL.');
}
$provider = $dataSource === 'MYSQL'
    ? (function (): MySqlSearchIndexProvider {
        $store = new JsonDatasetStore(new JsonFileImporter(), __DIR__ . '/data');
        $database = DatabaseConnection::createFromEnvironment();
        return new MySqlSearchIndexProvider(
            $database,
            new SearchIndexBuilder(),
            new RulesCatalog($store, $database)
        );
    })()
    : new JsonSearchIndexProvider(
        new JsonDatasetStore(new JsonFileImporter(), __DIR__ . '/data'),
        new SearchIndexBuilder()
    );
$normalizedQuery = mb_strtolower($query, 'UTF-8');
$items = array_values(array_filter(
    $provider->getIndex($query),
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

usort(
    $items,
    static function (array $left, array $right) use ($normalizedQuery): int {
        $scoreComparison = searchResultScore($right, $normalizedQuery) <=> searchResultScore($left, $normalizedQuery);
        if ($scoreComparison !== 0) {
            return $scoreComparison;
        }

        $titleComparison = strnatcasecmp((string) ($left['title'] ?? ''), (string) ($right['title'] ?? ''));
        if ($titleComparison !== 0) {
            return $titleComparison;
        }

        return strnatcasecmp((string) ($left['section'] ?? ''), (string) ($right['section'] ?? ''));
    }
);

echo json_encode(['items' => array_slice($items, 0, 30)], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

/**
 * Scores a global search result according to title and metadata relevance.
 *
 * @param array<string, mixed> $item Search index entry.
 * @param string $query Lowercase normalized search query.
 *
 * @return int Relevance score, where higher values are more relevant.
 */
function searchResultScore(array $item, string $query): int
{
    $title = mb_strtolower((string) ($item['title'] ?? ''), 'UTF-8');
    $section = mb_strtolower((string) ($item['section'] ?? ''), 'UTF-8');
    $description = mb_strtolower((string) ($item['description'] ?? ''), 'UTF-8');
    $tags = array_map(
        static fn (mixed $tag): string => mb_strtolower((string) $tag, 'UTF-8'),
        is_array($item['tags'] ?? null) ? $item['tags'] : []
    );
    $score = 0;

    if ($title === $query) {
        $score += 1000;
    } elseif (str_starts_with($title, $query)) {
        $score += 800;
    } elseif (preg_match('/(?<![\p{L}\p{N}])' . preg_quote($query, '/') . '(?![\p{L}\p{N}])/u', $title) === 1) {
        $score += 650;
    } elseif (str_contains($title, $query)) {
        $score += 550;
    }

    if (in_array($query, $tags, true) || $section === $query) {
        $score += 350;
    } elseif (str_contains(implode(' ', $tags), $query) || str_contains($section, $query)) {
        $score += 250;
    }

    if (str_contains($description, $query)) {
        $score += 150;
    }

    $searchText = mb_strtolower((string) ($item['search'] ?? ''), 'UTF-8');
    if (str_contains($searchText, $query)) {
        $score += 50;
    }

    $queryTerms = array_values(array_filter(
        preg_split('/\s+/u', $query) ?: [],
        static fn (string $term): bool => $term !== ''
    ));
    if (count($queryTerms) > 1) {
        $matchedTitleTerms = count(array_filter(
            $queryTerms,
            static fn (string $term): bool => str_contains($title, $term)
        ));
        $score += $matchedTitleTerms * 100;
    }

    return $score;
}
