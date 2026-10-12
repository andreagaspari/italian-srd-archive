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
     */
    public function __construct(
        private readonly PDO $database,
        private readonly SearchIndexBuilder $builder
    ) {
    }

    /**
     * Returns catalog records in the shape expected by the global search UI.
     *
     * @return array<int, array<string, mixed>> Search index entries.
     */
    public function getIndex(): array
    {
        $statement = $this->database->query(
            'SELECT dataset, payload
             FROM catalog_items
             ORDER BY name ASC'
        );
        $datasets = [
            'objectData' => ['items' => []],
            'armorData' => ['items' => []],
            'ammunitionData' => ['items' => []],
            'toolsData' => ['items' => []],
            'adventuringGearData' => ['items' => []],
            'spellsData' => ['items' => []],
            'monstersData' => ['items' => []],
        ];
        $datasetMap = [
            'weapons' => 'objectData',
            'armor' => 'armorData',
            'ammunition' => 'ammunitionData',
            'tools' => 'toolsData',
            'adventuring-gear' => 'adventuringGearData',
            'spells' => 'spellsData',
            'monsters' => 'monstersData',
        ];

        foreach ($statement->fetchAll() as $row) {
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
