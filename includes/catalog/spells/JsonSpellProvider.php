<?php

declare(strict_types=1);

/**
 * Provides spell data by filtering an imported JSON dataset in memory.
 */
final class JsonSpellProvider implements SpellProviderInterface
{
    /**
     * @var array<int, array<string, mixed>>|null
     */
    private ?array $spells = null;

    /**
     * @param JsonDatasetStore $store Shared JSON dataset store.
     */
    public function __construct(
        private readonly JsonDatasetStore $store
    ) {
    }

    /**
     * Retrieves spells by filtering and paginating the JSON dataset in memory.
     *
     * @param SpellQuery $query Filtering and pagination criteria.
     *
     * @return PageResult Query results and pagination metadata.
     */
    public function getSpells(SpellQuery $query): PageResult
    {
        $filteredSpells = array_values(array_filter(
            $this->loadSpells(),
            fn (array $spell): bool =>
                ($query->level === null || $spell['level'] === $query->level)
                && ($query->school === null || $query->school === '' || $spell['school'] === $query->school)
                && ($query->className === null || $query->className === '' || in_array($query->className, $spell['classes'], true))
        ));
        $totalItems = count($filteredSpells);

        if ($query->pageSize === null) {
            return new PageResult($filteredSpells, $query->page, null, $totalItems);
        }

        $offset = ($query->page - 1) * $query->pageSize;
        $items = array_slice($filteredSpells, $offset, $query->pageSize);

        return new PageResult($items, $query->page, $query->pageSize, $totalItems);
    }

    /**
     * Loads and caches the spell collection.
     *
     * @return array<int, array<string, mixed>> Imported spell records.
     *
     * @throws RuntimeException If the dataset cannot be read.
     * @throws UnexpectedValueException If the dataset does not contain an items collection.
     */
    private function loadSpells(): array
    {
        if ($this->spells !== null) {
            return $this->spells;
        }

        $data = $this->store->get('spells');
        if (!isset($data['items']) || !is_array($data['items'])) {
            throw new UnexpectedValueException("Il dataset degli incantesimi non contiene una collezione 'items'.");
        }

        $this->spells = array_values(array_filter(
            $data['items'],
            static fn (mixed $spell): bool => is_array($spell)
        ));

        return $this->spells;
    }
}
