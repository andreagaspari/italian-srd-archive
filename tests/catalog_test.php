<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/bootstrap.php';

function expectTrue(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$store = new JsonDatasetStore(new JsonFileImporter(), dirname(__DIR__) . '/data');
$weapons = new JsonWeaponProvider($store);
$spells = new JsonSpellProvider($store);
$feats = new JsonFeatProvider($store);
$monsters = new JsonMonsterProvider($store);
$rules = new RulesCatalog($store);

$dardoExport = buildObjectExportData('Dardo', '5 mr', '0,125 kg');
expectTrue($dardoExport['valore'] === 5, 'Il valore esportato deve mantenere il numero originale del costo.');
expectTrue($dardoExport['currency'] === 'bronze', 'I mr devono essere esportati come valuta bronze.');

$searchIndex = (new SearchIndexBuilder())->build([
    'objectData' => $store->get('weapons'),
    'armorData' => $store->get('armor'),
    'ammunitionData' => $store->get('ammunition'),
    'toolsData' => $store->get('tools'),
    'adventuringGearData' => $store->get('adventuring-gear'),
    'spellsData' => $store->get('spells'),
    'featsData' => $store->get('feats'),
    'monstersData' => $store->get('monsters'),
    'rulesData' => $store->get('rules')
]);
$dardoSearchResult = array_values(array_filter(
    $searchIndex,
    static fn (array $item): bool => $item['title'] === 'Dardo'
))[0] ?? null;
expectTrue(is_array($dardoSearchResult) && str_contains($dardoSearchResult['url'], 'focus=dardo'), 'La ricerca globale deve indicare l’elemento da evidenziare.');
expectTrue(is_array($dardoSearchResult) && $dardoSearchResult['type'] === 'Arma', 'La ricerca globale deve indicare il tipo del risultato.');
expectTrue(is_array($dardoSearchResult) && $dardoSearchResult['tagTitles'][0] === 'Modalità di combattimento', 'I tag della ricerca globale devono avere un significato esplicito.');

$weaponPage = $weapons->getWeapons(new WeaponQuery(page: 2, pageSize: 10));
expectTrue(count($weaponPage->items) === 10, 'La seconda pagina delle armi deve contenere 10 elementi.');
expectTrue($weaponPage->totalItems === 38, 'Il totale delle armi deve essere 38.');
expectTrue($weaponPage->totalPages() === 4, 'Le armi devono occupare 4 pagine da 10.');

$spellSearch = $spells->getSpells(new SpellQuery(search: 'palla di fuoco', pageSize: 24));
expectTrue($spellSearch->totalItems === 2, 'La ricerca degli incantesimi deve trovare 2 risultati.');
expectTrue($spellSearch->items[0]['name'] === 'Palla di fuoco', 'La ricerca deve restituire Palla di fuoco.');

$featSearch = $feats->getFeats(new FeatQuery(subcategory: 'Origini', search: 'abile', pageSize: 24));
expectTrue($featSearch->totalItems === 1, 'La ricerca dei talenti deve filtrare per categoria e testo.');
$featSearchResult = array_values(array_filter(
    $searchIndex,
    static fn (array $item): bool => $item['title'] === 'Abile'
))[0] ?? null;
expectTrue(is_array($featSearchResult) && $featSearchResult['section'] === 'Talenti', 'La ricerca globale deve includere i talenti.');

$monsterSearch = $monsters->getMonsters(new MonsterQuery(search: 'drago', pageSize: 24));
expectTrue($monsterSearch->totalItems > 0, 'La ricerca dei mostri deve trovare almeno un risultato.');
expectTrue(count($monsterSearch->items) <= 24, 'La ricerca dei mostri deve rispettare la paginazione.');

$conditionRules = $rules->getRules('condizione', 'accecato', 1, 24);
expectTrue($conditionRules->totalItems > 0, 'Il glossario deve trovare la regola Accecato.');
expectTrue($conditionRules->items[0]['name'] === 'Accecato', 'La regola deve rimuovere il descrittore dal nome.');
expectTrue($conditionRules->items[0]['category'] === 'condizione', 'Il descrittore deve diventare la categoria.');
expectTrue(count($rules->document()['abbreviations']) > 0, 'Il glossario deve includere le abbreviazioni.');

echo "Catalog tests passed.\n";
