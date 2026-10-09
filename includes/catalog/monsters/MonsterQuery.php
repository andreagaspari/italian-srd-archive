<?php

declare(strict_types=1);

/**
 * Describes filtering and pagination criteria for monster queries.
 */
final class MonsterQuery
{
    /**
     * @param string|null $type Monster type identifier.
     * @param string|null $size Monster size identifier.
     * @param string|null $challengeRating Monster challenge rating.
     * @param string|null $alignment Monster alignment.
     * @param int $page One-based page number.
     * @param int|null $pageSize Maximum number of items to return, or null for all matches.
     *
     * @throws InvalidArgumentException If pagination values are invalid.
     */
    public function __construct(
        public readonly ?string $type = null,
        public readonly ?string $size = null,
        public readonly ?string $challengeRating = null,
        public readonly ?string $alignment = null,
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
