<?php

declare(strict_types=1);

/**
 * Provides entries for the global frontend search index.
 */
interface SearchIndexProviderInterface
{
    /**
     * Returns the complete search index.
     *
     * @return array<int, array<string, mixed>> Search index entries.
     */
    public function getIndex(): array;
}
