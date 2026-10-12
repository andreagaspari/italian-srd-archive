<?php

declare(strict_types=1);

/**
 * Imports all JSON catalog datasets into MySQL using idempotent upserts.
 */

require __DIR__ . '/../includes/database/DatabaseConnection.php';
require __DIR__ . '/../includes/import/DataImporterInterface.php';
require __DIR__ . '/../includes/import/JsonFileImporter.php';

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

/**
 * Extracts a concise source version from dataset metadata.
 *
 * @param array<string, mixed> $document Imported dataset document.
 *
 * @return string Source version or `unknown` when unavailable.
 */
function catalogSourceVersion(array $document): string
{
    $source = $document['source'] ?? [];
    $sourceText = is_array($source)
        ? implode(' ', array_map(static fn (mixed $value): string => is_scalar($value) ? (string) $value : '', $source))
        : '';

    return preg_match('/\b\d+\.\d+\.\d+\b/', $sourceText, $matches) === 1
        ? $matches[0]
        : 'unknown';
}

/**
 * Flattens nested catalog values into searchable text.
 *
 * @param mixed $value Catalog value.
 *
 * @return string Searchable text.
 */
function catalogSearchText(mixed $value): string
{
    if (is_scalar($value)) {
        return trim((string) $value);
    }

    if (!is_array($value)) {
        return '';
    }

    $parts = [];
    foreach ($value as $nestedValue) {
        $text = catalogSearchText($nestedValue);
        if ($text !== '') {
            $parts[] = $text;
        }
    }

    return implode(' ', $parts);
}

/**
 * Reads a required scalar field from a catalog item.
 *
 * @param array<string, mixed> $item Catalog item.
 * @param string $field Field name.
 * @param string $dataset Dataset name for error messages.
 *
 * @return string Normalized field value.
 *
 * @throws UnexpectedValueException If the field is missing or not scalar.
 */
function catalogString(array $item, string $field, string $dataset): string
{
    if (!array_key_exists($field, $item) || !is_scalar($item[$field])) {
        throw new UnexpectedValueException("Campo {$field} non valido nel dataset {$dataset}.");
    }

    return (string) $item[$field];
}

/**
 * Reads an optional scalar field from a catalog item.
 *
 * @param array<string, mixed> $item Catalog item.
 * @param string $field Field name.
 * @param string $dataset Dataset name for error messages.
 *
 * @return string|null Normalized field value or null when absent.
 *
 * @throws UnexpectedValueException If the field is present but not scalar.
 */
function catalogNullableString(array $item, string $field, string $dataset): ?string
{
    if (!array_key_exists($field, $item) || $item[$field] === null) {
        return null;
    }
    if (!is_scalar($item[$field])) {
        throw new UnexpectedValueException("Campo {$field} non valido nel dataset {$dataset}.");
    }

    return (string) $item[$field];
}

/**
 * Reads a boolean catalog field.
 *
 * @param array<string, mixed> $item Catalog item.
 * @param string $field Field name.
 * @param string $dataset Dataset name for error messages.
 *
 * @return bool Normalized boolean value.
 *
 * @throws UnexpectedValueException If the field is missing or not boolean.
 */
function catalogBoolean(array $item, string $field, string $dataset): bool
{
    if (!array_key_exists($field, $item) || !is_bool($item[$field])) {
        throw new UnexpectedValueException("Campo {$field} non valido nel dataset {$dataset}.");
    }

    return $item[$field];
}

