<?php

declare(strict_types=1);

/**
 * Provides catalog summaries by reading JSON datasets through the shared store.
 */
final class JsonCatalogSummary implements CatalogSummaryInterface
{
    /**
     * @param JsonDatasetStore $store Shared JSON dataset store.
     */
    public function __construct(
        private readonly JsonDatasetStore $store
    ) {
    }

    /**
     * Returns the number of records in a resource.
     *
     * @param string $resource Resource identifier.
     *
     * @return int Number of records.
     *
     * @throws InvalidArgumentException If the resource is not supported.
     */
    public function count(string $resource): int
    {
        return match ($resource) {
            'objects' => $this->countItems('weapons')
                + $this->countItems('armor')
                + $this->countItems('ammunition')
                + $this->countItems('tools')
                + $this->countItems('adventuring-gear'),
            'weapons', 'armor', 'ammunition', 'tools', 'adventuring-gear', 'spells', 'monsters'
                => $this->countItems($resource),
            default => throw new InvalidArgumentException("Risorsa non supportata: {$resource}")
        };
    }

    /**
     * Returns navigation categories for a resource.
     *
     * @param string $resource Resource identifier.
     *
     * @return array<int, array{id: string, label: string, route: string, count: int}> Category metadata.
     *
     * @throws InvalidArgumentException If the resource is not supported.
     */
    public function categories(string $resource): array
    {
        return match ($resource) {
            'objects' => [
                $this->category('weapons', 'Armi', 'oggetti/armi'),
                $this->category('armor', 'Armature', 'oggetti/armature'),
                $this->category('ammunition', 'Munizioni', 'oggetti/munizioni'),
                $this->category('tools', 'Strumenti', 'oggetti/strumenti'),
                $this->category('adventuring-gear', 'Equipaggiamento', 'oggetti/avventura')
            ],
            'spells' => array_map(
                fn (int $level): array => [
                    'id' => (string) $level,
                    'label' => $level === 0 ? 'Trucchetti' : $level . '° livello',
                    'route' => 'incantesimi?livello=' . $level,
                    'count' => $this->countSpellsByLevel($level)
                ],
                range(0, 9)
            ),
            'spell-classes' => $this->spellClasses(),
            'monsters' => $this->monsterTypes(),
            default => throw new InvalidArgumentException("Categorie non supportate per la risorsa: {$resource}")
        };
    }

    /**
     * Counts records in a dataset's items collection.
     *
     * @param string $dataset Dataset identifier.
     *
     * @return int Number of records.
     */
    private function countItems(string $dataset): int
    {
        return count($this->store->get($dataset)['items'] ?? []);
    }

    /**
     * Creates metadata for a top-level object category.
     *
     * @param string $dataset Dataset identifier.
     * @param string $label Human-readable category label.
     * @param string $route Frontend route.
     *
     * @return array{id: string, label: string, route: string, count: int} Category metadata.
     */
    private function category(string $dataset, string $label, string $route): array
    {
        return [
            'id' => $dataset,
            'label' => $label,
            'route' => $route,
            'count' => $this->countItems($dataset)
        ];
    }

    /**
     * Counts spells at a given level.
     *
     * @param int $level Spell level.
     *
     * @return int Number of matching spells.
     */
    private function countSpellsByLevel(int $level): int
    {
        return count(array_filter(
            $this->store->get('spells')['items'] ?? [],
            static fn (array $spell): bool => $spell['level'] === $level
        ));
    }

    /**
     * Returns navigation metadata for spell classes.
     *
     * @return array<int, array{id: string, label: string, route: string, count: int}> Spell class metadata.
     */
    private function spellClasses(): array
    {
        $classes = [];
        foreach ($this->store->get('spells')['items'] ?? [] as $spell) {
            if (!is_array($spell) || !isset($spell['classes']) || !is_array($spell['classes'])) {
                continue;
            }

            foreach ($spell['classes'] as $class) {
                if (!is_string($class)) {
                    continue;
                }

                $classes[$class] = ($classes[$class] ?? 0) + 1;
            }
        }

        ksort($classes);

        return array_map(
            static fn (string $class, int $count): array => [
                'id' => $class,
                'label' => ucfirst($class),
                'route' => 'incantesimi?classe=' . rawurlencode($class),
                'count' => $count
            ],
            array_keys($classes),
            array_values($classes)
        );
    }

    /**
     * Returns navigation metadata for monster types.
     *
     * @return array<int, array{id: string, label: string, route: string, count: int}> Monster type metadata.
     */
    private function monsterTypes(): array
    {
        $types = [];
        foreach ($this->store->get('monsters')['items'] ?? [] as $monster) {
            if (!is_array($monster) || !isset($monster['type']) || !is_string($monster['type'])) {
                continue;
            }

            $types[$monster['type']] = ($types[$monster['type']] ?? 0) + 1;
        }

        ksort($types);

        return array_map(
            static fn (string $type, int $count): array => [
                'id' => $type,
                'label' => $type,
                'route' => 'mostri?tipo=' . rawurlencode($type),
                'count' => $count
            ],
            array_keys($types),
            array_values($types)
        );
    }
}
