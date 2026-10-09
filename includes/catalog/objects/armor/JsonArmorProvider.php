<?php

declare(strict_types=1);

/**
 * Provides armor data by filtering an imported JSON dataset in memory.
 */
final class JsonArmorProvider implements ArmorProviderInterface
{
    /**
     * @var array<int, array<string, mixed>>|null
     */
    private ?array $armor = null;

    /**
     * @param JsonDatasetStore $store Shared JSON dataset store.
     */
    public function __construct(
        private readonly JsonDatasetStore $store
    ) {
    }

    /**
     * Retrieves armor by filtering and paginating the JSON dataset in memory.
     *
     * @param ArmorQuery $query Filtering and pagination criteria.
     *
     * @return PageResult Query results and pagination metadata.
     */
    public function getArmor(ArmorQuery $query): PageResult
    {
        $filteredArmor = array_values(array_filter(
            $this->loadArmor(),
            fn (array $item): bool => $this->matchesCategory($item, $query->category)
        ));
        $totalItems = count($filteredArmor);

        if ($query->pageSize === null) {
            return new PageResult($filteredArmor, $query->page, null, $totalItems);
        }

        $offset = ($query->page - 1) * $query->pageSize;
        $items = array_slice($filteredArmor, $offset, $query->pageSize);

        return new PageResult($items, $query->page, $query->pageSize, $totalItems);
    }

    /**
     * Loads and caches the armor collection.
     *
     * @return array<int, array<string, mixed>> Imported armor records.
     *
     * @throws RuntimeException If the dataset cannot be read.
     * @throws UnexpectedValueException If the dataset does not contain an items collection.
     */
    private function loadArmor(): array
    {
        if ($this->armor !== null) {
            return $this->armor;
        }

        $data = $this->store->get('armor');
        if (!isset($data['items']) || !is_array($data['items'])) {
            throw new UnexpectedValueException("Il dataset delle armature non contiene una collezione 'items'.");
        }

        $this->armor = array_values(array_filter(
            $data['items'],
            static fn (mixed $item): bool => is_array($item)
        ));

        return $this->armor;
    }

    /**
     * Determines whether an armor item belongs to the requested category.
     *
     * @param array<string, mixed> $item Armor record.
     * @param string|null $category Category identifier, or null for all armor.
     *
     * @return bool True when the armor matches the category.
     */
    private function matchesCategory(array $item, ?string $category): bool
    {
        return match ($category) {
            null, '', 'armor' => true,
            'armor-light' => $item['armor_type'] === 'leggera',
            'armor-medium' => $item['armor_type'] === 'media',
            'armor-heavy' => $item['armor_type'] === 'pesante',
            'armor-shields' => $item['armor_type'] === 'scudo',
            default => false
        };
    }
}
