<?php

declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Metodo non consentito.';
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
