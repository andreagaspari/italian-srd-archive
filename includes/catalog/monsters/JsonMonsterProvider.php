<?php

declare(strict_types=1);

/**
 * Provides monster data by filtering an imported JSON dataset in memory.
 */
final class JsonMonsterProvider implements MonsterProviderInterface
{
    /**
     * @var array<int, array<string, mixed>>|null
     */
    private ?array $monsters = null;

    /**
     * @param JsonDatasetStore $store Shared JSON dataset store.
     */
    public function __construct(
        private readonly JsonDatasetStore $store
    ) {
    }

    /**
     * Retrieves monsters by filtering and paginating the JSON dataset in memory.
     *
     * @param MonsterQuery $query Filtering and pagination criteria.
     *
     * @return PageResult Query results and pagination metadata.
     */
    public function getMonsters(MonsterQuery $query): PageResult
    {
        $filteredMonsters = array_values(array_filter(
            $this->loadMonsters(),
            fn (array $monster): bool =>
                ($query->type === null || $query->type === '' || $monster['type'] === $query->type)
                && ($query->size === null || $query->size === '' || $monster['size'] === $query->size)
                && ($query->challengeRating === null || $query->challengeRating === '' || $monster['challenge_rating'] === $query->challengeRating)
                && ($query->alignment === null || $query->alignment === '' || $monster['alignment'] === $query->alignment)
                && ($query->search === null || $query->search === ''
                    || str_contains(mb_strtolower(json_encode($monster, JSON_UNESCAPED_UNICODE) ?: '', 'UTF-8'), mb_strtolower($query->search, 'UTF-8')))
        ));
        $totalItems = count($filteredMonsters);

        if ($query->pageSize === null) {
            return new PageResult($filteredMonsters, $query->page, null, $totalItems);
        }

        $offset = ($query->page - 1) * $query->pageSize;
        $items = array_slice($filteredMonsters, $offset, $query->pageSize);

        return new PageResult($items, $query->page, $query->pageSize, $totalItems);
    }

    /**
     * Loads and caches the monster collection.
     *
     * @return array<int, array<string, mixed>> Imported monster records.
     *
     * @throws RuntimeException If the dataset cannot be read.
     * @throws UnexpectedValueException If the dataset does not contain an items collection.
     */
    private function loadMonsters(): array
    {
        if ($this->monsters !== null) {
            return $this->monsters;
        }

        $data = $this->store->get('monsters');
        if (!isset($data['items']) || !is_array($data['items'])) {
            throw new UnexpectedValueException("Il dataset dei mostri non contiene una collezione 'items'.");
        }

        $this->monsters = array_values(array_filter(
            $data['items'],
            static fn (mixed $monster): bool => is_array($monster)
        ));

        return $this->monsters;
    }
}
