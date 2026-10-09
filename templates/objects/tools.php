<?php
$toolTypeLabels = [
    'artigiano' => 'Strumenti da artigiano',
    'altri' => 'Altri strumenti',
    'musicali' => 'Strumenti musicali',
    'giochi' => 'Giochi'
];
?>
<section class="results-view tools-list-view">
    <div class="breadcrumb"><a class="breadcrumb-home" href="<?= e(appUrl('oggetti')) ?>">Oggetti</a><span>/</span><a href="<?= e(appUrl('oggetti/strumenti')) ?>">Strumenti</a><span>/</span><strong><?= e($pageTitle) ?></strong></div>
    <div class="section-heading"><div><h1><?= e($pageTitle) ?></h1></div></div>
    <?php require __DIR__ . '/search.php'; ?>
    <nav class="taxonomy-filters" aria-label="Categorie strumenti">
<?php foreach ($categoryLabels as $category => $label): ?>
        <a class="taxonomy-filter <?= $category === $selectedCategory ? 'is-selected' : '' ?>" href="<?= e(appUrl('oggetti/strumenti?categoria=' . urlencode($category))) ?>"><?= e($label) ?></a>
<?php endforeach; ?>
    </nav>
    <div class="result-summary">Mostrati <strong id="result-count"><?= count($tools) ?></strong> di <strong><?= e((string) $pagination->totalItems) ?></strong> elementi</div>
    <div id="results-grid" class="results-grid">
<?php foreach ($tools as $tool): ?>
<?php
    $toolDescription = '<div><b>Utilizzo:</b> ' . $tool['use'] . '</div>';
    $toolDescription .= '<div><b>Caratteristica:</b> ' . $tool['ability'] . '</div>';
    if (!empty($tool['creation'])) {
        $toolDescription .= '<div><b>Creazione:</b> ' . $tool['creation'] . '</div>';
    }
    if (!empty($tool['variants'])) {
        $toolDescription .= '<div><b>Varianti:</b> ' . $tool['variants'] . '</div>';
    }
    $exportData = buildObjectExportData($tool['name'], $tool['cost'], $tool['weight'], $toolDescription);
?>
        <article class="result-card tool-card" data-export="<?= e(json_encode($exportData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)) ?>" data-sort-name="<?= e($tool['name']) ?>" data-sort-category="<?= e($toolTypeLabels[$tool['tool_type']]) ?>" data-sort-weight="<?= e($tool['weight']) ?>" data-sort-value="<?= e($tool['cost']) ?>" data-search="<?= e(implode(' ', [$tool['name'], $tool['tool_type'], $tool['ability'], $tool['weight'], $tool['cost'], $tool['use'], $tool['creation'] ?? '', $tool['variants'] ?? ''])) ?>">
            <div class="weapon-category"><span class="weapon-category-main">Strumenti</span><span class="weapon-category-arrow">→</span><span><?= e($toolTypeLabels[$tool['tool_type']]) ?></span></div>
            <div class="weapon-heading"><h3><?= e($tool['name']) ?></h3><span class="weapon-mode">Strumento</span></div>
            <div class="tool-use"><span class="armor-icon" aria-hidden="true">🔧</span><div><span class="weapon-label">Utilizzo</span><span><?= e($tool['use']) ?></span></div></div>
            <div class="weapon-details">
                <div class="weapon-detail"><span class="weapon-icon" aria-hidden="true">🧠</span><div><span class="weapon-label">Caratteristica</span><span class="weapon-value"><?= e($tool['ability']) ?></span></div></div>
                <div class="weapon-detail"><span class="weapon-icon" aria-hidden="true">⚖️</span><div><span class="weapon-label">Peso</span><span class="weapon-value"><?= e($tool['weight']) ?></span></div></div>
                <div class="weapon-detail"><span class="weapon-icon" aria-hidden="true">🪙</span><div><span class="weapon-label">Valore</span><span class="weapon-value"><?= e($tool['cost']) ?></span></div></div>
<?php if (!empty($tool['creation'])): ?>
                <div class="weapon-detail"><span class="weapon-icon" aria-hidden="true">🛠️</span><div><span class="weapon-label">Creazione</span><span class="weapon-value"><?= e($tool['creation']) ?></span></div></div>
<?php endif; ?>
<?php if (!empty($tool['variants'])): ?>
                <div class="weapon-detail"><span class="weapon-icon" aria-hidden="true">🔀</span><div><span class="weapon-label">Varianti</span><span class="weapon-value"><?= e($tool['variants']) ?></span></div></div>
<?php endif; ?>
            </div>
            <button class="export-button" type="button">⇩ Esporta</button>
        </article>
<?php endforeach; ?>
    </div>
    <div id="empty-state" class="empty-state" hidden><span class="empty-icon">⌕</span><h3>Nessun risultato</h3><p>Prova a modificare la ricerca.</p></div>
</section>
<?php require __DIR__ . '/../partials/pagination.php'; ?>
