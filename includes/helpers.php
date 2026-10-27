<?php

declare(strict_types=1);

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/**
 * Marks glossary abbreviations with accessible browser tooltips.
 *
 * @param string $content Rule text to render.
 * @param array<int, array{abbreviation: string, meaning: string}> $abbreviations Glossary abbreviations.
 *
 * @return string Escaped rule text containing safe abbreviation markup.
 */
function ruleContentWithAbbreviations(string $content, array $abbreviations): string
{
    $escapedContent = e($content);
    $definitions = [];
    foreach ($abbreviations as $abbreviation) {
        $key = (string) ($abbreviation['abbreviation'] ?? '');
        $meaning = (string) ($abbreviation['meaning'] ?? '');
        if ($key === '' || $meaning === '') {
            continue;
        }
        $definitions[$key] = $meaning;
    }
    uksort($definitions, static fn (string $left, string $right): int => strlen($right) <=> strlen($left));
    if ($definitions === []) {
        return $escapedContent;
    }

    $pattern = '/(?<![\p{L}])(' . implode('|', array_map(
        static fn (string $value): string => preg_quote(e($value), '/'),
        array_keys($definitions)
    )) . ')(?![\p{L}])/u';

    return preg_replace_callback(
        $pattern,
        static function (array $match) use ($definitions): string {
            $abbreviation = html_entity_decode($match[1], ENT_QUOTES, 'UTF-8');
            return '<abbr class="rule-abbreviation" title="' . e($definitions[$abbreviation]) . '">' . $match[1] . '</abbr>';
        },
        $escapedContent
    ) ?? $escapedContent;
}

/**
 * Renders structured rule blocks, falling back to legacy rule text.
 *
 * @param array<string, mixed> $rule Rule data with optional structured blocks.
 * @param array<int, array{abbreviation: string, meaning: string}> $abbreviations Glossary abbreviations.
 *
 * @return string Safe HTML for paragraphs and tables.
 */
