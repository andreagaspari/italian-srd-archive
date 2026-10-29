<?php

declare(strict_types=1);

/**
 * Builds the global search index used by the server-rendered frontend.
 */
final class SearchIndexBuilder
{
    /**
     * Builds search entries from the available raw datasets.
     *
     * @param array<string, array<string, mixed>> $datasets Raw application datasets.
     *
     * @return array<int, array<string, mixed>> Search index entries.
     */
    public function build(array $datasets): array
    {
        $index = [
            ['section' => 'Oggetti', 'title' => 'Oggetti', 'description' => 'Armi, armature, munizioni, strumenti ed equipaggiamento.', 'tags' => [], 'url' => appUrl('oggetti')],
            ['section' => 'Armi', 'title' => 'Armi', 'description' => 'Armi da mischia e a distanza, semplici e da guerra.', 'tags' => ['Categoria'], 'url' => appUrl('oggetti/armi')],
            ['section' => 'Armi', 'title' => 'Armi da mischia semplici', 'description' => 'Categoria di armi.', 'tags' => ['Categoria', 'Mischia', 'Semplice'], 'url' => appUrl('oggetti/armi?categoria=items-weapons-simple-melee')],
            ['section' => 'Armi', 'title' => 'Armi a distanza semplici', 'description' => 'Categoria di armi.', 'tags' => ['Categoria', 'A distanza', 'Semplice'], 'url' => appUrl('oggetti/armi?categoria=items-weapons-simple-ranged')],
            ['section' => 'Armi', 'title' => 'Armi da mischia da guerra', 'description' => 'Categoria di armi.', 'tags' => ['Categoria', 'Mischia', 'Da guerra'], 'url' => appUrl('oggetti/armi?categoria=items-weapons-martial-melee')],
            ['section' => 'Armi', 'title' => 'Armi a distanza da guerra', 'description' => 'Categoria di armi.', 'tags' => ['Categoria', 'A distanza', 'Da guerra'], 'url' => appUrl('oggetti/armi?categoria=items-weapons-martial-ranged')],
            ['section' => 'Armature', 'title' => 'Armature', 'description' => 'Armature leggere, medie, pesanti e scudi.', 'tags' => ['Categoria'], 'url' => appUrl('oggetti/armature')],
            ['section' => 'Munizioni', 'title' => 'Munizioni', 'description' => 'Munizioni per archi, balestre, fionde e armi da fuoco.', 'tags' => ['Categoria'], 'url' => appUrl('oggetti/munizioni')],
            ['section' => 'Strumenti', 'title' => 'Strumenti', 'description' => 'Strumenti da artigiano, musicali, giochi e altri strumenti.', 'tags' => ['Categoria'], 'url' => appUrl('oggetti/strumenti')],
            ['section' => 'Equipaggiamento', 'title' => 'Equipaggiamento', 'description' => 'Oggetti, contenitori, consumabili e attrezzatura da avventura.', 'tags' => ['Categoria'], 'url' => appUrl('oggetti/avventura')],
            ['section' => 'Incantesimi', 'title' => 'Incantesimi', 'description' => 'Trucchetti e incantesimi dal 1° al 9° livello.', 'tags' => ['Categoria'], 'url' => appUrl('incantesimi')],
            ['section' => 'Talenti', 'title' => 'Talenti', 'description' => 'Talenti di origine, generali, epici e di stile di combattimento.', 'tags' => ['Categoria'], 'url' => appUrl('talenti')],
            ['section' => 'Mostri', 'title' => 'Mostri', 'description' => 'Blocchi statistici dei mostri dello SRD 5.2.1.', 'tags' => ['Categoria'], 'url' => appUrl('mostri')]
        ];
        foreach ($index as &$entry) {
            $entry['type'] = $this->resultType((string) $entry['section']);
            $entry['tagTitles'] = array_fill(0, count($entry['tags']), 'Tipo di risultato');
        }
        unset($entry);

        $this->appendSearchItems($index, $datasets['objectData']['items'] ?? [], 'Armi', appUrl('oggetti/armi'), static fn (array $item): string => $item['name'], static fn (array $item): array => [$item['mode'], $item['proficiency']]);
        $this->appendSearchItems($index, $datasets['armorData']['items'] ?? [], 'Armature', appUrl('oggetti/armature'), static fn (array $item): string => $item['name'], static fn (array $item): array => [$item['armor_type']]);
        $this->appendSearchItems($index, $datasets['ammunitionData']['items'] ?? [], 'Munizioni', appUrl('oggetti/munizioni'), static fn (array $item): string => $item['name'], static fn (array $item): array => [$item['container']]);
        $this->appendSearchItems($index, $datasets['toolsData']['items'] ?? [], 'Strumenti', appUrl('oggetti/strumenti'), static fn (array $item): string => $item['name'], static fn (array $item): array => [$item['tool_type']]);
        $this->appendSearchItems($index, $datasets['adventuringGearData']['items'] ?? [], 'Equipaggiamento', appUrl('oggetti/avventura'), static fn (array $item): string => $item['name'], static fn (array $item): array => [$item['category']]);
        $this->appendSearchItems($index, $datasets['spellsData']['items'] ?? [], 'Incantesimi', appUrl('incantesimi'), static fn (array $item): string => $item['name'], static fn (array $item): array => [
            $item['level'] === 0 ? 'Trucchetto' : 'Livello ' . $item['level'],
            $item['school'],
            ...$item['classes']
        ]);
        $this->appendSearchItems($index, $datasets['featsData']['items'] ?? [], 'Talenti', appUrl('talenti'), static fn (array $item): string => $item['name'], static fn (array $item): array => [
            $item['subcategory'],
            (string) ($item['prerequisite'] ?? ''),
            !empty($item['repeatable']) ? 'Ripetibile' : ''
        ]);
        $this->appendSearchItems($index, $datasets['monstersData']['items'] ?? [], 'Mostri', appUrl('mostri'), static fn (array $item): string => $item['name'], static fn (array $item): array => [$item['type'], $item['size'], $item['challenge_rating']]);
        $this->appendSearchItems($index, $datasets['rulesData']['items'] ?? [], 'Regole', appUrl('regole'), static fn (array $item): string => $item['name'], static fn (array $item): array => [(string) ($item['category'] ?? '')]);

        return $index;
    }

