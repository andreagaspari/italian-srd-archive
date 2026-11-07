<?php
$toolTypeLabels = [
    'artigiano' => 'Strumenti da artigiano',
    'altri' => 'Altri strumenti',
    'musicali' => 'Strumenti musicali',
    'giochi' => 'Giochi'
];
?>
<section class="results-view tools-list-view container-fluid px-0">
    <div class="breadcrumb d-flex flex-wrap gap-2 mb-4 text-body-secondary small"><a class="breadcrumb-home" href="<?= e(appUrl('oggetti')) ?>">Oggetti</a><span>/</span><a href="<?= e(appUrl('oggetti/strumenti')) ?>">Strumenti</a><span>/</span><strong><?= e($pageTitle) ?></strong></div>
    <div class="section-heading d-flex justify-content-between align-items-end mb-3"><div><h1><?= e($pageTitle) ?></h1></div></div>
    <?php require __DIR__ . '/search.php'; ?>
    <nav class="taxonomy-filters d-flex flex-wrap gap-2 mb-3" aria-label="Categorie strumenti">
<?php foreach ($categoryLabels as $category => $label): ?>
        <a class="taxonomy-filter btn btn-sm btn-outline-secondary <?= $category === $selectedCategory ? 'is-selected' : '' ?>" href="<?= e(appUrl('oggetti/strumenti?categoria=' . urlencode($category))) ?>"><?= e($label) ?></a>
<?php endforeach; ?>
    </nav>
<?php $paginationPosition = 'top'; require __DIR__ . '/../partials/pagination.php'; ?>
    <div id="results-grid" class="results-grid row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4 align-items-start">
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
        <div class="col">
        <article class="result-card tool-card card position-relative" data-favorite-dataset="tools" data-favorite-id="<?= e($tool['id']) ?>" data-export="<?= e(json_encode($exportData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)) ?>" data-sort-name="<?= e($tool['name']) ?>" data-sort-category="<?= e($toolTypeLabels[$tool['tool_type']]) ?>" data-sort-weight="<?= e($tool['weight']) ?>" data-sort-value="<?= e($tool['cost']) ?>" data-search="<?= e(implode(' ', [$tool['name'], $tool['tool_type'], $tool['ability'], $tool['weight'], $tool['cost'], $tool['use'], $tool['creation'] ?? '', $tool['variants'] ?? ''])) ?>">
            <div class="card-header d-flex align-items-start justify-content-between gap-3">
                <div>
                    <div class="weapon-category d-flex align-items-center gap-2 mb-1">
                        <a class="weapon-category-main" href="<?= e(appUrl('oggetti/strumenti')) ?>">Strumenti</a>
                        <span class="weapon-category-arrow">→</span>
                        <a class="weapon-category-subcategory" href="<?= e(appUrl('oggetti/strumenti?categoria=' . urlencode($tool['tool_type']))) ?>"><?= e($toolTypeLabels[$tool['tool_type']]) ?></a>
                    </div>
                    <h3><?= e($tool['name']) ?></h3>
                </div>
                <span class="weapon-mode">Strumento</span>
            </div>
            <div class="card-body">
            <div class="weapon-summary d-flex align-items-center gap-3 mb-3">
                <div class="d-flex align-items-center gap-1" title="Caratteristica">
                    <span class="weapon-icon" aria-hidden="true">🧠</span>
                    <strong><?= e($tool['ability']) ?></strong>
                </div>
                <div class="weapon-summary-detail d-flex align-items-center gap-1 ms-auto" title="Peso">
                    <span class="weapon-icon" aria-hidden="true">⚖️</span>
                    <span class="weapon-value"><?= e($tool['weight']) ?></span>
                </div>
                <div class="weapon-summary-detail d-flex align-items-center gap-1" title="Valore">
                    <span class="weapon-icon" aria-hidden="true">🪙</span>
                    <span class="weapon-value"><?= e($tool['cost']) ?></span>
                </div>
            </div>
            <div class="row row-cols-1 g-2">
                <div class="col weapon-detail"><span class="weapon-icon" aria-hidden="true">🔧</span><div><span class="weapon-label">Utilizzo</span><span class="weapon-value"><?= e($tool['use']) ?></span></div></div>
<?php if (!empty($tool['creation'])): ?>
                <div class="col weapon-detail"><span class="weapon-icon" aria-hidden="true">🛠️</span><div><span class="weapon-label">Creazione</span><span class="weapon-value"><?= e($tool['creation']) ?></span></div></div>
<?php endif; ?>
<?php if (!empty($tool['variants'])): ?>
                <div class="col weapon-detail"><span class="weapon-icon" aria-hidden="true">🔀</span><div><span class="weapon-label">Varianti</span><span class="weapon-value"><?= e($tool['variants']) ?></span></div></div>
<?php endif; ?>
            </div>
            </div>
            <div class="card-footer bg-transparent border-0 d-flex justify-content-between align-items-center px-3 pb-3">
                <button class="export-button btn btn-sm btn-outline-primary position-static" type="button">⇩ Esporta</button>
            </div>
        </article>
        </div>
<?php endforeach; ?>
    </div>
    <div id="empty-state" class="empty-state text-center p-5" hidden><span class="empty-icon">⌕</span><h3>Nessun risultato</h3><p>Prova a modificare la ricerca.</p></div>
</section>
<?php $paginationPosition = 'bottom'; require __DIR__ . '/../partials/pagination.php'; ?>
