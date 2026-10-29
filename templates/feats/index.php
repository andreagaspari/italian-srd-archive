<section class="results-view feat-list-view">
    <div class="breadcrumb">
        <a href="<?= e(appUrl('talenti')) ?>">Talenti</a>
        <span>/</span>
        <strong><?= e($pageTitle) ?></strong>
    </div>
    <div class="section-heading">
        <div>
            <p class="eyebrow">Personaggio</p>
            <h1><?= e($pageTitle) ?></h1>
        </div>
    </div>
    <?php require __DIR__ . '/../objects/search.php'; ?>

    <nav class="taxonomy-filters" aria-label="Categorie dei talenti">
        <a class="taxonomy-filter <?= $selectedFeatSubcategory === '' ? 'is-selected' : '' ?>" href="<?= e(appUrl('talenti')) ?>">Tutti i talenti</a>
<?php foreach ($featSubcategories as $subcategory): ?>
        <a class="taxonomy-filter <?= $subcategory === $selectedFeatSubcategory ? 'is-selected' : '' ?>" href="<?= e(appUrl('talenti?' . http_build_query(['categoria' => $subcategory]))) ?>"><?= e($subcategory) ?></a>
<?php endforeach; ?>
    </nav>

<?php $paginationPosition = 'top'; require __DIR__ . '/../partials/pagination.php'; ?>
    <div id="results-grid" class="results-grid">
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
        <article class="result-card feat-card" data-favorite-dataset="feats" data-favorite-id="<?= e((string) $feat['id']) ?>" data-export="<?= e(json_encode($exportData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)) ?>" data-sort-name="<?= e((string) $feat['name']) ?>" data-sort-category="<?= e((string) $feat['subcategory']) ?>" data-sort-weight="0" data-sort-value="0" data-search="<?= e($searchText) ?>">
            <div class="feat-heading">
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
            <button class="export-button" type="button">⇩ Esporta</button>
        </article>
<?php endforeach; ?>
    </div>
    <div id="empty-state" class="empty-state" hidden>
        <span class="empty-icon">⌕</span>
        <h3>Nessun risultato</h3>
        <p>Prova a modificare la ricerca o a scegliere un altro filtro.</p>
    </div>
</section>
<?php $paginationPosition = 'bottom'; require __DIR__ . '/../partials/pagination.php'; ?>
