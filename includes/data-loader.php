<?php

declare(strict_types=1);

require_once __DIR__ . '/import/DataImporterInterface.php';
require_once __DIR__ . '/import/JsonFileImporter.php';
require_once __DIR__ . '/catalog/DataCatalog.php';
require_once __DIR__ . '/catalog/shared/PageResult.php';
require_once __DIR__ . '/catalog/shared/JsonDatasetStore.php';
require_once __DIR__ . '/catalog/shared/CatalogSummaryInterface.php';
require_once __DIR__ . '/catalog/shared/JsonCatalogSummary.php';
require_once __DIR__ . '/catalog/objects/weapons/WeaponQuery.php';
require_once __DIR__ . '/catalog/objects/weapons/WeaponProviderInterface.php';
require_once __DIR__ . '/catalog/objects/weapons/JsonWeaponProvider.php';
require_once __DIR__ . '/catalog/objects/armor/ArmorQuery.php';
require_once __DIR__ . '/catalog/objects/armor/ArmorProviderInterface.php';
require_once __DIR__ . '/catalog/objects/armor/JsonArmorProvider.php';
require_once __DIR__ . '/catalog/objects/ammunition/AmmunitionQuery.php';
require_once __DIR__ . '/catalog/objects/ammunition/AmmunitionProviderInterface.php';
require_once __DIR__ . '/catalog/objects/ammunition/JsonAmmunitionProvider.php';
require_once __DIR__ . '/catalog/objects/tools/ToolsQuery.php';
require_once __DIR__ . '/catalog/objects/tools/ToolsProviderInterface.php';
require_once __DIR__ . '/catalog/objects/tools/JsonToolsProvider.php';
require_once __DIR__ . '/catalog/objects/adventuring-gear/AdventuringGearQuery.php';
require_once __DIR__ . '/catalog/objects/adventuring-gear/AdventuringGearProviderInterface.php';
require_once __DIR__ . '/catalog/objects/adventuring-gear/JsonAdventuringGearProvider.php';
require_once __DIR__ . '/catalog/spells/SpellQuery.php';
require_once __DIR__ . '/catalog/spells/SpellProviderInterface.php';
require_once __DIR__ . '/catalog/spells/JsonSpellProvider.php';
require_once __DIR__ . '/catalog/monsters/MonsterQuery.php';
require_once __DIR__ . '/catalog/monsters/MonsterProviderInterface.php';
require_once __DIR__ . '/catalog/monsters/JsonMonsterProvider.php';
require_once __DIR__ . '/presentation/FrontendController.php';
require_once __DIR__ . '/presentation/SearchIndexBuilder.php';
require_once __DIR__ . '/presentation/SearchIndexProviderInterface.php';
require_once __DIR__ . '/presentation/JsonSearchIndexProvider.php';

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
