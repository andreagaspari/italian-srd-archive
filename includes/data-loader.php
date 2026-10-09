<?php

declare(strict_types=1);

require_once __DIR__ . '/import/DataImporterInterface.php';
require_once __DIR__ . '/import/JsonFileImporter.php';
require_once __DIR__ . '/catalog/DataCatalog.php';

/**
 * Loads a JSON file through the default JSON importer.
 *
 * @param string $path Path to the JSON file.
 *
 * @return array<string, mixed>
 *
 * @throws RuntimeException If the file cannot be read.
 * @throws UnexpectedValueException If the decoded JSON is not an array.
 * @throws JsonException If the JSON is invalid.
 */
function loadJsonFile(string $path): array
{
    return (new JsonFileImporter())->import($path);
}
