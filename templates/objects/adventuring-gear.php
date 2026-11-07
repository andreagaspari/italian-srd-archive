<?php
$gearCategoryLabels = [
    'avventura' => 'Avventura',
    'consumabili' => 'Consumabili',
    'contenitori' => 'Contenitori',
    'illuminazione' => 'Illuminazione'
];
?>
<section class="results-view container-fluid px-0">
    <div class="breadcrumb d-flex flex-wrap gap-2 mb-4 text-body-secondary small"><a class="breadcrumb-home" href="<?= e(appUrl('oggetti')) ?>">Oggetti</a><span>/</span><a href="<?= e(appUrl('oggetti/avventura')) ?>">Equipaggiamento</a><span>/</span><strong><?= e($pageTitle) ?></strong></div>
    <div class="section-heading d-flex justify-content-between align-items-end mb-3"><div><h1><?= e($pageTitle) ?></h1></div></div>
    <?php require __DIR__ . '/search.php'; ?>
    <nav class="taxonomy-filters d-flex flex-wrap gap-2 mb-3" aria-label="Categorie equipaggiamento">
<?php foreach ($categoryLabels as $category => $label): ?>
        <a class="taxonomy-filter btn btn-sm btn-outline-secondary <?= $category === $selectedCategory ? 'is-selected' : '' ?>" href="<?= e(appUrl('oggetti/avventura?categoria=' . urlencode($category))) ?>"><?= e($label) ?></a>
<?php endforeach; ?>
    </nav>
<?php $paginationPosition = 'top'; require __DIR__ . '/../partials/pagination.php'; ?>
    <div id="results-grid" class="results-grid row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4 align-items-start">
<?php foreach ($adventuringGear as $item): ?>
<?php $exportData = buildObjectExportData($item['name'], $item['cost'], $item['weight'], '<div><b>Utilizzo:</b> ' . $item['use'] . '</div>'); ?>
        <div class="col">
        <article class="result-card card position-relative" data-favorite-dataset="adventuring-gear" data-favorite-id="<?= e($item['id']) ?>" data-export="<?= e(json_encode($exportData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)) ?>" data-sort-name="<?= e($item['name']) ?>" data-sort-category="<?= e($gearCategoryLabels[$item['category']] ?? $item['category']) ?>" data-sort-weight="<?= e($item['weight']) ?>" data-sort-value="<?= e($item['cost']) ?>" data-search="<?= e(implode(' ', [$item['name'], $item['category'], $item['weight'], $item['cost'], $item['use']])) ?>">
            <div class="card-header d-flex align-items-start justify-content-between gap-3">
                <div>
                    <div class="weapon-category d-flex align-items-center gap-2 mb-1">
                        <a class="weapon-category-main" href="<?= e(appUrl('oggetti/avventura')) ?>">Equipaggiamento</a>
                        <span class="weapon-category-arrow">→</span>
                        <a class="weapon-category-subcategory" href="<?= e(appUrl('oggetti/avventura?categoria=' . urlencode($item['category']))) ?>"><?= e($gearCategoryLabels[$item['category']] ?? $item['category']) ?></a>
                    </div>
                    <h3><?= e($item['name']) ?></h3>
                </div>
                <span class="weapon-mode">Oggetto</span>
            </div>
            <div class="card-body">
            <div class="gear-use-summary mb-3">
                <div class="gear-summary-values float-end d-flex align-items-center gap-3 ms-3">
                    <div class="weapon-summary-detail d-flex align-items-center gap-1" title="Peso">
                        <span class="weapon-icon" aria-hidden="true">⚖️</span>
                        <span class="weapon-value"><?= e($item['weight']) ?></span>
                    </div>
                    <div class="weapon-summary-detail d-flex align-items-center gap-1" title="Valore">
                        <span class="weapon-icon" aria-hidden="true">🪙</span>
                        <span class="weapon-value"><?= e($item['cost']) ?></span>
                    </div>
                </div>
                <div class="gear-use-description">
                    <div class="gear-use-label">
                        <span class="armor-icon me-1" aria-hidden="true">🎒</span>
                        <strong>Utilizzo</strong>
                    </div>
                    <div><?= e($item['use']) ?></div>
                </div>
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
