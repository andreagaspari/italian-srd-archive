<?php

declare(strict_types=1);

/**
 * Loads and caches JSON datasets shared by catalog facades and providers.
 */
final class JsonDatasetStore
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
     * Returns a dataset, loading it only once per store instance.
     *
     * @param string $dataset Dataset filename without the `.json` extension.
     *
     * @return array<string, mixed> Imported dataset.
     *
     * @throws RuntimeException If the dataset cannot be read.
     * @throws UnexpectedValueException If the dataset contains invalid JSON data.
     */
    public function get(string $dataset): array
    {
        if (!array_key_exists($dataset, $this->loadedData)) {
            $path = $this->dataDirectory . '/' . $dataset . '.json';
            $this->loadedData[$dataset] = $this->importer->import($path);
        }

        return $this->loadedData[$dataset];
    }
}