    /**
     * Appends searchable records from one dataset.
     *
     * @param array<int, array<string, mixed>> $index Search index to mutate.
     * @param array<int, array<string, mixed>> $items Dataset records.
     * @param string $section Search result section.
     * @param string $url Result destination URL.
     * @param callable(array<string, mixed>): string $titleFormatter Creates the result title.
     * @param callable(array<string, mixed>): array<int, mixed> $tagFormatter Creates result tags.
     */
    private function appendSearchItems(
        array &$index,
        array $items,
        string $section,
        string $url,
        callable $titleFormatter,
        callable $tagFormatter
    ): void {
        foreach ($items as $item) {
            $title = $titleFormatter($item);
            $tags = array_values(array_filter($tagFormatter($item)));
            $values = $this->flattenSearchValues($item);
            $descriptionValues = $this->flattenSearchValues(array_diff_key($item, array_flip([
                'id', 'name', 'mode', 'proficiency', 'subcategory', 'armor_type', 'tool_type', 'category', 'container', 'source_page'
            ])));
            $descriptionValues = array_values(array_unique(array_filter(
                $descriptionValues,
                static fn (string $value): bool => !in_array($value, $tags, true)
            )));
            $description = implode(' · ', array_slice($descriptionValues, 0, 3));
            if (isset($item['source_page']) && is_scalar($item['source_page'])) {
                $description = 'p. ' . $item['source_page'] . ($description !== '' ? ' · ' . $description : '');
            }
            $index[] = [
                'section' => $section,
                'title' => $title,
                'description' => $description,
                'tags' => $tags,
                'tagTitles' => $this->tagTitles($section, $tags),
                'type' => $this->resultType($section),
                'search' => implode(' ', $values),
                'url' => $this->buildItemUrl($url, $title, (string) ($item['id'] ?? ''))
            ];
        }
    }

