<?php

declare(strict_types=1);

/**
 * Provides ammunition data from an imported JSON dataset.
 */
final class JsonAmmunitionProvider implements AmmunitionProviderInterface
{
    /**
     * @var array<int, array<string, mixed>>|null
     */
    private ?array $ammunition = null;

    /**
     * @param JsonDatasetStore $store Shared JSON dataset store.
     */
    public function __construct(
        private readonly JsonDatasetStore $store
    ) {
    }

    /**
     * Retrieves ammunition by paginating the JSON dataset in memory.
     *
     * @param AmmunitionQuery $query Pagination criteria.
     *
     * @return PageResult Query results and pagination metadata.
     */
    public function getAmmunition(AmmunitionQuery $query): PageResult
    {
        $ammunition = $this->loadAmmunition();
        $totalItems = count($ammunition);

        if ($query->pageSize === null) {
            return new PageResult($ammunition, $query->page, null, $totalItems);
        }

        $offset = ($query->page - 1) * $query->pageSize;
        $items = array_slice($ammunition, $offset, $query->pageSize);

        return new PageResult($items, $query->page, $query->pageSize, $totalItems);
    }

    /**
     * Loads and caches the ammunition collection.
     *
     * @return array<int, array<string, mixed>> Imported ammunition records.
     *
     * @throws RuntimeException If the dataset cannot be read.
     * @throws UnexpectedValueException If the dataset does not contain an items collection.
     */
    private function loadAmmunition(): array
    {
        if ($this->ammunition !== null) {
            return $this->ammunition;
        }

        $data = $this->store->get('ammunition');
        if (!isset($data['items']) || !is_array($data['items'])) {
            throw new UnexpectedValueException("Il dataset delle munizioni non contiene una collezione 'items'.");
        }

        $this->ammunition = array_values(array_filter(
            $data['items'],
            static fn (mixed $item): bool => is_array($item)
        ));

        return $this->ammunition;
    }
}
