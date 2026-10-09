<?php

declare(strict_types=1);

/**
 * Provides access to the application's imported data collections.
 */
final class DataCatalog
{
    /**
     * @param JsonDatasetStore $store Shared dataset store for raw dataset access.
     * @param WeaponProviderInterface $weaponProvider Provider for weapon queries.
     * @param ArmorProviderInterface $armorProvider Provider for armor queries.
     * @param AmmunitionProviderInterface $ammunitionProvider Provider for ammunition queries.
     * @param ToolsProviderInterface $toolsProvider Provider for tool queries.
     * @param AdventuringGearProviderInterface $adventuringGearProvider Provider for adventuring gear queries.
     * @param SpellProviderInterface $spellProvider Provider for spell queries.
     * @param MonsterProviderInterface $monsterProvider Provider for monster queries.
     */
    public function __construct(
        private readonly JsonDatasetStore $store,
        private readonly WeaponProviderInterface $weaponProvider,
        private readonly ArmorProviderInterface $armorProvider,
        private readonly AmmunitionProviderInterface $ammunitionProvider,
        private readonly ToolsProviderInterface $toolsProvider,
        private readonly AdventuringGearProviderInterface $adventuringGearProvider,
        private readonly SpellProviderInterface $spellProvider,
        private readonly MonsterProviderInterface $monsterProvider
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
        return $this->weaponProvider->getWeapons($query);
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
        return $this->armorProvider->getArmor($query);
    }

    /**
     * Retrieves paginated ammunition.
     *
     * @param AmmunitionQuery $query Pagination criteria.
     *
     * @return PageResult Query results and pagination metadata.
     */
    public function getAmmunition(AmmunitionQuery $query): PageResult
    {
        return $this->ammunitionProvider->getAmmunition($query);
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
        return $this->toolsProvider->getTools($query);
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
        return $this->adventuringGearProvider->getAdventuringGear($query);
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
        return $this->spellProvider->getSpells($query);
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
        return $this->monsterProvider->getMonsters($query);
    }

    /**
     * Returns the weapons dataset.
     *
     * @return array<string, mixed>
     */
    public function weapons(): array
    {
        return $this->store->get('weapons');
    }

    /**
     * Returns the armor dataset.
     *
     * @return array<string, mixed>
     */
    public function armor(): array
    {
        return $this->store->get('armor');
    }

    /**
     * Returns the ammunition dataset.
     *
     * @return array<string, mixed>
     */
    public function ammunition(): array
    {
        return $this->store->get('ammunition');
    }

    /**
     * Returns the tools dataset.
     *
     * @return array<string, mixed>
     */
    public function tools(): array
    {
        return $this->store->get('tools');
    }

    /**
     * Returns the adventuring gear dataset.
     *
     * @return array<string, mixed>
     */
    public function adventuringGear(): array
    {
        return $this->store->get('adventuring-gear');
    }

    /**
     * Returns the spells dataset.
     *
     * @return array<string, mixed>
     */
    public function spells(): array
    {
        return $this->store->get('spells');
    }

    /**
     * Returns the monsters dataset.
     *
     * @return array<string, mixed>
     */
    public function monsters(): array
    {
        return $this->store->get('monsters');
    }
}
