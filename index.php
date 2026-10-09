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
$catalog = new DataCatalog(new JsonFileImporter(), __DIR__ . '/data');
$objectData = $catalog->weapons();
$armorData = $catalog->armor();
$ammunitionData = $catalog->ammunition();
$toolsData = $catalog->tools();
$adventuringGearData = $catalog->adventuringGear();
$spellsData = $catalog->spells();
$monstersData = $catalog->monsters();
$monsterCount = count($monstersData['items'] ?? []);
$objectCount = count($objectData['items'] ?? []) + count($armorData['items'] ?? []) + count($ammunitionData['items'] ?? []) + count($toolsData['items'] ?? []) + count($adventuringGearData['items'] ?? []);

$flattenSearchValues = static function (mixed $value) use (&$flattenSearchValues): array {
    if (is_array($value)) {
        $values = [];
        foreach ($value as $nestedValue) {
            $values = array_merge($values, $flattenSearchValues($nestedValue));
        }
        return $values;
    }
    return is_scalar($value) ? [(string) $value] : [];
};

$globalSearchIndex = [
    ['section' => 'Oggetti', 'title' => 'Oggetti', 'description' => 'Armi, armature, munizioni, strumenti ed equipaggiamento.', 'tags' => [], 'url' => appUrl('oggetti')],
    ['section' => 'Armi', 'title' => 'Armi', 'description' => 'Armi da mischia e a distanza, semplici e da guerra.', 'tags' => ['Categoria'], 'url' => appUrl('oggetti/armi')],
    ['section' => 'Armi', 'title' => 'Armi da mischia semplici', 'description' => 'Categoria di armi.', 'tags' => ['Categoria', 'Mischia', 'Semplice'], 'url' => appUrl('oggetti/armi?categoria=items-weapons-simple-melee')],
    ['section' => 'Armi', 'title' => 'Armi a distanza semplici', 'description' => 'Categoria di armi.', 'tags' => ['Categoria', 'A distanza', 'Semplice'], 'url' => appUrl('oggetti/armi?categoria=items-weapons-simple-ranged')],
    ['section' => 'Armi', 'title' => 'Armi da mischia da guerra', 'description' => 'Categoria di armi.', 'tags' => ['Categoria', 'Mischia', 'Da guerra'], 'url' => appUrl('oggetti/armi?categoria=items-weapons-martial-melee')],
    ['section' => 'Armi', 'title' => 'Armi a distanza da guerra', 'description' => 'Categoria di armi.', 'tags' => ['Categoria', 'A distanza', 'Da guerra'], 'url' => appUrl('oggetti/armi?categoria=items-weapons-martial-ranged')],
    ['section' => 'Armature', 'title' => 'Armature', 'description' => 'Armature leggere, medie, pesanti e scudi.', 'tags' => ['Categoria'], 'url' => appUrl('oggetti/armature')],
    ['section' => 'Munizioni', 'title' => 'Munizioni', 'description' => 'Munizioni per archi, balestre, fionde e armi da fuoco.', 'tags' => ['Categoria'], 'url' => appUrl('oggetti/munizioni')],
    ['section' => 'Strumenti', 'title' => 'Strumenti', 'description' => 'Strumenti da artigiano, musicali, giochi e altri strumenti.', 'tags' => ['Categoria'], 'url' => appUrl('oggetti/strumenti')],
    ['section' => 'Equipaggiamento', 'title' => 'Equipaggiamento', 'description' => 'Oggetti, contenitori, consumabili e attrezzatura da avventura.', 'tags' => ['Categoria'], 'url' => appUrl('oggetti/avventura')],
    ['section' => 'Incantesimi', 'title' => 'Incantesimi', 'description' => 'Trucchetti e incantesimi dal 1° al 9° livello.', 'tags' => ['Categoria'], 'url' => appUrl('incantesimi')],
    ['section' => 'Mostri', 'title' => 'Mostri', 'description' => 'Blocchi statistiche dei mostri dello SRD 5.2.1.', 'tags' => ['Categoria'], 'url' => appUrl('mostri')]
];

