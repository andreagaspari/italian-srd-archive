<?php

declare(strict_types=1);

/**
 * Provides filtered and paginated armor data.
 */
interface ArmorProviderInterface
{
    /**
     * Retrieves armor matching the requested criteria.
     *
     * @param ArmorQuery $query Filtering and pagination criteria.
     *
     * @return PageResult Query results and pagination metadata.
     */
    public function getArmor(ArmorQuery $query): PageResult;
}
