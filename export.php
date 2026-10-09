<?php

declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Metodo non consentito.';
    exit;
}

$allResults = filter_var($_POST['exportAll'] ?? false, FILTER_VALIDATE_BOOLEAN);
if ($allResults) {
    $route = (string) ($_POST['route'] ?? '');
    $request = json_decode((string) ($_POST['request'] ?? '{}'), true);
    if (!is_array($request)) {
        http_response_code(400);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['error' => 'Filtri non validi.']);
        exit;
    }

    $datasetStore = new JsonDatasetStore(new JsonFileImporter(), __DIR__ . '/data');
    $catalog = new DataCatalog(
        store: $datasetStore,
        weaponProvider: new JsonWeaponProvider($datasetStore),
        armorProvider: new JsonArmorProvider($datasetStore),
        ammunitionProvider: new JsonAmmunitionProvider($datasetStore),
        toolsProvider: new JsonToolsProvider($datasetStore),
        adventuringGearProvider: new JsonAdventuringGearProvider($datasetStore),
        spellProvider: new JsonSpellProvider($datasetStore),
        monsterProvider: new JsonMonsterProvider($datasetStore)
    );

    $items = match ($route) {
        'oggetti/armi' => array_map(
            static function (array $item): array {
                $range = 'Corpo a corpo';
                if ($item['mode'] === 'A distanza') {
                    $range = 'A distanza';
                    foreach ($item['properties'] as $property) {
                        if (str_contains($property, 'Munizioni (') || str_contains($property, 'Lancio (')) {
                            $range = (string) preg_replace('/\bgittata\s+/u', '', $property);
                            break;
                        }
                    }
                }
                $description = $item['properties']
                    ? '<div><b>Proprietà:</b> ' . implode(' · ', $item['properties']) . '</div>'
                    : '';
                $description .= '<div><b>Padronanza:</b> ' . $item['mastery'] . '</div>';

                return buildObjectExportData(
                    $item['name'],
                    $item['cost'],
                    $item['weight'],
                    $description,
                    $item['damage'],
                    $range
                );
            },
            $catalog->getWeapons(new WeaponQuery(
                category: (string) ($request['categoria'] ?? 'items-weapons'),
                search: (string) ($request['q'] ?? ''),
                pageSize: null
            ))->items
        ),
        'oggetti/armature' => array_map(
            static function (array $item) use ($catalog): array {
                $armorData = $catalog->armor();
                $description = '<div><b>CA: ' . $item['armor_class'] . '</b></div>';
                if ($item['strength_requirement'] !== null) {
                    $description .= '<div>Forza minima: ' . $item['strength_requirement'] . '</div>';
                }
                $description .= $item['stealth_disadvantage']
                    ? '<div>Svantaggio a Furtività</div>'
                    : '<div>Furtività normale</div>';
                $description .= '<div>Indossare: ' . $armorData['donning'][$item['armor_type']]['wear']
                    . ' · Togliere: ' . $armorData['donning'][$item['armor_type']]['remove'] . '</div>';

                return buildObjectExportData($item['name'], $item['cost'], $item['weight'], $description);
            },
            $catalog->getArmor(new ArmorQuery(
                category: (string) ($request['categoria'] ?? 'armor'),
                search: (string) ($request['q'] ?? ''),
                pageSize: null
            ))->items
        ),
        'oggetti/munizioni' => array_map(
            static fn (array $item): array => buildObjectExportData(
                $item['name'],
                $item['cost'],
                $item['weight'],
                '<div><b>Quantità per acquisto:</b> ' . $item['quantity'] . '</div>'
                    . '<div><b>Armi compatibili:</b> ' . implode(' · ', $item['compatible_weapons']) . '</div>'
            ),
            $catalog->getAmmunition(new AmmunitionQuery(
                search: (string) ($request['q'] ?? ''),
                pageSize: null
            ))->items
        ),
        'oggetti/strumenti' => array_map(
            static function (array $item): array {
                $description = '<div><b>Utilizzo:</b> ' . $item['use'] . '</div>';
                $description .= '<div><b>Caratteristica:</b> ' . $item['ability'] . '</div>';
                if (!empty($item['creation'])) {
                    $description .= '<div><b>Creazione:</b> ' . $item['creation'] . '</div>';
                }
                if (!empty($item['variants'])) {
                    $description .= '<div><b>Varianti:</b> ' . $item['variants'] . '</div>';
                }

                return buildObjectExportData($item['name'], $item['cost'], $item['weight'], $description);
            },
            $catalog->getTools(new ToolsQuery(
                category: (string) ($request['categoria'] ?? 'tools'),
                search: (string) ($request['q'] ?? ''),
                pageSize: null
            ))->items
        ),
        'oggetti/avventura' => array_map(
            static fn (array $item): array => buildObjectExportData(
                $item['name'],
                $item['cost'],
                $item['weight'],
                '<div><b>Utilizzo:</b> ' . $item['use'] . '</div>'
            ),
            $catalog->getAdventuringGear(new AdventuringGearQuery(
                category: (string) ($request['categoria'] ?? 'gear'),
                search: (string) ($request['q'] ?? ''),
                pageSize: null
            ))->items
        ),
        'incantesimi' => array_map(
            static fn (array $item): array => buildSpellExportData($item),
            $catalog->getSpells(new SpellQuery(
                level: isset($request['livello']) && $request['livello'] !== 'all'
                    ? (int) $request['livello']
                    : null,
                school: (string) ($request['scuola'] ?? ''),
                className: (string) ($request['classe'] ?? ''),
                search: (string) ($request['q'] ?? ''),
                pageSize: null
            ))->items
        ),
        'mostri' => array_map(
            static fn (array $item): array => buildMonsterExportData($item),
            $catalog->getMonsters(new MonsterQuery(
                type: (string) ($request['tipo'] ?? ''),
                size: (string) ($request['taglia'] ?? ''),
                challengeRating: (string) ($request['gs'] ?? ''),
                alignment: (string) ($request['allineamento'] ?? ''),
                search: (string) ($request['q'] ?? ''),
                pageSize: null
            ))->items
        ),
        default => []
    };

    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(['items' => $items], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    exit;
}

