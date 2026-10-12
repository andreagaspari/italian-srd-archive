<?php

declare(strict_types=1);

/**
 * Provides all catalog collections from the normalized MySQL schema.
 */
final class MySqlCatalogProvider implements
    WeaponProviderInterface,
    ArmorProviderInterface,
    AmmunitionProviderInterface,
    ToolsProviderInterface,
    AdventuringGearProviderInterface,
    SpellProviderInterface,
    MonsterProviderInterface
{
    /**
     * @param PDO $database Configured MySQL connection.
     */
    public function __construct(
        private readonly PDO $database
    ) {
    }

    /**
     * Retrieves filtered and paginated weapons.
     *
     * @param WeaponQuery $query Filtering and pagination criteria.
     *
     * @return PageResult Query results and pagination metadata.
     */
    public function getWeapons(WeaponQuery $query): PageResult
    {
        $conditions = [];
        if ($query->category !== null && $query->category !== '' && $query->category !== 'items-weapons') {
            $conditions[] = match ($query->category) {
                'items-weapons-simple-melee' => 'wi.proficiency = ? AND wi.mode = ?',
                'items-weapons-simple-ranged' => 'wi.proficiency = ? AND wi.mode = ?',
                'items-weapons-martial-melee' => 'wi.proficiency = ? AND wi.mode = ?',
                'items-weapons-martial-ranged' => 'wi.proficiency = ? AND wi.mode = ?',
                default => '1 = 0'
            };
        }
        $categoryParameters = match ($query->category) {
            'items-weapons-simple-melee', 'items-weapons-simple-ranged' => ['Semplice', $query->category === 'items-weapons-simple-melee' ? 'Mischia' : 'A distanza'],
            'items-weapons-martial-melee', 'items-weapons-martial-ranged' => ['Da guerra', $query->category === 'items-weapons-martial-melee' ? 'Mischia' : 'A distanza'],
            default => []
        };

        return $this->find(
            dataset: 'weapons',
            join: 'JOIN weapon_items wi ON wi.catalog_item_id = ci.id',
            conditions: $conditions,
            parameters: $categoryParameters,
            search: $query->search,
            page: $query->page,
            pageSize: $query->pageSize
        );
    }

    /**
     * Retrieves filtered and paginated armor.
     *
     * @param ArmorQuery $query Filtering and pagination criteria.
     *
     * @return PageResult Query results and pagination metadata.
     */
    public function getArmor(ArmorQuery $query): PageResult
    {
        $conditions = [];
        $parameters = [];
        if ($query->category !== null && $query->category !== '' && $query->category !== 'armor') {
            $conditions[] = 'ai.armor_type = ?';
            $parameters[] = match ($query->category) {
                'armor-light' => 'leggera',
                'armor-medium' => 'media',
                'armor-heavy' => 'pesante',
                'armor-shields' => 'scudo',
                default => '__invalid__'
            };
        }

        return $this->find('armor', 'JOIN armor_items ai ON ai.catalog_item_id = ci.id', $conditions, $parameters, $query->search, $query->page, $query->pageSize);
    }

    /**
     * Retrieves filtered and paginated ammunition.
     *
     * @param AmmunitionQuery $query Filtering and pagination criteria.
     *
     * @return PageResult Query results and pagination metadata.
     */
    public function getAmmunition(AmmunitionQuery $query): PageResult
    {
        return $this->find('ammunition', 'JOIN ammunition_items ammi ON ammi.catalog_item_id = ci.id', [], [], $query->search, $query->page, $query->pageSize);
    }

    /**
     * Retrieves filtered and paginated tools.
     *
     * @param ToolsQuery $query Filtering and pagination criteria.
     *
     * @return PageResult Query results and pagination metadata.
     */
    public function getTools(ToolsQuery $query): PageResult
    {
        $conditions = [];
        $parameters = [];
        if ($query->category !== null && $query->category !== '' && $query->category !== 'tools') {
            $conditions[] = 'ti.tool_type = ?';
            $parameters[] = $query->category;
        }

        return $this->find('tools', 'JOIN tool_items ti ON ti.catalog_item_id = ci.id', $conditions, $parameters, $query->search, $query->page, $query->pageSize);
    }

    /**
     * Retrieves filtered and paginated adventuring gear.
     *
     * @param AdventuringGearQuery $query Filtering and pagination criteria.
     *
     * @return PageResult Query results and pagination metadata.
     */
    public function getAdventuringGear(AdventuringGearQuery $query): PageResult
    {
        $conditions = [];
        $parameters = [];
        if ($query->category !== null && $query->category !== '' && $query->category !== 'gear') {
            $conditions[] = 'agi.category = ?';
            $parameters[] = $query->category;
        }

        return $this->find('adventuring-gear', 'JOIN adventuring_gear_items agi ON agi.catalog_item_id = ci.id', $conditions, $parameters, $query->search, $query->page, $query->pageSize);
    }

    /**
     * Retrieves filtered and paginated spells.
     *
     * @param SpellQuery $query Filtering and pagination criteria.
     *
     * @return PageResult Query results and pagination metadata.
     */
    public function getSpells(SpellQuery $query): PageResult
    {
        $conditions = [];
        $parameters = [];
        $join = 'JOIN spell_items si ON si.catalog_item_id = ci.id';
        if ($query->level !== null) {
            $conditions[] = 'si.level = ?';
            $parameters[] = $query->level;
        }
        if ($query->school !== null && $query->school !== '') {
            $conditions[] = 'si.school = ?';
            $parameters[] = $query->school;
        }
        if ($query->className !== null && $query->className !== '') {
            $conditions[] = 'EXISTS (SELECT 1 FROM spell_classes sc WHERE sc.catalog_item_id = ci.id AND sc.class_name = ?)';
            $parameters[] = $query->className;
        }

        return $this->find('spells', $join, $conditions, $parameters, $query->search, $query->page, $query->pageSize);
    }

    /**
     * Retrieves filtered and paginated monsters.
     *
     * @param MonsterQuery $query Filtering and pagination criteria.
     *
     * @return PageResult Query results and pagination metadata.
     */
    public function getMonsters(MonsterQuery $query): PageResult
    {
        $conditions = [];
        $parameters = [];
        foreach ([
            ['mi.monster_type', $query->type],
            ['mi.size', $query->size],
            ['mi.challenge_rating', $query->challengeRating],
            ['mi.alignment', $query->alignment],
        ] as [$column, $value]) {
            if ($value !== null && $value !== '') {
                $conditions[] = "{$column} = ?";
                $parameters[] = $value;
            }
        }

        return $this->find('monsters', 'JOIN monster_items mi ON mi.catalog_item_id = ci.id', $conditions, $parameters, $query->search, $query->page, $query->pageSize);
    }

    /**
     * Executes a catalog query and hydrates the original JSON item shape.
     *
     * @param string $dataset Dataset identifier.
     * @param string $join Dataset-specific SQL join.
     * @param array<int, string> $conditions Additional SQL conditions.
     * @param array<int, mixed> $parameters Parameters for additional conditions.
     * @param string|null $search Full-text search term.
     * @param int $page One-based page number.
     * @param int|null $pageSize Page size, or null for all results.
     *
     * @return PageResult Hydrated results and pagination metadata.
     */
    private function find(
        string $dataset,
        string $join,
        array $conditions,
        array $parameters,
        ?string $search,
        int $page,
        ?int $pageSize
    ): PageResult {
        $where = ['ci.dataset = ?'];
        $baseParameters = [$dataset];
        if ($search !== null && trim($search) !== '') {
            $where[] = '(MATCH(ci.name, ci.search_text) AGAINST (? IN BOOLEAN MODE) OR ci.name LIKE ? OR ci.search_text LIKE ?)';
            $baseParameters[] = $search;
            $baseParameters[] = '%' . $search . '%';
            $baseParameters[] = '%' . $search . '%';
        }
        $where = array_merge($where, $conditions);
        $queryParameters = array_merge($baseParameters, $parameters);
        $whereSql = implode(' AND ', $where);
        $fromSql = "FROM catalog_items ci {$join} WHERE {$whereSql}";

        $countStatement = $this->database->prepare("SELECT COUNT(*) {$fromSql}");
        $countStatement->execute($queryParameters);
        $totalItems = (int) $countStatement->fetchColumn();

        $sql = "SELECT ci.payload {$fromSql} ORDER BY ci.name ASC";
        if ($pageSize !== null) {
            $sql .= ' LIMIT ? OFFSET ?';
            $queryParameters[] = $pageSize;
            $queryParameters[] = ($page - 1) * $pageSize;
        }
        $statement = $this->database->prepare($sql);
        foreach ($queryParameters as $index => $parameter) {
            $statement->bindValue($index + 1, $parameter, is_int($parameter) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $statement->execute();

        $items = [];
        foreach ($statement->fetchAll(PDO::FETCH_COLUMN) as $payload) {
            $item = json_decode((string) $payload, true, 512, JSON_THROW_ON_ERROR);
            if (!is_array($item)) {
                throw new UnexpectedValueException("Payload non valido nel dataset {$dataset}.");
            }
            $items[] = $item;
        }

        return new PageResult($items, $page, $pageSize, $totalItems);
    }
}
