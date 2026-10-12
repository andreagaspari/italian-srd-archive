<?php

declare(strict_types=1);

/**
 * Provides entries for the global frontend search index.
 */
interface SearchIndexProviderInterface
{
    /**
     * Returns the search index, optionally narrowed to a query.
     *
     * @param string|null $query Search term used to limit data-backed entries.
     *
     * @return array<int, array<string, mixed>> Search index entries.
     */
    public function getIndex(?string $query = null): array;
}
