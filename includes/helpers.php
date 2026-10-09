<?php

declare(strict_types=1);

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
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
    $text = strtolower(trim((string) $value));
    $number = (float) exportNumber($text);
    if (str_contains($text, 'mr')) {
        $number /= 100;
    } elseif (str_contains($text, 'ma')) {
        $number /= 10;
    }

    return fmod($number, 1.0) === 0.0 ? (int) $number : $number;
}

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
