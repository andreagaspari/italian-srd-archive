<?php

declare(strict_types=1);

/**
 * Resolves web routes into template contexts for the server-rendered frontend.
 */
final class FrontendController
{
    /**
     * @param DataCatalog $catalog Application facade for filtered dataset queries.
     * @param string $templatesDirectory Absolute path to the template directory.
     */
    public function __construct(
        private readonly DataCatalog $catalog,
        private readonly string $templatesDirectory
    ) {
    }

    /**
     * Resolves a route and prepares the data required by its template.
     *
     * @param string $route Application route.
     * @param array<string, mixed> $request Request query parameters.
     *
     * @return array{pageTitle: string, contentView: string, variables: array<string, mixed>} Render context.
     */
    public function resolve(string $route, array $request): array
    {
        $page = $this->parsePage($request);
        $pageSize = $this->parsePageSize($request);

        switch ($route) {
            case 'oggetti/armi':
                $categoryLabels = [
                    'items-weapons' => 'Tutte le armi',
                    'items-weapons-simple-melee' => 'Armi da mischia semplici',
                    'items-weapons-simple-ranged' => 'Armi a distanza semplici',
                    'items-weapons-martial-melee' => 'Armi da mischia da guerra',
                    'items-weapons-martial-ranged' => 'Armi a distanza da guerra'
                ];
                $selectedCategory = $request['categoria'] ?? 'items-weapons';
                if (!is_string($selectedCategory) || !array_key_exists($selectedCategory, $categoryLabels)) {
                    $selectedCategory = 'items-weapons';
                }

                $weaponsResult = $this->catalog->getWeapons(new WeaponQuery(
                    category: $selectedCategory,
                    search: $this->parseSearch($request),
                    page: $page,
                    pageSize: $pageSize
                ));
                $filteredWeapons = $weaponsResult->items;
                $pagination = $weaponsResult;

                $pageTitle = $categoryLabels[$selectedCategory];
                $contentView = $this->templatesDirectory . '/objects/weapons.php';
                break;

            case 'oggetti/armature':
                $armorData = $this->catalog->armor();
                $categoryLabels = [
                    'armor' => 'Tutte le armature',
                    'armor-light' => 'Armature leggere',
                    'armor-medium' => 'Armature medie',
                    'armor-heavy' => 'Armature pesanti',
                    'armor-shields' => 'Scudi'
                ];
                $selectedCategory = $request['categoria'] ?? 'armor';
                if (!is_string($selectedCategory) || !array_key_exists($selectedCategory, $categoryLabels)) {
                    $selectedCategory = 'armor';
                }

                $armorResult = $this->catalog->getArmor(new ArmorQuery(
                    category: $selectedCategory,
                    search: $this->parseSearch($request),
                    page: $page,
                    pageSize: $pageSize
                ));
                $filteredArmor = $armorResult->items;
                $pagination = $armorResult;

                $pageTitle = $categoryLabels[$selectedCategory];
                $contentView = $this->templatesDirectory . '/objects/armor.php';
                break;

            case 'oggetti/munizioni':
                $pageTitle = 'Munizioni';
                $ammunitionResult = $this->catalog->getAmmunition(new AmmunitionQuery(
                    search: $this->parseSearch($request),
                    page: $page,
                    pageSize: $pageSize
                ));
                $ammunition = $ammunitionResult->items;
                $pagination = $ammunitionResult;
                $contentView = $this->templatesDirectory . '/objects/ammunition.php';
                break;

            case 'oggetti/strumenti':
                $categoryLabels = [
                    'tools' => 'Tutti gli strumenti',
                    'artigiano' => 'Strumenti da artigiano',
                    'giochi' => 'Giochi',
                    'musicali' => 'Strumenti musicali',
                    'altri' => 'Altri strumenti'
                ];
                $selectedCategory = $request['categoria'] ?? 'tools';
                if (!is_string($selectedCategory) || !array_key_exists($selectedCategory, $categoryLabels)) {
                    $selectedCategory = 'tools';
                }

                $toolsResult = $this->catalog->getTools(new ToolsQuery(
                    category: $selectedCategory,
                    search: $this->parseSearch($request),
                    page: $page,
                    pageSize: $pageSize
                ));
                $tools = $toolsResult->items;
                $pagination = $toolsResult;
                $pageTitle = $categoryLabels[$selectedCategory];
                $contentView = $this->templatesDirectory . '/objects/tools.php';
                break;

            case 'oggetti/avventura':
                $categoryLabels = [
                    'gear' => 'Tutto l’equipaggiamento',
                    'avventura' => 'Avventura',
                    'consumabili' => 'Consumabili',
                    'contenitori' => 'Contenitori',
                    'illuminazione' => 'Illuminazione'
                ];
                $selectedCategory = $request['categoria'] ?? 'gear';
                if (!is_string($selectedCategory) || !array_key_exists($selectedCategory, $categoryLabels)) {
                    $selectedCategory = 'gear';
                }

                $adventuringGearResult = $this->catalog->getAdventuringGear(new AdventuringGearQuery(
                    category: $selectedCategory,
                    search: $this->parseSearch($request),
                    page: $page,
                    pageSize: $pageSize
                ));
                $adventuringGear = $adventuringGearResult->items;
                $pagination = $adventuringGearResult;
                $pageTitle = $categoryLabels[$selectedCategory];
                $contentView = $this->templatesDirectory . '/objects/adventuring-gear.php';
                break;

            case 'oggetti':
                $pageTitle = 'Oggetti';
                $contentView = $this->templatesDirectory . '/objects/index.php';
                break;

            case 'incantesimi':
                $spellsData = $this->catalog->spells();
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
                $selectedLevel = (string) ($request['livello'] ?? 'all');
                if (!array_key_exists($selectedLevel, $levelLabels)) {
                    $selectedLevel = 'all';
                }
                $schoolLabels = ['' => 'Tutte le scuole'];
                foreach ($spellsData['items'] ?? [] as $spell) {
                    $schoolLabels[$spell['school']] = $spell['school'];
                }
                ksort($schoolLabels);
                $selectedSchool = (string) ($request['scuola'] ?? '');
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
                $selectedClass = (string) ($request['classe'] ?? '');
                if ($selectedClass !== '' && !array_key_exists($selectedClass, $spellClasses)) {
                    $selectedClass = '';
                }
                $spellsResult = $this->catalog->getSpells(new SpellQuery(
                    level: $selectedLevel === 'all' ? null : (int) $selectedLevel,
                    school: $selectedSchool,
                    className: $selectedClass,
                    search: $this->parseSearch($request),
                    page: $page,
                    pageSize: $pageSize
                ));
                $spells = $spellsResult->items;
                $pagination = $spellsResult;
                $pageTitle = $levelLabels[$selectedLevel];
                $contentView = $this->templatesDirectory . '/spells/index.php';
                break;

            case 'mostri':
                $monstersData = $this->catalog->monsters();
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
                $selectedMonsterType = (string) ($request['tipo'] ?? '');
                $selectedMonsterSize = (string) ($request['taglia'] ?? '');
                $selectedMonsterChallenge = (string) ($request['gs'] ?? '');
                $selectedMonsterAlignment = (string) ($request['allineamento'] ?? '');
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
                $monstersResult = $this->catalog->getMonsters(new MonsterQuery(
                    type: $selectedMonsterType,
                    size: $selectedMonsterSize,
                    challengeRating: $selectedMonsterChallenge,
                    alignment: $selectedMonsterAlignment,
                    search: $this->parseSearch($request),
                    page: $page,
                    pageSize: $pageSize
                ));
                $monsters = $monstersResult->items;
                $pagination = $monstersResult;
                $pageTitle = 'Mostri';
                $contentView = $this->templatesDirectory . '/monsters/index.php';
                break;

            case 'home':
            default:
                $pageTitle = 'SRD Italia';
                $contentView = $this->templatesDirectory . '/home.php';
                break;
        }

        $variables = get_defined_vars();
        unset($variables['this'], $variables['route'], $variables['request']);

        return [
            'pageTitle' => $pageTitle,
            'contentView' => $contentView,
            'variables' => $variables
        ];
    }

    /**
     * Reads and validates the requested one-based page number.
     *
     * @param array<string, mixed> $request Request query parameters.
     *
     * @return int Valid page number.
     */
    private function parsePage(array $request): int
    {
        $page = filter_var($request['page'] ?? 1, FILTER_VALIDATE_INT);

        return is_int($page) && $page > 0 ? $page : 1;
    }

    /**
     * Reads and validates the requested page size.
     *
     * @param array<string, mixed> $request Request query parameters.
     *
     * @return int Page size between 10 and 100.
     */
    private function parsePageSize(array $request): int
    {
        $pageSize = filter_var($request['pageSize'] ?? 24, FILTER_VALIDATE_INT);

        if (!is_int($pageSize) || $pageSize < 10) {
            return 24;
        }

        return min($pageSize, 100);
    }

    /**
     * Reads the optional section search term.
     *
     * @param array<string, mixed> $request Request query parameters.
     *
     * @return string|null Trimmed search term, or null when absent.
     */
    private function parseSearch(array $request): ?string
    {
        if (!is_string($request['q'] ?? null)) {
            return null;
        }

        $search = trim($request['q']);

        return $search === '' ? null : $search;
    }
}
