<?php
/*
 * Italian SRD Data JSON Archive.
 * 
 * Quest'opera include materiale tratto dalla versione italiana del System Reference Document ("SRD") @SRDVersion
 *  di Wizards of the Coast LLC, disponibile all'indirizzo https://www.dndbeyond.com/srd. Il SRD è concesso in 
 * licenza ai sensi della licenza di attribuzione 4.0 Internazionale di Creative Commons, 
 * disponibile all'indirizzo https://creativecommons.org/licenses/by/4.0/legalcode.
 * 
 * I dati riportati sono compatibili con la v5.5 (2024).
 * 
 * Version: 0.0.7
 * SRDVersion: 5.2.1
 * Author: Andrea Gaspari
 * Author URI: https://andreagaspari.dev
 * License: CC BY 4.0
 * License URI: https://creativecommons.org/licenses/by/4.0/
 */

declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/database/DatabaseConnection.php';

$route = $_GET['route'] ?? 'home';
DatabaseConnection::loadEnvironment();
$dataSource = strtoupper(trim((string) (DatabaseConnection::getEnvironment('DATA_SOURCE') ?: 'JSON')));
if (!in_array($dataSource, ['JSON', 'MYSQL'], true)) {
    throw new RuntimeException('DATA_SOURCE deve essere JSON oppure MYSQL.');
}
$datasetStore = new JsonDatasetStore(new JsonFileImporter(), __DIR__ . '/data');
$summary = new JsonCatalogSummary($datasetStore);
$database = $dataSource === 'MYSQL'
    ? DatabaseConnection::createFromEnvironment()
    : null;
$mysqlProvider = $dataSource === 'MYSQL'
    ? new MySqlCatalogProvider($database)
    : null;
$rulesCatalog = new RulesCatalog($datasetStore, $database);
$catalog = new DataCatalog(
    store: $datasetStore,
    weaponProvider: $mysqlProvider ?? new JsonWeaponProvider($datasetStore),
    armorProvider: $mysqlProvider ?? new JsonArmorProvider($datasetStore),
    ammunitionProvider: $mysqlProvider ?? new JsonAmmunitionProvider($datasetStore),
    toolsProvider: $mysqlProvider ?? new JsonToolsProvider($datasetStore),
    adventuringGearProvider: $mysqlProvider ?? new JsonAdventuringGearProvider($datasetStore),
    spellProvider: $mysqlProvider ?? new JsonSpellProvider($datasetStore),
    monsterProvider: $mysqlProvider ?? new JsonMonsterProvider($datasetStore)
);
$spellsCount = $summary->count('spells');
$monsterCount = $summary->count('monsters');
$objectCount = $summary->count('objects');
$rulesCount = count($rulesCatalog->document()['items'] ?? []);
$spellCategories = $summary->categories('spells');
$spellClassCategories = $summary->categories('spell-classes');
$monsterCategories = $summary->categories('monsters');
$objectCategories = $summary->categories('objects');
$frontendController = new FrontendController($catalog, $rulesCatalog, __DIR__ . '/templates');
$page = $frontendController->resolve(
    route: is_string($route) ? $route : 'home',
    request: $_GET
);
$pageTitle = $page['pageTitle'];
$contentView = $page['contentView'];
extract($page['variables'], EXTR_SKIP);

require __DIR__ . '/templates/layout.php';
