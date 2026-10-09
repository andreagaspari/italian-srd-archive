<?php

declare(strict_types=1);

function loadJsonFile(string $path): array
{
    $contents = file_get_contents($path);
    if ($contents === false) {
        throw new RuntimeException("Impossibile leggere il file JSON: {$path}");
    }

    $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
    if (!is_array($data)) {
        throw new UnexpectedValueException("Il file JSON non contiene un oggetto valido: {$path}");
    }

    return $data;
}
