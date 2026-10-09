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
 * Version: 0.0.1
 * SRDVersion: 5.2.1
 * Author: Andrea Gaspari
 * Author URI: https://andreagaspari.dev
 * License: CC BY 4.0
 * License URI: https://creativecommons.org/licenses/by/4.0/
 */

declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';

$route = $_GET['route'] ?? 'home';
$datasetStore = new JsonDatasetStore(new JsonFileImporter(), __DIR__ . '/data');
$summary = new JsonCatalogSummary($datasetStore);
$catalog = new DataCatalog(
    store: $datasetStore,
    weaponProvider: new JsonWeaponProvider($datasetStore),
    armorProvider: new JsonArmorProvider($datasetStore),
    ammunitionProvider: new JsonAmmunitionProvider($datasetStore),
    toolsProvider: new JsonToolsProvider($datasetStore),
    adventuringGearProvider: new JsonAdventuringGearProvider($datasetStore),
    spellProvider: new JsonSpellProvider($datasetStore),
    monsterProvider: new JsonMonsterProvider($datasetStore)
);
$spellsCount = $summary->count('spells');
$monsterCount = $summary->count('monsters');
$objectCount = $summary->count('objects');
$spellCategories = $summary->categories('spells');
$spellClassCategories = $summary->categories('spell-classes');
$monsterCategories = $summary->categories('monsters');
$objectCategories = $summary->categories('objects');
$frontendController = new FrontendController($catalog, __DIR__ . '/templates');
$page = $frontendController->resolve(
    route: is_string($route) ? $route : 'home',
    request: $_GET
);
$pageTitle = $page['pageTitle'];
$contentView = $page['contentView'];
extract($page['variables'], EXTR_SKIP);

require __DIR__ . '/templates/layout.php';