$itemsJson = $_POST['items'] ?? ($_POST['item'] ?? '');
$items = json_decode($itemsJson, true);
if (isset($items['title'])) {
    $items = [$items];
}
if (!is_array($items) || $items === []) {
    http_response_code(400);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Dati oggetti non validi.';
    exit;
}
foreach ($items as $item) {
    if (!is_array($item) || !isset($item['title']) || !is_string($item['title']) || trim($item['title']) === '') {
        http_response_code(400);
        header('Content-Type: text/plain; charset=UTF-8');
        echo 'Dati oggetto non validi.';
        exit;
    }
}

$folderId = trim((string) ($_POST['folderId'] ?? ''));
$cardColor = trim((string) ($_POST['cardColor'] ?? ''));
if ($folderId !== '' && !preg_match('/^[A-Za-z0-9_-]+$/', $folderId)) {
    http_response_code(400);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'ID cartella non valido.';
    exit;
}
if ($cardColor !== '' && !preg_match('/^#[0-9a-fA-F]{6}$/', $cardColor)) {
    http_response_code(400);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Colore non valido.';
    exit;
}

foreach ($items as &$item) {
    $item['folderId'] = $folderId === '' ? null : $folderId;
    if ($cardColor === '') {
        unset($item['cardColor']);
    } else {
        $item['cardColor'] = strtolower($cardColor);
    }
    if (($item['type'] ?? '') === 'oggetti') {
        $item['rarity'] = $item['rarity'] ?? 'comune';
    }
    $item['id'] = 'srd_' . bin2hex(random_bytes(8));
    $item['createdAt'] = (int) floor(microtime(true) * 1000);
    if (($item['type'] ?? '') === 'png') {
        foreach (['descBlocks', 'extraStats'] as $collection) {
            if (!isset($item[$collection]) || !is_array($item[$collection])) {
                continue;
            }
            foreach ($item[$collection] as &$entry) {
                if (is_array($entry)) {
                    $entry['id'] = 'srd_' . bin2hex(random_bytes(8));
                }
            }
            unset($entry);
        }
    }
}
unset($item);

$zip = new ZipArchive();
$temporaryZip = tempnam(sys_get_temp_dir(), 'srd-export-');
if ($temporaryZip === false || $zip->open($temporaryZip, ZipArchive::OVERWRITE) !== true) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Impossibile creare il file ZIP.';
    exit;
}

$json = json_encode(['items' => $items], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
$zip->addFromString('data.json', $json);
$zip->close();

$safeName = count($items) === 1 ? $items[0]['title'] : (($items[0]['type'] ?? '') === 'armi' ? 'incantesimi' : 'oggetti');
$safeName = preg_replace('/[^A-Za-z0-9À-ÿ._-]+/u', '-', $safeName);
$safeName = trim((string) $safeName, '-.') ?: 'oggetti';
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . $safeName . '.zip"');
header('Content-Length: ' . (string) filesize($temporaryZip));
readfile($temporaryZip);
unlink($temporaryZip);
