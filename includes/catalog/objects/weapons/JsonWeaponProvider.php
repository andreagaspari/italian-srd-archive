<?php

declare(strict_types=1);

/**
 * Provides weapon data by filtering an imported JSON dataset in memory.
 */
final class JsonWeaponProvider implements WeaponProviderInterface
{
    /**
     * @var array<int, array<string, mixed>>|null
     */
    private ?array $weapons = null;

    /**
     * @param JsonDatasetStore $store Shared JSON dataset store.
     */
    public function __construct(
        private readonly JsonDatasetStore $store
    ) {
    }

    /**
     * Retrieves weapons by filtering and paginating the JSON dataset in memory.
     *
     * @param WeaponQuery $query Filtering and pagination criteria.
     *
     * @return PageResult Query results and pagination metadata.
     */
    public function getWeapons(WeaponQuery $query): PageResult
    {
        $filteredWeapons = array_values(array_filter(
            $this->loadWeapons(),
            fn (array $weapon): bool => $this->matchesCategory($weapon, $query->category)
                && $this->matchesSearch($weapon, $query->search)
        ));
        $totalItems = count($filteredWeapons);

        if ($query->pageSize === null) {
            return new PageResult($filteredWeapons, $query->page, null, $totalItems);
        }

        $offset = ($query->page - 1) * $query->pageSize;
        $items = array_slice($filteredWeapons, $offset, $query->pageSize);

        return new PageResult($items, $query->page, $query->pageSize, $totalItems);
    }

    /**
     * Loads and caches the weapons collection.
     *
     * @return array<int, array<string, mixed>> Imported weapon records.
     *
     * @throws RuntimeException If the dataset cannot be read.
     * @throws UnexpectedValueException If the dataset does not contain an items collection.
     */
    private function loadWeapons(): array
    {
        if ($this->weapons !== null) {
            return $this->weapons;
        }

        $data = $this->store->get('weapons');
        if (!isset($data['items']) || !is_array($data['items'])) {
            throw new UnexpectedValueException("Il dataset delle armi non contiene una collezione 'items'.");
        }

        $this->weapons = array_values(array_filter(
            $data['items'],
            static fn (mixed $weapon): bool => is_array($weapon)
        ));

        return $this->weapons;
    }

    /**
     * Determines whether a weapon belongs to the requested category.
     *
     * @param array<string, mixed> $weapon Weapon record.
     * @param string|null $category Category identifier, or null for all weapons.
     *
     * @return bool True when the weapon matches the category.
     */
    private function matchesCategory(array $weapon, ?string $category): bool
    {
        return match ($category) {
            null, '', 'items-weapons' => true,
            'items-weapons-simple-melee' => $weapon['proficiency'] === 'Semplice' && $weapon['mode'] === 'Mischia',
            'items-weapons-simple-ranged' => $weapon['proficiency'] === 'Semplice' && $weapon['mode'] === 'A distanza',
            'items-weapons-martial-melee' => $weapon['proficiency'] === 'Da guerra' && $weapon['mode'] === 'Mischia',
            'items-weapons-martial-ranged' => $weapon['proficiency'] === 'Da guerra' && $weapon['mode'] === 'A distanza',
            default => false
        };
    }

    /**
     * Determines whether a weapon contains the requested search text.
     *
     * @param array<string, mixed> $weapon Weapon record.
     * @param string|null $search Search text.
     *
     * @return bool True when the record matches.
     */
    private function matchesSearch(array $weapon, ?string $search): bool
    {
        return $search === null || $search === ''
            || str_contains(mb_strtolower(json_encode($weapon, JSON_UNESCAPED_UNICODE) ?: '', 'UTF-8'), mb_strtolower($search, 'UTF-8'));
    }
}
