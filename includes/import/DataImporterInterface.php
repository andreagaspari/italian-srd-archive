<?php

declare(strict_types=1);

/**
 * Defines the contract for importing application data from an external source.
 */
interface DataImporterInterface
{
    /**
     * Imports data from the given source.
     *
     * @param string $source Path or identifier of the data source.
     *
     * @return array<string, mixed>
     */
    public function import(string $source): array;
}
