<?php

declare(strict_types=1);

/**
 * Provides filtered rules from JSON or MySQL.
 */
final class RulesCatalog
{
    /**
     * @var array<string, mixed>|null
     */
    private ?array $document = null;

    /**
     * @param JsonDatasetStore $store Shared JSON dataset store.
     * @param PDO|null $database Optional MySQL connection.
     */
    public function __construct(
        private readonly JsonDatasetStore $store,
        private readonly ?PDO $database = null
    ) {
    }

    /**
     * Returns the complete glossary document.
     *
     * @return array<string, mixed> Glossary metadata, introduction and rules.
     */
    public function document(): array
    {
        return $this->document ??= $this->database === null
            ? $this->store->get('rules')
            : $this->loadMysqlDocument();
    }

    /**
     * Returns rules matching the requested filters.
     *
     * @param string|null $category Optional category.
     * @param string|null $search Optional case-insensitive text search.
     * @param int $page One-based page number.
     * @param int $pageSize Maximum number of rules per page.
     *
     * @return PageResult Matching rules and pagination metadata.
     */
    public function getRules(?string $category, ?string $search, int $page, int $pageSize): PageResult
    {
        if ($this->database !== null) {
            return $this->getMysqlRules($category, $search, $page, $pageSize);
        }

        $items = $this->document()['items'] ?? [];
        if (!is_array($items)) {
            throw new UnexpectedValueException('Dataset delle regole non valido.');
        }

        $normalizedSearch = mb_strtolower(trim((string) $search), 'UTF-8');
        $filtered = array_values(array_filter(
            $items,
            static function (mixed $item) use ($category, $normalizedSearch): bool {
                if (!is_array($item)) {
                    return false;
                }
                if ($category !== null && $category !== '' && ($item['category'] ?? null) !== $category) {
                    return false;
                }
                return $normalizedSearch === '' || str_contains(
                    mb_strtolower(implode(' ', [
                        (string) ($item['name'] ?? ''),
                        (string) ($item['category'] ?? ''),
                        (string) ($item['content'] ?? '')
                    ]), 'UTF-8'),
                    $normalizedSearch
                );
            }
        ));

        return new PageResult(
            array_slice($filtered, ($page - 1) * $pageSize, $pageSize),
            $page,
            $pageSize,
            count($filtered)
        );
    }

    /**
     * Returns available rule categories.
     *
     * @return array<int, string> Category identifiers.
     */
    public function categories(): array
    {
        $categories = $this->document()['categories'] ?? [];
        if (!is_array($categories)) {
            throw new UnexpectedValueException('Categorie delle regole non valide.');
        }
        return array_values(array_filter($categories, 'is_string'));
    }

    /**
     * Loads the glossary document from MySQL.
     *
     * @return array<string, mixed> Glossary document.
     */
    private function loadMysqlDocument(): array
    {
        $statement = $this->database->query(
            'SELECT source_title, source_version, source_pages_from, source_pages_to,
                    source_url, introduction, abbreviations, categories
             FROM rule_documents WHERE document_key = "rules" LIMIT 1'
        );
        $document = $statement->fetch();
        if (!is_array($document)) {
            throw new RuntimeException('Documento del Glossario non trovato nel database.');
        }

        return [
            'source' => [
                'title' => $document['source_title'],
                'version' => $document['source_version'],
                'pages' => [
                    'from' => (int) $document['source_pages_from'],
                    'to' => (int) $document['source_pages_to'],
                ],
                'url' => $document['source_url'],
            ],
            'introduction' => $this->decodeJson($document['introduction'], 'introduzione'),
            'abbreviations' => $this->decodeJson($document['abbreviations'], 'abbreviazioni'),
            'categories' => $this->decodeJson($document['categories'], 'categorie'),
            'items' => $this->loadMysqlItems(),
        ];
    }

    /**
     * Loads glossary entries from MySQL.
     *
     * @return array<int, array<string, mixed>> Rule entries.
     */
    private function loadMysqlItems(): array
    {
        $statement = $this->database->query(
            'SELECT item_id AS id, name, category, source_page, content, see_also, payload
             FROM rule_items WHERE document_key = "rules" ORDER BY id ASC'
        );
        $items = [];
        foreach ($statement->fetchAll() as $item) {
            $item['source_page'] = (int) $item['source_page'];
            $item['see_also'] = $this->decodeJson($item['see_also'], 'riferimenti della regola');
            $item = $this->mergePayload($item);
            $items[] = $item;
        }
        return $items;
    }

    /**
     * Queries filtered rules from MySQL.
     *
     * @param string|null $category Optional category.
     * @param string|null $search Optional search term.
     * @param int $page One-based page number.
     * @param int $pageSize Maximum results per page.
     *
     * @return PageResult Matching rules and pagination metadata.
     */
    private function getMysqlRules(?string $category, ?string $search, int $page, int $pageSize): PageResult
    {
        $conditions = ['document_key = :document_key'];
        $parameters = ['document_key' => 'rules'];
        if ($category !== null && $category !== '') {
            $conditions[] = 'category = :category';
            $parameters['category'] = $category;
        }
        $normalizedSearch = trim((string) $search);
        if ($normalizedSearch !== '') {
            $conditions[] = '(name LIKE :search_name OR search_text LIKE :search_text)';
            $parameters['search_name'] = '%' . $normalizedSearch . '%';
            $parameters['search_text'] = '%' . $normalizedSearch . '%';
        }
        $where = implode(' AND ', $conditions);
        $count = $this->database->prepare("SELECT COUNT(*) FROM rule_items WHERE {$where}");
        $count->execute($parameters);
        $totalItems = (int) $count->fetchColumn();

        $statement = $this->database->prepare(
            "SELECT item_id AS id, name, category, source_page, content, see_also, payload
             FROM rule_items WHERE {$where} ORDER BY id ASC
             LIMIT :page_size OFFSET :offset"
        );
        foreach ($parameters as $name => $value) {
            $statement->bindValue(':' . $name, $value);
        }
        $statement->bindValue(':page_size', $pageSize, PDO::PARAM_INT);
        $statement->bindValue(':offset', ($page - 1) * $pageSize, PDO::PARAM_INT);
        $statement->execute();
        $items = [];
        foreach ($statement->fetchAll() as $item) {
            $item['source_page'] = (int) $item['source_page'];
            $item['see_also'] = $this->decodeJson($item['see_also'], 'riferimenti della regola');
            $item = $this->mergePayload($item);
            $items[] = $item;
        }

        return new PageResult($items, $page, $pageSize, $totalItems);
    }

    /**
     * Decodes and validates a JSON database column.
     *
     * @param mixed $value Encoded JSON.
     * @param string $label Field label for errors.
     *
     * @return array<mixed> Decoded value.
     */
    private function decodeJson(mixed $value, string $label): array
    {
        try {
            $decoded = json_decode((string) $value, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new UnexpectedValueException("Campo {$label} non valido nel database.", 0, $exception);
        }
        if (!is_array($decoded)) {
            throw new UnexpectedValueException("Campo {$label} non valido nel database.");
        }
        return $decoded;
    }

    /**
     * Restores optional structured presentation data stored in the rule payload.
     *
     * @param array<string, mixed> $item Database rule item.
     *
     * @return array<string, mixed> Rule item enriched with supported payload fields.
     */
    private function mergePayload(array $item): array
    {
        $payload = $this->decodeJson($item['payload'] ?? '{}', 'payload della regola');
        if (isset($payload['blocks']) && is_array($payload['blocks'])) {
            $item['blocks'] = $payload['blocks'];
        }
        unset($item['payload']);
        return $item;
    }
}
