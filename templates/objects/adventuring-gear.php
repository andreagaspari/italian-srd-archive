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
    <div id="results-grid" class="results-grid row row-cols-1 row-cols-md-2 row-cols-xl-3 g-3">
<?php foreach ($adventuringGear as $item): ?>
<?php $exportData = buildObjectExportData($item['name'], $item['cost'], $item['weight'], '<div><b>Utilizzo:</b> ' . $item['use'] . '</div>'); ?>
        <article class="result-card card col h-100 position-relative" data-favorite-dataset="adventuring-gear" data-favorite-id="<?= e($item['id']) ?>" data-export="<?= e(json_encode($exportData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)) ?>" data-sort-name="<?= e($item['name']) ?>" data-sort-category="<?= e($gearCategoryLabels[$item['category']] ?? $item['category']) ?>" data-sort-weight="<?= e($item['weight']) ?>" data-sort-value="<?= e($item['cost']) ?>" data-search="<?= e(implode(' ', [$item['name'], $item['category'], $item['weight'], $item['cost'], $item['use']])) ?>">
            <div class="card-body">
            <div class="weapon-category"><span class="weapon-category-main">Equipaggiamento</span><span class="weapon-category-arrow">→</span><span><?= e($gearCategoryLabels[$item['category']] ?? $item['category']) ?></span></div>
            <div class="weapon-heading d-flex align-items-start justify-content-between gap-3 bg-body-tertiary border-bottom px-3 py-2 mx-n3 mt-n3 mb-3"><h3><?= e($item['name']) ?></h3><span class="weapon-mode">Oggetto</span></div>
            <div class="tool-use"><span class="armor-icon" aria-hidden="true">🎒</span><div><span class="weapon-label">Utilizzo</span><span><?= e($item['use']) ?></span></div></div>
            <div class="weapon-details">
                <div class="weapon-detail"><span class="weapon-icon" aria-hidden="true">⚖️</span><div><span class="weapon-label">Peso</span><span class="weapon-value"><?= e($item['weight']) ?></span></div></div>
                <div class="weapon-detail"><span class="weapon-icon" aria-hidden="true">🪙</span><div><span class="weapon-label">Valore</span><span class="weapon-value"><?= e($item['cost']) ?></span></div></div>
            </div>
            </div>
            <div class="card-footer bg-transparent border-0 d-flex justify-content-between align-items-center px-3 pb-3">
                <button class="export-button btn btn-sm btn-outline-primary position-static" type="button">⇩ Esporta</button>
            </div>
        </article>
<?php endforeach; ?>
    </div>
    <div id="empty-state" class="empty-state text-center p-5" hidden><span class="empty-icon">⌕</span><h3>Nessun risultato</h3><p>Prova a modificare la ricerca.</p></div>
</section>
<?php $paginationPosition = 'bottom'; require __DIR__ . '/../partials/pagination.php'; ?>
