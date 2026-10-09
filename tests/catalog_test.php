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
$monsters = new JsonMonsterProvider($store);

$weaponPage = $weapons->getWeapons(new WeaponQuery(page: 2, pageSize: 10));
expectTrue(count($weaponPage->items) === 10, 'La seconda pagina delle armi deve contenere 10 elementi.');
expectTrue($weaponPage->totalItems === 38, 'Il totale delle armi deve essere 38.');
expectTrue($weaponPage->totalPages() === 4, 'Le armi devono occupare 4 pagine da 10.');

$spellSearch = $spells->getSpells(new SpellQuery(search: 'palla di fuoco', pageSize: 24));
expectTrue($spellSearch->totalItems === 2, 'La ricerca degli incantesimi deve trovare 2 risultati.');
expectTrue($spellSearch->items[0]['name'] === 'Palla di fuoco', 'La ricerca deve restituire Palla di fuoco.');

$monsterSearch = $monsters->getMonsters(new MonsterQuery(search: 'drago', pageSize: 24));
expectTrue($monsterSearch->totalItems > 0, 'La ricerca dei mostri deve trovare almeno un risultato.');
expectTrue(count($monsterSearch->items) <= 24, 'La ricerca dei mostri deve rispettare la paginazione.');

echo "Catalog tests passed.\n";
