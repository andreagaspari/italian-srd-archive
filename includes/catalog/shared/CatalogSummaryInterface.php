<?php

declare(strict_types=1);

/**
 * Provides lightweight counts and navigation metadata for catalog resources.
 */
interface CatalogSummaryInterface
{
    /**
     * Returns the number of records in a resource.
     *
     * @param string $resource Resource identifier.
     *
     * @return int Number of records.
     */
    public function count(string $resource): int;

    /**
     * Returns categories that can be used to navigate a resource.
     *
     * @param string $resource Resource identifier.
     *
     * @return array<int, array{id: string, label: string, route: string, count: int}> Category metadata.
     */
    public function categories(string $resource): array;
}