try {
    $database = DatabaseConnection::createFromEnvironment();
    $importer = new JsonFileImporter();
    $dataDirectory = __DIR__ . '/../data';
    $files = glob($dataDirectory . '/*.json');
    if ($files === false || $files === []) {
        throw new RuntimeException('Nessun dataset JSON trovato.');
    }
    sort($files, SORT_STRING);

    $baseInsert = $database->prepare(
        'INSERT INTO catalog_items
            (dataset, item_id, name, search_text, payload, source_version, created_at, updated_at)
         VALUES
            (:dataset, :item_id, :name, :search_text, :payload, :source_version, NOW(), NOW())
         ON DUPLICATE KEY UPDATE
            name = VALUES(name),
            search_text = VALUES(search_text),
            payload = VALUES(payload),
            source_version = VALUES(source_version),
            updated_at = NOW()'
    );
    $baseId = $database->prepare(
        'SELECT id FROM catalog_items WHERE dataset = :dataset AND item_id = :item_id'
    );

    $specializedStatements = [
        'spells' => $database->prepare(
            'INSERT INTO spell_items
                (catalog_item_id, level, school, casting_time, range_value, duration, concentration)
             VALUES
                (:catalog_item_id, :level, :school, :casting_time, :range_value, :duration, :concentration)
             ON DUPLICATE KEY UPDATE
                level = VALUES(level), school = VALUES(school), casting_time = VALUES(casting_time),
                range_value = VALUES(range_value), duration = VALUES(duration),
                concentration = VALUES(concentration)'
        ),
        'monsters' => $database->prepare(
            'INSERT INTO monster_items
                (catalog_item_id, monster_type, size, alignment, challenge_rating)
             VALUES
                (:catalog_item_id, :monster_type, :size, :alignment, :challenge_rating)
             ON DUPLICATE KEY UPDATE
                monster_type = VALUES(monster_type), size = VALUES(size),
                alignment = VALUES(alignment), challenge_rating = VALUES(challenge_rating)'
        ),
        'weapons' => $database->prepare(
            'INSERT INTO weapon_items
                (catalog_item_id, proficiency, mode, subcategory, damage, mastery, weight_text, cost_text)
             VALUES
                (:catalog_item_id, :proficiency, :mode, :subcategory, :damage, :mastery, :weight_text, :cost_text)
             ON DUPLICATE KEY UPDATE
                proficiency = VALUES(proficiency), mode = VALUES(mode), subcategory = VALUES(subcategory),
                damage = VALUES(damage), mastery = VALUES(mastery),
                weight_text = VALUES(weight_text), cost_text = VALUES(cost_text)'
        ),
        'armor' => $database->prepare(
            'INSERT INTO armor_items
                (catalog_item_id, armor_type, armor_class, strength_requirement,
                 stealth_disadvantage, weight_text, cost_text)
             VALUES
                (:catalog_item_id, :armor_type, :armor_class, :strength_requirement,
                 :stealth_disadvantage, :weight_text, :cost_text)
             ON DUPLICATE KEY UPDATE
                armor_type = VALUES(armor_type), armor_class = VALUES(armor_class),
                strength_requirement = VALUES(strength_requirement),
                stealth_disadvantage = VALUES(stealth_disadvantage),
                weight_text = VALUES(weight_text), cost_text = VALUES(cost_text)'
        ),
        'ammunition' => $database->prepare(
            'INSERT INTO ammunition_items
                (catalog_item_id, quantity, container, weight_text, cost_text)
             VALUES
                (:catalog_item_id, :quantity, :container, :weight_text, :cost_text)
             ON DUPLICATE KEY UPDATE
                quantity = VALUES(quantity), container = VALUES(container),
                weight_text = VALUES(weight_text), cost_text = VALUES(cost_text)'
        ),
        'tools' => $database->prepare(
            'INSERT INTO tool_items
                (catalog_item_id, tool_type, ability, weight_text, cost_text)
             VALUES
                (:catalog_item_id, :tool_type, :ability, :weight_text, :cost_text)
             ON DUPLICATE KEY UPDATE
                tool_type = VALUES(tool_type), ability = VALUES(ability),
                weight_text = VALUES(weight_text), cost_text = VALUES(cost_text)'
        ),
        'adventuring-gear' => $database->prepare(
            'INSERT INTO adventuring_gear_items
                (catalog_item_id, category, weight_text, cost_text)
             VALUES
                (:catalog_item_id, :category, :weight_text, :cost_text)
             ON DUPLICATE KEY UPDATE
                category = VALUES(category), weight_text = VALUES(weight_text),
                cost_text = VALUES(cost_text)'
        ),
    ];
    $deleteSpellClasses = $database->prepare(
        'DELETE FROM spell_classes WHERE catalog_item_id = :catalog_item_id'
    );
    $insertSpellClass = $database->prepare(
        'INSERT INTO spell_classes (catalog_item_id, class_name) VALUES (:catalog_item_id, :class_name)'
    );

    $totalItems = 0;
    foreach ($files as $file) {
        $dataset = pathinfo($file, PATHINFO_FILENAME);
        $document = $importer->import($file);
        $items = $document['items'] ?? null;
        if (!is_array($items) || !isset($specializedStatements[$dataset])) {
            throw new UnexpectedValueException("Dataset non supportato o senza items: {$dataset}");
        }

        $sourceVersion = catalogSourceVersion($document);
        $datasetItems = 0;
        $database->beginTransaction();
        try {
            foreach ($items as $item) {
                if (!is_array($item)) {
                    throw new UnexpectedValueException("Elemento non valido nel dataset {$dataset}.");
                }

                $itemId = catalogString($item, 'id', $dataset);
                $name = catalogString($item, 'name', $dataset);
                $itemKey = $dataset . ':' . $itemId;
                $baseInsert->execute([
                    'dataset' => $dataset,
                    'item_id' => $itemId,
                    'name' => $name,
                    'search_text' => catalogSearchText($item),
                    'payload' => json_encode($item, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR),
                    'source_version' => $sourceVersion,
                ]);
                $baseId->execute(['dataset' => $dataset, 'item_id' => $itemId]);
                $catalogItemId = $baseId->fetchColumn();
                if ($catalogItemId === false) {
                    throw new RuntimeException("Impossibile recuperare l'ID interno per {$itemKey}.");
                }

                $parameters = match ($dataset) {
                    'spells' => [
                        'catalog_item_id' => $catalogItemId,
                        'level' => (int) catalogString($item, 'level', $dataset),
                        'school' => catalogString($item, 'school', $dataset),
                        'casting_time' => catalogString($item, 'casting_time', $dataset),
                        'range_value' => catalogString($item, 'range', $dataset),
                        'duration' => catalogString($item, 'duration', $dataset),
                        'concentration' => (int) catalogBoolean($item, 'concentration', $dataset),
                    ],
                    'monsters' => [
                        'catalog_item_id' => $catalogItemId,
                        'monster_type' => catalogString($item, 'type', $dataset),
                        'size' => catalogString($item, 'size', $dataset),
                        'alignment' => catalogString($item, 'alignment', $dataset),
                        'challenge_rating' => catalogString($item, 'challenge_rating', $dataset),
                    ],
                    'weapons' => [
                        'catalog_item_id' => $catalogItemId,
                        'proficiency' => catalogString($item, 'proficiency', $dataset),
                        'mode' => catalogString($item, 'mode', $dataset),
                        'subcategory' => catalogString($item, 'subcategory', $dataset),
                        'damage' => catalogString($item, 'damage', $dataset),
                        'mastery' => catalogString($item, 'mastery', $dataset),
                        'weight_text' => catalogString($item, 'weight', $dataset),
                        'cost_text' => catalogString($item, 'cost', $dataset),
                    ],
                    'armor' => [
                        'catalog_item_id' => $catalogItemId,
                        'armor_type' => catalogString($item, 'armor_type', $dataset),
                        'armor_class' => catalogString($item, 'armor_class', $dataset),
                        'strength_requirement' => catalogNullableString($item, 'strength_requirement', $dataset),
                        'stealth_disadvantage' => (int) catalogBoolean($item, 'stealth_disadvantage', $dataset),
                        'weight_text' => catalogString($item, 'weight', $dataset),
                        'cost_text' => catalogString($item, 'cost', $dataset),
                    ],
                    'ammunition' => [
                        'catalog_item_id' => $catalogItemId,
                        'quantity' => catalogString($item, 'quantity', $dataset),
                        'container' => catalogString($item, 'container', $dataset),
                        'weight_text' => catalogString($item, 'weight', $dataset),
                        'cost_text' => catalogString($item, 'cost', $dataset),
                    ],
                    'tools' => [
                        'catalog_item_id' => $catalogItemId,
                        'tool_type' => catalogString($item, 'tool_type', $dataset),
                        'ability' => catalogString($item, 'ability', $dataset),
                        'weight_text' => catalogString($item, 'weight', $dataset),
                        'cost_text' => catalogString($item, 'cost', $dataset),
                    ],
                    'adventuring-gear' => [
                        'catalog_item_id' => $catalogItemId,
                        'category' => catalogString($item, 'category', $dataset),
                        'weight_text' => catalogString($item, 'weight', $dataset),
                        'cost_text' => catalogString($item, 'cost', $dataset),
                    ],
                };
                $specializedStatements[$dataset]->execute($parameters);

                if ($dataset === 'spells') {
                    $deleteSpellClasses->execute(['catalog_item_id' => $catalogItemId]);
                    if (!is_array($item['classes'] ?? null)) {
                        throw new UnexpectedValueException("Campo classes non valido nel dataset {$dataset}.");
                    }
                    foreach ($item['classes'] as $className) {
                        if (!is_string($className) || trim($className) === '') {
                            throw new UnexpectedValueException("Classe non valida nel dataset {$dataset}.");
                        }
                        $insertSpellClass->execute([
                            'catalog_item_id' => $catalogItemId,
                            'class_name' => $className,
                        ]);
                    }
                }
                $datasetItems++;
            }
            $database->commit();
        } catch (Throwable $exception) {
            $database->rollBack();
            throw $exception;
        }

        $totalItems += $datasetItems;
        printf("%s: %d elementi importati\n", $dataset, $datasetItems);
    }

    printf("Import completato: %d elementi totali.\n", $totalItems);
} catch (Throwable $exception) {
    fwrite(STDERR, $exception->getMessage() . PHP_EOL);
    exit(1);
}
