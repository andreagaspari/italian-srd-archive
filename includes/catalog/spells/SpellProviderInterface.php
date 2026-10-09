<?php

declare(strict_types=1);

/**
 * Provides filtered and paginated spell data.
 */
interface SpellProviderInterface
{
    /**
     * Retrieves spells matching the requested criteria.
     *
     * @param SpellQuery $query Filtering and pagination criteria.
     *
     * @return PageResult Query results and pagination metadata.
     */
    public function getSpells(SpellQuery $query): PageResult;
}