function renderRuleContent(array $rule, array $abbreviations): string
{
    $blocks = $rule['blocks'] ?? null;
    if (!is_array($blocks)) {
        return '<p>' . ruleContentWithAbbreviations((string) ($rule['content'] ?? ''), $abbreviations) . '</p>';
    }

    $html = '';
    foreach ($blocks as $block) {
        if (!is_array($block) || !is_string($block['type'] ?? null)) {
            continue;
        }
        if ($block['type'] === 'paragraph' && is_string($block['text'] ?? null)) {
            $html .= '<p>' . ruleContentWithAbbreviations($block['text'], $abbreviations) . '</p>';
            continue;
        }
        if ($block['type'] === 'section'
            && is_string($block['title'] ?? null)
            && is_string($block['text'] ?? null)
        ) {
            $title = rtrim($block['title'], ". \t\n\r\0\x0B");
            $html .= '<p><strong>' . e($title) . '.</strong> '
                . ruleContentWithAbbreviations($block['text'], $abbreviations) . '</p>';
            continue;
        }
        if ($block['type'] !== 'table' || !is_array($block['columns'] ?? null) || !is_array($block['rows'] ?? null)) {
            continue;
        }
        $columns = array_values(array_filter($block['columns'], 'is_string'));
        if ($columns === []) {
            continue;
        }
        $html .= '<div class="rule-table-wrapper"><table class="rule-table">';
        if (is_string($block['caption'] ?? null) && $block['caption'] !== '') {
            $html .= '<caption>' . e($block['caption']) . '</caption>';
        }
        $html .= '<thead><tr>';
        foreach ($columns as $column) {
            $html .= '<th scope="col">' . e($column) . '</th>';
        }
        $html .= '</tr></thead><tbody>';
        foreach ($block['rows'] as $row) {
            if (!is_array($row) || count($row) !== count($columns)) {
                continue;
            }
            $html .= '<tr>';
            foreach ($row as $cell) {
                $html .= '<td>' . e((string) $cell) . '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</tbody></table></div>';
    }

    return $html !== '' ? $html : '<p>' . ruleContentWithAbbreviations((string) ($rule['content'] ?? ''), $abbreviations) . '</p>';
}

function monsterDescriptionFeatures(string $content): array
{
    preg_match_all('/<b>(.*?)<\/b>(.*?)(?=<b>|$)/su', $content, $matches, PREG_SET_ORDER);

    return array_values(array_filter(array_map(
        static function (array $match): array {
            $label = trim(strip_tags($match[1]));
            $description = preg_replace('/\s+/u', ' ', trim(strip_tags($match[2]))) ?? '';

            return [
                'label' => rtrim($label, '.'),
                'description' => $description
            ];
        },
        $matches
    ), static fn (array $feature): bool => $feature['label'] !== ''));
}

function monsterAbilityStats(array $abilities): array
{
    $stats = [];
    foreach ($abilities as $ability) {
        if (preg_match('/^(\S+)\s+(-?\d+)\s+\(([-+−]?\d+)\/([-+−]?\d+)\)$/u', trim((string) $ability), $matches) !== 1) {
            continue;
        }
        $stats[] = [
            'name' => $matches[1],
            'score' => $matches[2],
            'modifier' => str_replace('−', '-', $matches[3]),
            'save' => str_replace('−', '-', $matches[4])
        ];
    }
    return $stats;
}

function monsterDescriptionColor(string $title): string
{
    return match (mb_strtolower($title, 'UTF-8')) {
        'tratti' => 'hsl(12, 72%, 50%)',
        'azioni' => 'hsl(350, 68%, 52%)',
        'azioni bonus' => 'hsl(28, 78%, 48%)',
        'azioni leggendarie' => 'hsl(276, 58%, 52%)',
        'reazioni' => 'hsl(198, 66%, 44%)',
        'reazioni leggendarie' => 'hsl(165, 58%, 40%)',
        default => 'hsl(44, 68%, 45%)'
    };
}

function normalizeMonsterDescriptionBlocks(array $monster): array
{
    foreach ($monster['description_blocks'] as &$block) {
        if ($monster['name'] !== 'Aboleth' || $block['title'] !== 'Azioni leggendarie') {
            continue;
        }

        $block['content'] = '<div><b>Utilizzi di azioni leggendarie: 3 (4 nella tana).</b> Subito dopo il turno di un\'altra creatura, l\'aboleth può consumare un utilizzo per effettuare una delle seguenti azioni. L\'aboleth recupera tutti gli utilizzi consumati all\'inizio di ogni suo turno.</div>'
            . '<div><b>Risucchio psichico.</b> Se l\'aboleth ha affascinato o afferrato almeno una creatura, utilizza Consuma ricordi e recupera 5 (1d10) punti ferita.</div>'
            . '<div><b>Sferzata.</b> L\'aboleth effettua un attacco Tentacolo.</div>';
    }
    unset($block);

    return $monster['description_blocks'];
}

function appUrl(string $path = ''): string
{
    global $basePath;

    return $basePath . '/' . ltrim($path, '/');
}

function exportNumber(string|int|float|null $value): int|float
{
    $text = trim((string) $value);
    if ($text === '' || $text === '—' || $text === '-') {
        return 0;
    }

    if (str_contains($text, ',') || !preg_match('/^\d{1,3}(?:\.\d{3})+/', $text)) {
        $text = str_replace(',', '.', $text);
    } else {
        $text = str_replace('.', '', $text);
    }
    preg_match('/-?\d+(?:\.\d+)?/', $text, $matches);
    $number = (float) ($matches[0] ?? 0);

    return fmod($number, 1.0) === 0.0 ? (int) $number : $number;
}

function exportCurrencyValue(string|int|float|null $value): int|float
{
    return exportNumber($value);
}

/**
 * Converts an Italian SRD currency abbreviation to Make a Master currency.
 *
 * @param string|int|float|null $value Cost containing an optional currency abbreviation.
 *
 * @return string Make a Master currency identifier.
 */
function exportCurrency(string|int|float|null $value): string
{
    $text = strtolower(trim((string) $value));

    return match (true) {
        str_contains($text, 'mr') => 'bronze',
        str_contains($text, 'ma') => 'silver',
        str_contains($text, 'mo') => 'gold',
        default => 'gold'
    };
}

/**
 * Builds the Make a Master export payload for a catalog object.
 *
 * @param string $title Object title.
 * @param string|int|float|null $value Object cost, including its currency abbreviation.
 * @param string|int|float|null $weight Object weight.
 * @param string|null $description Optional formatted object description.
 * @param string|null $attack Optional attack or damage value.
 * @param string|null $range Optional attack range.
 *
 * @return array<string, mixed> Export payload accepted by Make a Master.
 */
function buildObjectExportData(
    string $title,
    string|int|float|null $value,
    string|int|float|null $weight,
    ?string $description = null,
    ?string $attack = null,
    ?string $range = null
): array {
    return [
        'type' => 'oggetti',
        'title' => $title,
        'desc' => $description ?? '',
        'image' => '',
        'valore' => exportCurrencyValue($value),
        'currency' => exportCurrency($value),
        'weight' => exportNumber($weight),
        'rarity' => 'comune',
        'attack' => $attack ?? '',
        'range' => $range ?? '',
        'attackBonus' => '0',
        'folderId' => null,
        'tab' => 'oggetti',
        'id' => '',
        'createdAt' => 0,
        'cardColor' => null
    ];
}

function buildSpellExportData(array $spell): array
{
    $duration = $spell['duration'];
    if ($spell['concentration']) {
        $duration = preg_replace('/^concentrazione,\s*/iu', '', $duration) ?? $duration;
    }

    $range = preg_replace('/\s+metri$/iu', 'm', $spell['range']) ?? $spell['range'];
    $description = '<i>Scuola: ' . $spell['school'] . "\n</i>";
    $description .= '<b>Classi: ' . implode(' · ', array_map('ucfirst', $spell['classes'])) . "\n</b>";
    $description .= $spell['description'];
    if ($spell['higher_level'] !== '') {
        $description .= "\n<b>A slot superiore: " . $spell['higher_level'] . "\n</b>";
    }
    $description .= 'Tempo: ' . ucfirst($spell['casting_time']) . "\n";
    $description .= 'Componenti: ' . $spell['components'] . '<br>';

    return [
        'type' => 'armi',
        'title' => $spell['name'] . ' (' . $spell['level'] . ')',
        'range' => $range,
        'rangeNumber' => $spell['area_diameter'],
        'duration' => $duration,
        'damage' => $spell['damage'],
        'concentration' => $spell['concentration'] ? 'true' : 'false',
        'image' => '',
        'desc' => $description,
        'folderId' => null,
        'tab' => 'armi',
        'id' => '',
        'createdAt' => 0
    ];
}

function buildMonsterExportData(array $monster): array
{
    $initiativeText = str_replace('−', '-', (string) $monster['initiative']);
    $initiative = preg_match('/[+-]?\d+/', $initiativeText, $initiativeMatch)
        ? $initiativeMatch[0]
        : '0';
    $speed = preg_replace('/\s*m\b/u', 'm', (string) $monster['speed']) ?? (string) $monster['speed'];
    $maxHp = preg_match('/^(\d+)(?:\s+\([^)]+\))?$/u', (string) $monster['hit_points'], $hitPointsMatch) === 1
        ? $hitPointsMatch[1]
        : (string) $monster['hit_points'];
    $extraStats = $monster['extra_stats'];
    if (preg_match('/^\d+\s+\(([^)]+)\)$/u', (string) $monster['hit_points'], $hitDiceMatch) === 1) {
        $extraStats[] = [
            'title' => 'DV',
            'value' => $hitDiceMatch[1]
        ];
    }

    return [
        'type' => 'png',
        'title' => $monster['name'],
        'desc' => '',
        'image' => '',
        'specie' => $monster['type'],
        'maxHp' => $maxHp,
        'ac' => $monster['armor_class'],
        'initMod' => ltrim($initiative, '+'),
        'speed' => $speed,
        'stats' => implode(' | ', $monster['abilities']),
        'attacks' => null,
        'spells' => null,
        'equip' => null,
        'descBlocks' => array_map(
            static fn (array $block): array => [
                'id' => '',
                'title' => $block['title'],
                'color' => monsterDescriptionColor($block['title']),
                'content' => $block['content']
            ],
            $monster['description_blocks']
        ),
        'extraStats' => array_map(
            static fn (array $stat): array => [
                'id' => '',
                'title' => $stat['title'],
                'value' => $stat['value']
            ],
            $extraStats
        ),
        'folderId' => null,
        'tab' => 'png',
        'id' => '',
        'createdAt' => 0
    ];
}
