<?php

declare(strict_types=1);

/**
 * Provides access to the application's imported data collections.
 */
final class DataCatalog
{
    /**
     * @var array<string, array<string, mixed>>
     */
    private array $loadedData = [];

    /**
     * @param DataImporterInterface $importer Component used to import datasets.
     * @param string $dataDirectory Directory containing the JSON datasets.
     */
    public function __construct(
        private readonly DataImporterInterface $importer,
        private readonly string $dataDirectory
    ) {
    }

    /**
     * Returns the weapons dataset.
     *
     * @return array<string, mixed>
     */
    public function weapons(): array
    {
        return $this->load('weapons');
    }

    /**
     * Returns the armor dataset.
     *
     * @return array<string, mixed>
     */
    public function armor(): array
    {
        return $this->load('armor');
    }

    /**
     * Returns the ammunition dataset.
     *
     * @return array<string, mixed>
     */
    public function ammunition(): array
    {
        return $this->load('ammunition');
    }

    /**
     * Returns the tools dataset.
     *
     * @return array<string, mixed>
     */
    public function tools(): array
    {
        return $this->load('tools');
    }

    /**
     * Returns the adventuring gear dataset.
     *
     * @return array<string, mixed>
     */
    public function adventuringGear(): array
    {
        return $this->load('adventuring-gear');
    }

    /**
     * Returns the spells dataset.
     *
     * @return array<string, mixed>
     */
    public function spells(): array
    {
        return $this->load('spells');
    }

    /**
     * Returns the monsters dataset.
     *
     * @return array<string, mixed>
     */
    public function monsters(): array
    {
        return $this->load('monsters');
    }

    /**
     * Loads a dataset and caches it for subsequent requests.
     *
     * @param string $dataset Dataset filename without the `.json` extension.
     *
     * @return array<string, mixed>
     *
     * @throws RuntimeException If the dataset cannot be read.
     * @throws UnexpectedValueException If the dataset contains invalid JSON data.
     */
    private function load(string $dataset): array
    {
        if (!array_key_exists($dataset, $this->loadedData)) {
            $path = $this->dataDirectory . '/' . $dataset . '.json';
            $this->loadedData[$dataset] = $this->importer->import($path);
        }

        return $this->loadedData[$dataset];
    }
}
