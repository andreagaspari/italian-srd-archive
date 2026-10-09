<?php

declare(strict_types=1);

/**
 * Describes pagination criteria for ammunition queries.
 */
final class AmmunitionQuery
{
    /**
     * @param int $page One-based page number.
     * @param int|null $pageSize Maximum number of items to return, or null for all matches.
     *
     * @throws InvalidArgumentException If pagination values are invalid.
     */
    public function __construct(
        public readonly int $page = 1,
        public readonly ?int $pageSize = null
    ) {
        if ($this->page < 1) {
            throw new InvalidArgumentException('La pagina deve essere maggiore o uguale a 1.');
        }

        if ($this->pageSize !== null && $this->pageSize < 1) {
            throw new InvalidArgumentException('La dimensione della pagina deve essere maggiore di 0.');
        }
    }
}
