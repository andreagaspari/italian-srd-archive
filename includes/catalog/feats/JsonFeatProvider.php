<?php

declare(strict_types=1);

/**
 * Provides feat data by filtering an imported JSON dataset in memory.
 */
final class JsonFeatProvider implements FeatProviderInterface
{
    /**
     * @var array<int, array<string, mixed>>|null
     */
    private ?array $feats = null;

    /**
     * @param JsonDatasetStore $store Shared JSON dataset store.
     */
    public function __construct(
        private readonly JsonDatasetStore $store
    ) {
    }

    /**
     * Retrieves feats by filtering and paginating the JSON dataset in memory.
     *
     * @param FeatQuery $query Filtering and pagination criteria.
     *
     * @return PageResult Query results and pagination metadata.
     */
    public function getFeats(FeatQuery $query): PageResult
    {
        $filteredFeats = array_values(array_filter(
            $this->loadFeats(),
            static function (array $feat) use ($query): bool {
                $searchable = implode(' ', [
                    (string) ($feat['name'] ?? ''),
                    (string) ($feat['subcategory'] ?? ''),
                    (string) ($feat['prerequisite'] ?? ''),
                    (string) ($feat['description'] ?? '')
                ]);
                $search = mb_strtolower(trim((string) $query->search), 'UTF-8');

                return ($query->subcategory === null
                        || $query->subcategory === ''
                        || ($feat['subcategory'] ?? '') === $query->subcategory)
                    && ($search === ''
                        || str_contains(mb_strtolower($searchable, 'UTF-8'), $search));
            }
        ));
        $totalItems = count($filteredFeats);

        if ($query->pageSize === null) {
            return new PageResult($filteredFeats, $query->page, null, $totalItems);
        }

        $offset = ($query->page - 1) * $query->pageSize;

        return new PageResult(
            array_slice($filteredFeats, $offset, $query->pageSize),
            $query->page,
            $query->pageSize,
            $totalItems
        );
    }

    /**
     * Loads and caches the feat collection.
     *
     * @return array<int, array<string, mixed>> Imported feat records.
     *
     * @throws RuntimeException If the dataset cannot be read.
     * @throws UnexpectedValueException If the dataset does not contain an items collection.
     */
    private function loadFeats(): array
    {
        if ($this->feats !== null) {
            return $this->feats;
        }

        $data = $this->store->get('feats');
        if (!isset($data['items']) || !is_array($data['items'])) {
            throw new UnexpectedValueException("Il dataset dei talenti non contiene una collezione 'items'.");
        }

        $this->feats = array_values(array_filter(
            $data['items'],
            static fn (mixed $feat): bool => is_array($feat)
        ));

        return $this->feats;
    }
}
