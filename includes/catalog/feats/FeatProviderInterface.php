<?php

declare(strict_types=1);

/**
 * Defines the provider contract for querying feats.
 */
interface FeatProviderInterface
{
    /**
     * Retrieves filtered and paginated feats.
     *
     * @param FeatQuery $query Filtering and pagination criteria.
     *
     * @return PageResult Query results and pagination metadata.
     */
    public function getFeats(FeatQuery $query): PageResult;
}
