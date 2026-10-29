<?php

declare(strict_types=1);

/**
 * Provides the global search index from catalog records stored in MySQL.
 */
final class MySqlSearchIndexProvider implements SearchIndexProviderInterface
{
    /**
     * @param PDO $database Configured MySQL connection.
     * @param SearchIndexBuilder $builder Shared index formatting logic.
     * @param RulesCatalog $rulesCatalog Glossary rules provider.
     * @param JsonDatasetStore $store JSON store for datasets not yet normalized in MySQL.
     */
    public function __construct(
        private readonly PDO $database,
        private readonly SearchIndexBuilder $builder,
        private readonly RulesCatalog $rulesCatalog,
        private readonly JsonDatasetStore $store
    ) {
    }

    /**
     * Returns catalog records in the shape expected by the global search UI.
     *
     * When a query is supplied, only matching catalog records are read from
     * MySQL. Static navigation entries are still built so the caller can
     * search them with the same rules as the JSON-backed implementation.
     *
     * @param string|null $query Search term used to limit catalog records.
     *
     * @return array<int, array<string, mixed>> Search index entries.
     */
    public function getIndex(?string $query = null): array
    {
        $sql = 'SELECT dataset, payload
                FROM catalog_items';
        $parameters = [];
        $query = trim((string) $query);
        if ($query !== '') {
            $sql .= " WHERE name LIKE :search_name ESCAPE '\\\\'
                      OR search_text LIKE :search_text ESCAPE '\\\\'";
            $escapedQuery = str_replace(
                ['\\', '%', '_'],
                ['\\\\', '\\%', '\\_'],
                $query
            );
            $searchPattern = '%' . $escapedQuery . '%';
            $parameters['search_name'] = $searchPattern;
            $parameters['search_text'] = $searchPattern;
        }
        $sql .= ' ORDER BY name ASC';
        $statement = $this->database->prepare($sql);
        $statement->execute($parameters);
        $datasets = [
            'objectData' => ['items' => []],
            'armorData' => ['items' => []],
            'ammunitionData' => ['items' => []],
            'toolsData' => ['items' => []],
            'adventuringGearData' => ['items' => []],
            'spellsData' => ['items' => []],
            'featsData' => $this->store->get('feats'),
            'monstersData' => ['items' => []],
            'rulesData' => $this->rulesCatalog->document(),
        ];
        $datasetMap = [
            'weapons' => 'objectData',
            'armor' => 'armorData',
            'ammunition' => 'ammunitionData',
            'tools' => 'toolsData',
            'adventuring-gear' => 'adventuringGearData',
            'spells' => 'spellsData',
            'feats' => 'featsData',
            'monsters' => 'monstersData',
        ];

        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $dataset = (string) $row['dataset'];
            if (!isset($datasetMap[$dataset])) {
                continue;
            }
            $item = json_decode((string) $row['payload'], true, 512, JSON_THROW_ON_ERROR);
            if (!is_array($item)) {
                throw new UnexpectedValueException("Payload non valido nel dataset {$dataset}.");
            }
            $datasets[$datasetMap[$dataset]]['items'][] = $item;
        }

        return $this->builder->build($datasets);
    }
}
