<?php

declare(strict_types=1);

/**
 * Imports application data from JSON files.
 */
final class JsonFileImporter implements DataImporterInterface
{
    /**
     * Reads and decodes a JSON file.
     *
     * @param string $source Path to the JSON file.
     *
     * @return array<string, mixed>
     *
     * @throws RuntimeException If the file cannot be read.
     * @throws UnexpectedValueException If the decoded JSON is not an array.
     * @throws JsonException If the JSON is invalid.
     */
    public function import(string $source): array
    {
        $contents = file_get_contents($source);
        if ($contents === false) {
            throw new RuntimeException("Impossibile leggere il file JSON: {$source}");
        }

        $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($data)) {
            throw new UnexpectedValueException("Il file JSON non contiene un oggetto valido: {$source}");
        }

        return $data;
    }
}
