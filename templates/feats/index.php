<section class="results-view feat-list-view container-fluid px-0">
    <div class="breadcrumb d-flex flex-wrap gap-2 mb-4 text-body-secondary small">
        <a href="<?= e(appUrl('talenti')) ?>">Talenti</a>
        <span>/</span>
        <strong><?= e($selectedFeatSubcategory !== '' ? $selectedFeatSubcategory : 'Tutti i talenti') ?></strong>
    </div>
    <div class="section-heading d-flex justify-content-between align-items-end mb-3">
        <div>
            <h1><?= e($pageTitle) ?></h1>
        </div>
    </div>
    <?php require __DIR__ . '/../objects/search.php'; ?>

    <nav class="taxonomy-filters d-flex flex-wrap gap-2 mb-3" aria-label="Categorie dei talenti">
        <a class="taxonomy-filter btn btn-sm btn-outline-secondary <?= $selectedFeatSubcategory === '' ? 'is-selected' : '' ?>" href="<?= e(appUrl('talenti')) ?>">Tutti i talenti</a>
<?php foreach ($featSubcategories as $subcategory): ?>
        <a class="taxonomy-filter btn btn-sm btn-outline-secondary <?= $subcategory === $selectedFeatSubcategory ? 'is-selected' : '' ?>" href="<?= e(appUrl('talenti?' . http_build_query(['categoria' => $subcategory]))) ?>"><?= e($subcategory) ?></a>
<?php endforeach; ?>
    </nav>

<?php $paginationPosition = 'top'; require __DIR__ . '/../partials/pagination.php'; ?>
    <div id="results-grid" class="results-grid row row-cols-1 row-cols-md-2 row-cols-xl-3 g-3">
<?php foreach ($feats as $feat): ?>
<?php
    $description = (string) ($feat['description'] ?? '');
    $searchText = implode(' ', [
        (string) ($feat['name'] ?? ''),
        (string) ($feat['subcategory'] ?? ''),
        (string) ($feat['prerequisite'] ?? ''),
        $description
    ]);
    $exportData = buildFeatExportData($feat);
?>
        <article class="result-card feat-card card col h-100 position-relative" data-favorite-dataset="feats" data-favorite-id="<?= e((string) $feat['id']) ?>" data-export="<?= e(json_encode($exportData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)) ?>" data-sort-name="<?= e((string) $feat['name']) ?>" data-sort-category="<?= e((string) $feat['subcategory']) ?>" data-sort-weight="0" data-sort-value="0" data-search="<?= e($searchText) ?>">
            <div class="card-body">
            <div class="feat-heading d-flex align-items-start justify-content-between gap-3 bg-body-tertiary border-bottom px-3 py-2 mx-n3 mt-n3 mb-3">
                <div>
                    <a class="feat-category" href="<?= e(appUrl('talenti?' . http_build_query(['categoria' => $feat['subcategory']]))) ?>"><?= e((string) $feat['subcategory']) ?></a>
                    <h3><?= e((string) $feat['name']) ?></h3>
                </div>
                <?php if (($feat['repeatable'] ?? false) === true): ?><span class="feat-repeatable">Ripetibile</span><?php endif; ?>
            </div>
            <?php if (($feat['prerequisite'] ?? null) !== null && $feat['prerequisite'] !== ''): ?>
            <p class="feat-prerequisite"><strong>Prerequisito:</strong> <?= e((string) $feat['prerequisite']) ?></p>
            <?php endif; ?>
            <div class="feat-description"><?= renderSpellContent($feat) ?></div>
            </div>
            <div class="card-footer bg-transparent border-0 d-flex justify-content-between align-items-center px-3 pb-3">
                <button class="export-button btn btn-sm btn-outline-primary position-static" type="button">⇩ Esporta</button>
            </div>
        </article>
<?php endforeach; ?>
    </div>
    <div id="empty-state" class="empty-state text-center p-5" hidden>
        <span class="empty-icon">⌕</span>
        <h3>Nessun risultato</h3>
        <p>Prova a modificare la ricerca o a scegliere un altro filtro.</p>
    </div>
</section>
<?php $paginationPosition = 'bottom'; require __DIR__ . '/../partials/pagination.php'; ?>
