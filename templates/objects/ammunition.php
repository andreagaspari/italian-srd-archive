<section class="results-view ammunition-list-view container-fluid px-0">
    <div class="breadcrumb d-flex flex-wrap gap-2 mb-4 text-body-secondary small">
        <a class="breadcrumb-home" href="<?= e(appUrl('oggetti')) ?>">Oggetti</a>
        <span>/</span>
        <strong>Munizioni</strong>
    </div>

    <div class="section-heading d-flex justify-content-between align-items-end mb-3">
        <div>
            <h1>Munizioni</h1>
        </div>
    </div>

    <?php require __DIR__ . '/search.php'; ?>

<?php $paginationPosition = 'top'; require __DIR__ . '/../partials/pagination.php'; ?>

    <div id="results-grid" class="results-grid row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4 align-items-start">
<?php foreach ($ammunition as $item): ?>
<?php
    $exportDescription = '<div><b>Quantità per acquisto:</b> ' . $item['quantity'] . '</div>';
    $exportDescription .= '<div><b>Armi compatibili:</b> ' . implode(' · ', $item['compatible_weapons']) . '</div>';
    $exportData = buildObjectExportData($item['name'], $item['cost'], $item['weight'], $exportDescription);
?>
        <div class="col">
        <article class="result-card ammunition-card card h-100 position-relative" data-favorite-dataset="ammunition" data-favorite-id="<?= e($item['id']) ?>" data-export="<?= e(json_encode($exportData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)) ?>" data-sort-name="<?= e($item['name']) ?>" data-sort-category="<?= e($item['container']) ?>" data-sort-weight="<?= e($item['weight']) ?>" data-sort-value="<?= e($item['cost']) ?>" data-search="<?= e(implode(' ', [
            $item['name'],
            $item['container'],
            implode(' ', $item['compatible_weapons']),
            $item['weight'],
            $item['cost']
        ])) ?>">
            <div class="card-header d-flex align-items-start justify-content-between gap-3">
                <h3><?= e($item['name']) ?></h3>
                <span class="weapon-mode">Oggetto</span>
            </div>
            <div class="card-body">
            <div class="weapon-category">
                <span class="weapon-category-main">Munizioni</span>
                <span class="weapon-category-arrow">→</span>
                <span><?= e($item['container']) ?></span>
            </div>
            <div class="ammo-quantity">
                <span class="armor-icon" aria-hidden="true">🎯</span>
                <div>
                    <span class="weapon-label">Quantità per acquisto</span>
                    <strong><?= e((string) $item['quantity']) ?></strong>
                </div>
            </div>
            <div class="row g-2 my-3">
                <div class="weapon-detail">
                    <span class="weapon-icon" aria-hidden="true">🏹</span>
                    <div>
                        <span class="weapon-label">Armi compatibili</span>
                        <span class="weapon-value"><?= e(implode(' · ', $item['compatible_weapons'])) ?></span>
                    </div>
                </div>
                <div class="weapon-detail">
                    <span class="weapon-icon" aria-hidden="true">⚖️</span>
                    <div>
                        <span class="weapon-label">Peso</span>
                        <span class="weapon-value"><?= e($item['weight']) ?></span>
                    </div>
                </div>
                <div class="weapon-detail">
                    <span class="weapon-icon" aria-hidden="true">🪙</span>
                    <div>
                        <span class="weapon-label">Valore</span>
                        <span class="weapon-value"><?= e($item['cost']) ?></span>
                    </div>
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

    <div id="empty-state" class="empty-state text-center p-5" hidden>
        <span class="empty-icon">⌕</span>
        <h3>Nessun risultato</h3>
        <p>Prova a modificare la ricerca.</p>
    </div>
</section>
<?php $paginationPosition = 'bottom'; require __DIR__ . '/../partials/pagination.php'; ?>
