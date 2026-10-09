<?php

declare(strict_types=1);

/**
 * Provides filtered and paginated monster data.
 */
interface MonsterProviderInterface
{
    /**
     * Retrieves monsters matching the requested criteria.
     *
     * @param MonsterQuery $query Filtering and pagination criteria.
     *
     * @return PageResult Query results and pagination metadata.
     */
    public function getMonsters(MonsterQuery $query): PageResult;
}
