<?php

declare(strict_types=1);

/**
 * Provides filtered and paginated adventuring gear data.
 */
interface AdventuringGearProviderInterface
{
    /**
     * Retrieves adventuring gear matching the requested criteria.
     *
     * @param AdventuringGearQuery $query Filtering and pagination criteria.
     *
     * @return PageResult Query results and pagination metadata.
     */
    public function getAdventuringGear(AdventuringGearQuery $query): PageResult;
}
