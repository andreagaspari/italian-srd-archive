<?php

declare(strict_types=1);

/**
 * Builds the global search index from JSON datasets.
 */
final class JsonSearchIndexProvider implements SearchIndexProviderInterface
{
    /**
     * @var array<int, array<string, mixed>>|null
     */
    private ?array $index = null;

    /**
     * @param JsonDatasetStore $store Shared JSON dataset store.
     * @param SearchIndexBuilder $builder Search index builder.
     */
    public function __construct(
        private readonly JsonDatasetStore $store,
        private readonly SearchIndexBuilder $builder
    ) {
    }

    /**
     * Returns the cached JSON-backed search index.
     *
     * @param string|null $query Ignored because the JSON index is cached as a whole.
     *
     * @return array<int, array<string, mixed>> Search index entries.
     */
    public function getIndex(?string $query = null): array
    {
        if ($this->index === null) {
            $this->index = $this->builder->build([
                'objectData' => $this->store->get('weapons'),
                'armorData' => $this->store->get('armor'),
                'ammunitionData' => $this->store->get('ammunition'),
                'toolsData' => $this->store->get('tools'),
                'adventuringGearData' => $this->store->get('adventuring-gear'),
                'spellsData' => $this->store->get('spells'),
                'monstersData' => $this->store->get('monsters')
            ]);
        }

        return $this->index;
    }
}