    /**
     * Adds the item name and identifier to an item result URL.
     *
     * @param string $url Base section URL, optionally containing filters.
     * @param string $title Item name used to narrow the destination results.
     * @param string $itemId Stable item identifier used to highlight the result.
     *
     * @return string URL that opens the matching section search.
     */
    private function buildItemUrl(string $url, string $title, string $itemId): string
    {
        $separator = str_contains($url, '?') ? '&' : '?';

        return $url . $separator . http_build_query(
            ['q' => $title, 'focus' => $itemId],
            '',
            '&',
            PHP_QUERY_RFC3986
        );
    }

    /**
     * Converts an index section into a singular result type label.
     *
     * @param string $section Search index section.
     *
     * @return string Result type label.
     */
    private function resultType(string $section): string
    {
        return match ($section) {
            'Armi' => 'Arma',
            'Armature' => 'Armatura',
            'Munizioni' => 'Munizione',
            'Strumenti' => 'Strumento',
            'Equipaggiamento' => 'Equipaggiamento',
            'Incantesimi' => 'Incantesimo',
            'Mostri' => 'Mostro',
            'Regole' => 'Regola',
            'Talenti' => 'Talento',
            default => 'Categoria'
        };
    }

    /**
     * Creates explanatory titles for the tags of a search result.
     *
     * @param string $section Search result section.
     * @param array<int, string> $tags Result tags.
     *
     * @return array<int, string> Tag explanations aligned with the tags array.
     */
    private function tagTitles(string $section, array $tags): array
    {
        return array_map(
            function (string $tag) use ($section): string {
                return match ($section) {
                    'Armi' => match ($tag) {
                        'Mischia', 'A distanza' => 'Modalità di combattimento',
                        'Semplice', 'Da guerra' => 'Tipo di competenza',
                        default => 'Categoria dell’arma'
                    },
                    'Armature' => 'Tipo di armatura',
                    'Munizioni' => 'Contenitore o tipo di munizione',
                    'Strumenti' => 'Tipo di strumento',
                    'Equipaggiamento' => 'Categoria dell’equipaggiamento',
                    'Incantesimi' => str_starts_with($tag, 'Livello ')
                        || $tag === 'Trucchetto'
                        ? 'Livello dell’incantesimo'
                        : (in_array($tag, ['Invocazione', 'Abiurazione', 'Ammaliamento', 'Divinazione', 'Illusione', 'Necromanzia', 'Trasmutazione'], true)
                            ? 'Scuola dell’incantesimo'
                            : 'Classe dell’incantesimo'),
                    'Mostri' => match ($tag) {
                        'Enorme', 'Grande', 'Media', 'Piccola', 'Minuscola' => 'Taglia della creatura',
                        default => $this->monsterTagTitle($tag)
                    },
                    'Regole' => 'Categoria della regola',
                    default => 'Tipo di risultato'
                };
            },
            $tags
        );
    }

    /**
     * Determines the meaning of a monster search tag.
     *
     * @param string $tag Monster tag.
     *
     * @return string Tag explanation.
     */
    private function monsterTagTitle(string $tag): string
    {
        return preg_match('/^(?:GS|CR)?\s*\d+(?:\/\d+)?$/i', $tag) === 1
            ? 'Grado di sfida'
            : 'Tipo di creatura';
    }

    /**
     * Flattens nested scalar values into searchable strings.
     *
     * @param mixed $value Value to flatten.
     *
     * @return array<int, string> Scalar values found in the input.
     */
    private function flattenSearchValues(mixed $value): array
    {
        if (is_array($value)) {
            $values = [];
            foreach ($value as $nestedValue) {
                $values = array_merge($values, $this->flattenSearchValues($nestedValue));
            }
            return $values;
        }

        return is_scalar($value) ? [(string) $value] : [];
    }
}
