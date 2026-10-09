<?php

declare(strict_types=1);

/**
 * Provides filtered and paginated weapon data.
 */
interface WeaponProviderInterface
{
    /**
     * Retrieves weapons matching the requested criteria.
     *
     * @param WeaponQuery $query Filtering and pagination criteria.
     *
     * @return PageResult Query results and pagination metadata.
     */
    public function getWeapons(WeaponQuery $query): PageResult;
}
