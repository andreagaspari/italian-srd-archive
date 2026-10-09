<?php

declare(strict_types=1);

/**
 * Contains a page of query results and its pagination metadata.
 */
final class PageResult
{
    /**
     * @param array<int, array<string, mixed>> $items Items in the current page.
     * @param int $page One-based page number.
     * @param int|null $pageSize Maximum number of items per page, or null when unpaginated.
     * @param int $totalItems Total number of matching items before pagination.
     */
    public function __construct(
        public readonly array $items,
        public readonly int $page,
        public readonly ?int $pageSize,
        public readonly int $totalItems
    ) {
    }

    /**
     * Returns the number of available pages.
     *
     * @return int Number of pages, or zero when there are no matching items.
     */
    public function totalPages(): int
    {
        if ($this->totalItems === 0) {
            return 0;
        }

        if ($this->pageSize === null) {
            return 1;
        }

        return (int) ceil($this->totalItems / $this->pageSize);
    }
}
