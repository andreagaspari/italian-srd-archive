<?php

declare(strict_types=1);

/**
 * Provides filtered and paginated ammunition data.
 */
interface AmmunitionProviderInterface
{
    /**
     * Retrieves ammunition matching the requested criteria.
     *
     * @param AmmunitionQuery $query Pagination criteria.
     *
     * @return PageResult Query results and pagination metadata.
     */
    public function getAmmunition(AmmunitionQuery $query): PageResult;
}
