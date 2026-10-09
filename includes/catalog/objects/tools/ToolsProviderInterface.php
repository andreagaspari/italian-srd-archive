<?php

declare(strict_types=1);

/**
 * Provides filtered and paginated tool data.
 */
interface ToolsProviderInterface
{
    /**
     * Retrieves tools matching the requested criteria.
     *
     * @param ToolsQuery $query Filtering and pagination criteria.
     *
     * @return PageResult Query results and pagination metadata.
     */
    public function getTools(ToolsQuery $query): PageResult;
}
