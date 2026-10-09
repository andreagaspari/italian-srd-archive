<?php
$gearCategoryLabels = [
    'avventura' => 'Avventura',
    'consumabili' => 'Consumabili',
    'contenitori' => 'Contenitori',
    'illuminazione' => 'Illuminazione'
];
?>
<section class="results-view">
    <div class="breadcrumb"><a class="breadcrumb-home" href="<?= e(appUrl('oggetti')) ?>">Oggetti</a><span>/</span><a href="<?= e(appUrl('oggetti/avventura')) ?>">Equipaggiamento</a><span>/</span><strong><?= e($pageTitle) ?></strong></div>
    <div class="section-heading"><div><h1><?= e($pageTitle) ?></h1></div></div>
    <?php require __DIR__ . '/search.php'; ?>
    <nav class="taxonomy-filters" aria-label="Categorie equipaggiamento">
<?php foreach ($categoryLabels as $category => $label): ?>
        <a class="taxonomy-filter <?= $category === $selectedCategory ? 'is-selected' : '' ?>" href="<?= e(appUrl('oggetti/avventura?categoria=' . urlencode($category))) ?>"><?= e($label) ?></a>
<?php endforeach; ?>
    </nav>
    <div class="result-summary">Mostrati <strong id="result-count"><?= count($adventuringGear) ?></strong> di <strong><?= e((string) $pagination->totalItems) ?></strong> elementi</div>
    <div id="results-grid" class="results-grid">
<?php foreach ($adventuringGear as $item): ?>
<?php $exportData = buildObjectExportData($item['name'], $item['cost'], $item['weight'], '<div><b>Utilizzo:</b> ' . $item['use'] . '</div>'); ?>
        <article class="result-card" data-export="<?= e(json_encode($exportData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)) ?>" data-sort-name="<?= e($item['name']) ?>" data-sort-category="<?= e($gearCategoryLabels[$item['category']] ?? $item['category']) ?>" data-sort-weight="<?= e($item['weight']) ?>" data-sort-value="<?= e($item['cost']) ?>" data-search="<?= e(implode(' ', [$item['name'], $item['category'], $item['weight'], $item['cost'], $item['use']])) ?>">
            <div class="weapon-category"><span class="weapon-category-main">Equipaggiamento</span><span class="weapon-category-arrow">→</span><span><?= e($gearCategoryLabels[$item['category']] ?? $item['category']) ?></span></div>
            <div class="weapon-heading"><h3><?= e($item['name']) ?></h3><span class="weapon-mode">Oggetto</span></div>
            <div class="tool-use"><span class="armor-icon" aria-hidden="true">🎒</span><div><span class="weapon-label">Utilizzo</span><span><?= e($item['use']) ?></span></div></div>
            <div class="weapon-details">
                <div class="weapon-detail"><span class="weapon-icon" aria-hidden="true">⚖️</span><div><span class="weapon-label">Peso</span><span class="weapon-value"><?= e($item['weight']) ?></span></div></div>
                <div class="weapon-detail"><span class="weapon-icon" aria-hidden="true">🪙</span><div><span class="weapon-label">Valore</span><span class="weapon-value"><?= e($item['cost']) ?></span></div></div>
            </div>
            <button class="export-button" type="button">⇩ Esporta</button>
        </article>
<?php endforeach; ?>
    </div>
    <div id="empty-state" class="empty-state" hidden><span class="empty-icon">⌕</span><h3>Nessun risultato</h3><p>Prova a modificare la ricerca.</p></div>
</section>
<?php require __DIR__ . '/../partials/pagination.php'; ?>