$appendSearchItems = static function (array &$index, array $items, string $section, string $url, callable $titleFormatter, callable $tagFormatter): void {
    foreach ($items as $item) {
        $title = $titleFormatter($item);
        $tags = array_values(array_filter($tagFormatter($item)));
        $values = $GLOBALS['flattenSearchValues']($item);
        $descriptionValues = $GLOBALS['flattenSearchValues'](array_diff_key($item, array_flip([
            'id', 'name', 'mode', 'proficiency', 'subcategory', 'armor_type', 'tool_type', 'category', 'container'
        ])));
        $descriptionValues = array_values(array_unique(array_filter($descriptionValues, static fn (string $value): bool => !in_array($value, $tags, true))));
        $index[] = [
            'section' => $section,
            'title' => $title,
            'description' => implode(' · ', array_slice($descriptionValues, 0, 3)),
            'tags' => $tags,
            'search' => implode(' ', $values),
            'url' => $url
        ];
    }
};

$appendSearchItems($globalSearchIndex, $objectData['items'] ?? [], 'Armi', appUrl('oggetti/armi'), static fn (array $item): string => $item['name'], static fn (array $item): array => [$item['mode'], $item['proficiency']]);
$appendSearchItems($globalSearchIndex, $armorData['items'] ?? [], 'Armature', appUrl('oggetti/armature'), static fn (array $item): string => $item['name'], static fn (array $item): array => [$item['armor_type']]);
$appendSearchItems($globalSearchIndex, $ammunitionData['items'] ?? [], 'Munizioni', appUrl('oggetti/munizioni'), static fn (array $item): string => $item['name'], static fn (array $item): array => [$item['container']]);
$appendSearchItems($globalSearchIndex, $toolsData['items'] ?? [], 'Strumenti', appUrl('oggetti/strumenti'), static fn (array $item): string => $item['name'], static fn (array $item): array => [$item['tool_type']]);
$appendSearchItems($globalSearchIndex, $adventuringGearData['items'] ?? [], 'Equipaggiamento', appUrl('oggetti/avventura'), static fn (array $item): string => $item['name'], static fn (array $item): array => [$item['category']]);
$appendSearchItems($globalSearchIndex, $spellsData['items'] ?? [], 'Incantesimi', appUrl('incantesimi'), static fn (array $item): string => $item['name'], static fn (array $item): array => [
    $item['level'] === 0 ? 'Trucchetto' : 'Livello ' . $item['level'],
    $item['school'],
    ...$item['classes']
]);
$appendSearchItems($globalSearchIndex, $monstersData['items'] ?? [], 'Mostri', appUrl('mostri'), static fn (array $item): string => $item['name'], static fn (array $item): array => [$item['type'], $item['size'], $item['challenge_rating']]);

