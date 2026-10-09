<?php

declare(strict_types=1);

/**
 * Describes filtering and pagination criteria for spell queries.
 */
final class SpellQuery
{
    /**
     * @param int|null $level Spell level, where zero represents a cantrip.
     * @param string|null $school Spell school identifier.
     * @param string|null $className Character class identifier.
     * @param string|null $search Text to search across spell fields.
     * @param int $page One-based page number.
     * @param int|null $pageSize Maximum number of items to return, or null for all matches.
     *
     * @throws InvalidArgumentException If pagination values are invalid.
     */
    public function __construct(
        public readonly ?int $level = null,
        public readonly ?string $school = null,
        public readonly ?string $className = null,
        public readonly ?string $search = null,
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
