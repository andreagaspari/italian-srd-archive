<?php

declare(strict_types=1);

/**
 * Provides adventuring gear by filtering an imported JSON dataset in memory.
 */
final class JsonAdventuringGearProvider implements AdventuringGearProviderInterface
{
    /**
     * @var array<int, array<string, mixed>>|null
     */
    private ?array $gear = null;

    /**
     * @param JsonDatasetStore $store Shared JSON dataset store.
     */
    public function __construct(
        private readonly JsonDatasetStore $store
    ) {
    }

    /**
     * Retrieves adventuring gear by filtering and paginating the JSON dataset in memory.
     *
     * @param AdventuringGearQuery $query Filtering and pagination criteria.
     *
     * @return PageResult Query results and pagination metadata.
     */
    public function getAdventuringGear(AdventuringGearQuery $query): PageResult
    {
        $filteredGear = array_values(array_filter(
            $this->loadGear(),
            fn (array $item): bool => (
                $query->category === null
                || $query->category === ''
                || $query->category === 'gear'
                || $item['category'] === $query->category
            )
                && ($query->search === null || $query->search === ''
                    || str_contains(mb_strtolower(json_encode($item, JSON_UNESCAPED_UNICODE) ?: '', 'UTF-8'), mb_strtolower($query->search, 'UTF-8')))
        ));
        $totalItems = count($filteredGear);

        if ($query->pageSize === null) {
            return new PageResult($filteredGear, $query->page, null, $totalItems);
        }

        $offset = ($query->page - 1) * $query->pageSize;
        $items = array_slice($filteredGear, $offset, $query->pageSize);

        return new PageResult($items, $query->page, $query->pageSize, $totalItems);
    }

    /**
     * Loads and caches the adventuring gear collection.
     *
     * @return array<int, array<string, mixed>> Imported gear records.
     *
     * @throws RuntimeException If the dataset cannot be read.
     * @throws UnexpectedValueException If the dataset does not contain an items collection.
     */
    private function loadGear(): array
    {
        if ($this->gear !== null) {
            return $this->gear;
        }

        $data = $this->store->get('adventuring-gear');
        if (!isset($data['items']) || !is_array($data['items'])) {
            throw new UnexpectedValueException("Il dataset dell'equipaggiamento non contiene una collezione 'items'.");
        }

        $this->gear = array_values(array_filter(
            $data['items'],
            static fn (mixed $item): bool => is_array($item)
        ));

        return $this->gear;
    }
}