switch ($route) {
    case 'oggetti/armi':
        $categoryLabels = [
            'items-weapons' => 'Tutte le armi',
            'items-weapons-simple-melee' => 'Armi da mischia semplici',
            'items-weapons-simple-ranged' => 'Armi a distanza semplici',
            'items-weapons-martial-melee' => 'Armi da mischia da guerra',
            'items-weapons-martial-ranged' => 'Armi a distanza da guerra'
        ];
        $selectedCategory = $_GET['categoria'] ?? 'items-weapons';
        if (!array_key_exists($selectedCategory, $categoryLabels)) {
            $selectedCategory = 'items-weapons';
        }

        $weapons = $objectData['items'] ?? [];
        $filteredWeapons = array_values(array_filter(
            $weapons,
            static function (array $weapon) use ($selectedCategory): bool {
                return match ($selectedCategory) {
                    'items-weapons' => true,
                    'items-weapons-simple-melee' => $weapon['proficiency'] === 'Semplice' && $weapon['mode'] === 'Mischia',
                    'items-weapons-simple-ranged' => $weapon['proficiency'] === 'Semplice' && $weapon['mode'] === 'A distanza',
                    'items-weapons-martial-melee' => $weapon['proficiency'] === 'Da guerra' && $weapon['mode'] === 'Mischia',
                    'items-weapons-martial-ranged' => $weapon['proficiency'] === 'Da guerra' && $weapon['mode'] === 'A distanza',
                    'items-armor' => false,
                    default => false
                };
            }
        ));

        $pageTitle = $categoryLabels[$selectedCategory];
        $contentView = __DIR__ . '/templates/objects/weapons.php';
        break;

    case 'oggetti/armature':
        $categoryLabels = [
            'armor' => 'Tutte le armature',
            'armor-light' => 'Armature leggere',
            'armor-medium' => 'Armature medie',
            'armor-heavy' => 'Armature pesanti',
            'armor-shields' => 'Scudi'
        ];
        $selectedCategory = $_GET['categoria'] ?? 'armor';
        if (!array_key_exists($selectedCategory, $categoryLabels)) {
            $selectedCategory = 'armor';
        }

        $armor = $armorData['items'] ?? [];
        $filteredArmor = array_values(array_filter(
            $armor,
            static function (array $item) use ($selectedCategory): bool {
                return match ($selectedCategory) {
                    'armor' => true,
                    'armor-light' => $item['armor_type'] === 'leggera',
                    'armor-medium' => $item['armor_type'] === 'media',
                    'armor-heavy' => $item['armor_type'] === 'pesante',
                    'armor-shields' => $item['armor_type'] === 'scudo',
                    default => false
                };
            }
        ));

        $pageTitle = $categoryLabels[$selectedCategory];
        $contentView = __DIR__ . '/templates/objects/armor.php';
        break;

    case 'oggetti/munizioni':
        $pageTitle = 'Munizioni';
        $ammunition = $ammunitionData['items'] ?? [];
        $contentView = __DIR__ . '/templates/objects/ammunition.php';
        break;

    case 'oggetti/strumenti':
        $categoryLabels = [
            'tools' => 'Tutti gli strumenti',
            'artigiano' => 'Strumenti da artigiano',
            'giochi' => 'Giochi',
            'musicali' => 'Strumenti musicali',
            'altri' => 'Altri strumenti'
        ];
        $selectedCategory = $_GET['categoria'] ?? 'tools';
        if (!array_key_exists($selectedCategory, $categoryLabels)) {
            $selectedCategory = 'tools';
        }

        $tools = array_values(array_filter(
            $toolsData['items'] ?? [],
            static fn (array $tool): bool => $selectedCategory === 'tools' || $tool['tool_type'] === $selectedCategory
        ));
        $pageTitle = $categoryLabels[$selectedCategory];
        $contentView = __DIR__ . '/templates/objects/tools.php';
        break;

    case 'oggetti/avventura':
        $categoryLabels = [
            'gear' => 'Tutto l’equipaggiamento',
            'avventura' => 'Avventura',
            'consumabili' => 'Consumabili',
            'contenitori' => 'Contenitori',
            'illuminazione' => 'Illuminazione'
        ];
        $selectedCategory = $_GET['categoria'] ?? 'gear';
        if (!array_key_exists($selectedCategory, $categoryLabels)) {
            $selectedCategory = 'gear';
        }

        $adventuringGear = array_values(array_filter(
            $adventuringGearData['items'] ?? [],
            static fn (array $item): bool => $selectedCategory === 'gear' || $item['category'] === $selectedCategory
        ));
        $pageTitle = $categoryLabels[$selectedCategory];
        $contentView = __DIR__ . '/templates/objects/adventuring-gear.php';
        break;

    case 'oggetti':
        $pageTitle = 'Oggetti';
        $contentView = __DIR__ . '/templates/objects/index.php';
        break;

    case 'incantesimi':
        $levelLabels = [
            'all' => 'Tutti gli incantesimi',
            '0' => 'Trucchetti',
            '1' => '1° livello',
            '2' => '2° livello',
            '3' => '3° livello',
            '4' => '4° livello',
            '5' => '5° livello',
            '6' => '6° livello',
            '7' => '7° livello',
            '8' => '8° livello',
            '9' => '9° livello'
        ];
        $selectedLevel = (string) ($_GET['livello'] ?? 'all');
        if (!array_key_exists($selectedLevel, $levelLabels)) {
            $selectedLevel = 'all';
        }
        $schoolLabels = ['' => 'Tutte le scuole'];
        foreach ($spellsData['items'] ?? [] as $spell) {
            $schoolLabels[$spell['school']] = $spell['school'];
        }
        ksort($schoolLabels);
        $selectedSchool = (string) ($_GET['scuola'] ?? '');
        if (!array_key_exists($selectedSchool, $schoolLabels)) {
            $selectedSchool = '';
        }
        $spellClasses = [];
        foreach ($spellsData['items'] ?? [] as $spell) {
            foreach ($spell['classes'] as $class) {
                $spellClasses[$class] = $class;
            }
        }
        ksort($spellClasses);
        $selectedClass = (string) ($_GET['classe'] ?? '');
        if ($selectedClass !== '' && !array_key_exists($selectedClass, $spellClasses)) {
            $selectedClass = '';
        }
        $spells = array_values(array_filter(
            $spellsData['items'] ?? [],
            static fn (array $spell): bool =>
                ($selectedLevel === 'all' || (string) $spell['level'] === $selectedLevel)
                && ($selectedSchool === '' || $spell['school'] === $selectedSchool)
                && ($selectedClass === '' || in_array($selectedClass, $spell['classes'], true))
        ));
        $pageTitle = $levelLabels[$selectedLevel];
        $contentView = __DIR__ . '/templates/spells/index.php';
        break;

    case 'mostri':
        $monsters = $monstersData['items'] ?? [];
        $monsterTypes = [];
        $monsterSizes = [];
        $monsterChallenges = [];
        $monsterAlignments = [];
        foreach ($monsters as $monster) {
            $monsterTypes[$monster['type']] = $monster['type'];
            if ($monster['size'] !== '') {
                $monsterSizes[$monster['size']] = $monster['size'];
            }
            $monsterChallenges[$monster['challenge_rating']] = $monster['challenge_rating'];
            $monsterAlignments[$monster['alignment']] = $monster['alignment'];
        }
        ksort($monsterTypes);
        ksort($monsterSizes);
        uksort($monsterChallenges, static fn (string $a, string $b): int => strnatcasecmp($a, $b));
        uasort($monsterAlignments, static fn (string $a, string $b): int => strnatcasecmp($a, $b));
        $selectedMonsterType = (string) ($_GET['tipo'] ?? '');
        $selectedMonsterSize = (string) ($_GET['taglia'] ?? '');
        $selectedMonsterChallenge = (string) ($_GET['gs'] ?? '');
        $selectedMonsterAlignment = (string) ($_GET['allineamento'] ?? '');
        if (!array_key_exists($selectedMonsterType, $monsterTypes)) {
            $selectedMonsterType = '';
        }
        if (!array_key_exists($selectedMonsterSize, $monsterSizes)) {
            $selectedMonsterSize = '';
        }
        if (!array_key_exists($selectedMonsterChallenge, $monsterChallenges)) {
            $selectedMonsterChallenge = '';
        }
        if (!array_key_exists($selectedMonsterAlignment, $monsterAlignments)) {
            $selectedMonsterAlignment = '';
        }
        $monsters = array_values(array_filter(
            $monsters,
            static fn (array $monster): bool =>
                ($selectedMonsterType === '' || $monster['type'] === $selectedMonsterType)
                && ($selectedMonsterSize === '' || $monster['size'] === $selectedMonsterSize)
                && ($selectedMonsterChallenge === '' || $monster['challenge_rating'] === $selectedMonsterChallenge)
                && ($selectedMonsterAlignment === '' || $monster['alignment'] === $selectedMonsterAlignment)
        ));
        $pageTitle = 'Mostri';
        $contentView = __DIR__ . '/templates/monsters/index.php';
        break;

    case 'home':
    default:
        $pageTitle = 'SRD Italia';
        $contentView = __DIR__ . '/templates/home.php';
        break;
}

require __DIR__ . '/templates/layout.php';
