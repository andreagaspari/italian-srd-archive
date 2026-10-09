<?php

declare(strict_types=1);

/**
 * Provides tool data by filtering an imported JSON dataset in memory.
 */
final class JsonToolsProvider implements ToolsProviderInterface
{
    /**
     * @var array<int, array<string, mixed>>|null
     */
    private ?array $tools = null;

    /**
     * @param JsonDatasetStore $store Shared JSON dataset store.
     */
    public function __construct(
        private readonly JsonDatasetStore $store
    ) {
    }

    /**
     * Retrieves tools by filtering and paginating the JSON dataset in memory.
     *
     * @param ToolsQuery $query Filtering and pagination criteria.
     *
     * @return PageResult Query results and pagination metadata.
     */
    public function getTools(ToolsQuery $query): PageResult
    {
        $filteredTools = array_values(array_filter(
            $this->loadTools(),
            fn (array $tool): bool => (
                $query->category === null
                || $query->category === ''
                || $query->category === 'tools'
                || $tool['tool_type'] === $query->category
            )
                && ($query->search === null || $query->search === ''
                    || str_contains(mb_strtolower(json_encode($tool, JSON_UNESCAPED_UNICODE) ?: '', 'UTF-8'), mb_strtolower($query->search, 'UTF-8')))
        ));
        $totalItems = count($filteredTools);

        if ($query->pageSize === null) {
            return new PageResult($filteredTools, $query->page, null, $totalItems);
        }

        $offset = ($query->page - 1) * $query->pageSize;
        $items = array_slice($filteredTools, $offset, $query->pageSize);

        return new PageResult($items, $query->page, $query->pageSize, $totalItems);
    }

    /**
     * Loads and caches the tools collection.
     *
     * @return array<int, array<string, mixed>> Imported tool records.
     *
     * @throws RuntimeException If the dataset cannot be read.
     * @throws UnexpectedValueException If the dataset does not contain an items collection.
     */
    private function loadTools(): array
    {
        if ($this->tools !== null) {
            return $this->tools;
        }

        $data = $this->store->get('tools');
        if (!isset($data['items']) || !is_array($data['items'])) {
            throw new UnexpectedValueException("Il dataset degli strumenti non contiene una collezione 'items'.");
        }

        $this->tools = array_values(array_filter(
            $data['items'],
            static fn (mixed $item): bool => is_array($item)
        ));

        return $this->tools;
    }
}
