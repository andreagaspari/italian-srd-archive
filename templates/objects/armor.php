<?php
$armorTypeLabels = [
    'leggera' => 'Armature leggere',
    'media' => 'Armature medie',
    'pesante' => 'Armature pesanti',
    'scudo' => 'Scudi'
];
?>
<section class="results-view armor-list-view container-fluid px-0">
    <div class="breadcrumb d-flex flex-wrap gap-2 mb-4 text-body-secondary small">
        <a class="breadcrumb-home" href="<?= e(appUrl('oggetti')) ?>">Oggetti</a>
        <span>/</span>
        <a href="<?= e(appUrl('oggetti/armature')) ?>">Armature</a>
        <span>/</span>
        <strong><?= e($pageTitle) ?></strong>
    </div>

    <div class="section-heading d-flex justify-content-between align-items-end mb-3">
        <div>
            <h1><?= e($pageTitle) ?></h1>
        </div>
    </div>
    <?php require __DIR__ . '/search.php'; ?>

    <nav class="taxonomy-filters d-flex flex-wrap gap-2 mb-3" aria-label="Categorie armature">
<?php foreach ($categoryLabels as $category => $label): ?>
        <a class="taxonomy-filter btn btn-sm btn-outline-secondary <?= $category === $selectedCategory ? 'is-selected' : '' ?>" href="<?= e(appUrl('oggetti/armature?categoria=' . urlencode($category))) ?>"><?= e($label) ?></a>
<?php endforeach; ?>
    </nav>

<?php $paginationPosition = 'top'; require __DIR__ . '/../partials/pagination.php'; ?>

    <div id="results-grid" class="results-grid row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4 align-items-start">
<?php foreach ($filteredArmor as $armor): ?>
<?php $armorCategory = 'armor-' . match ($armor['armor_type']) {
    'leggera' => 'light',
    'media' => 'medium',
    'pesante' => 'heavy',
    'scudo' => 'shields',
}; ?>
<?php
    $displayArmorClass = str_replace('modificatore di Destrezza', 'mod. DES', $armor['armor_class']);
    $armorDescription = '<div><b>CA: ' . $armor['armor_class'] . '</b></div>';
    if ($armor['strength_requirement'] !== null) {
        $armorDescription .= '<div>Forza minima: ' . $armor['strength_requirement'] . '</div>';
    }
    if ($armor['stealth_disadvantage']) {
        $armorDescription .= '<div>Svantaggio a Furtività</div>';
    }
    $armorDescription .= '<div>Indossare: ' . $armorData['donning'][$armor['armor_type']]['wear'] . ' · Togliere: ' . $armorData['donning'][$armor['armor_type']]['remove'] . '</div>';
    $exportData = buildObjectExportData($armor['name'], $armor['cost'], $armor['weight'], $armorDescription);
?>
        <div class="col">
        <article class="result-card armor-card card position-relative" data-favorite-dataset="armor" data-favorite-id="<?= e($armor['id']) ?>" data-export="<?= e(json_encode($exportData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)) ?>" data-sort-name="<?= e($armor['name']) ?>" data-sort-category="<?= e($armor['armor_type']) ?>" data-sort-weight="<?= e($armor['weight']) ?>" data-sort-value="<?= e($armor['cost']) ?>" data-search="<?= e(implode(' ', [
            $armor['name'],
            $armor['armor_type'],
            $armor['armor_class'],
            $armor['strength_requirement'] ?? '',
            $armor['stealth_disadvantage'] ? 'svantaggio furtività' : 'furtività normale',
            $armor['weight'],
            $armor['cost']
        ])) ?>">
            <div class="card-header d-flex align-items-start justify-content-between gap-3">
            <div>
                <div class="weapon-category d-flex align-items-center gap-2 mb-1">
                    <a class="weapon-category-main" href="<?= e(appUrl('oggetti/armature')) ?>">Armature</a>
                    <span class="weapon-category-arrow">→</span>
                    <a class="weapon-category-subcategory" href="<?= e(appUrl('oggetti/armature?categoria=' . $armorCategory)) ?>"><?= e($armorTypeLabels[$armor['armor_type']]) ?></a>
                </div>
                <h3><?= e($armor['name']) ?></h3>
            </div>
            <span class="weapon-mode"><?= e($armorTypeLabels[$armor['armor_type']]) ?></span>
        </div>
        <div class="card-body">
        <div class="weapon-summary d-flex align-items-center gap-3 mb-3">
            <div class="armor-class d-flex align-items-center gap-1" title="Classe Armatura">
                <span class="armor-icon" aria-hidden="true">🛡️</span>
                <strong><?= e($displayArmorClass) ?></strong>
            </div>
            <div class="weapon-summary-detail d-flex align-items-center gap-1 ms-auto" title="Peso">
                <span class="weapon-icon" aria-hidden="true">⚖️</span>
                <span class="weapon-value"><?= e($armor['weight']) ?></span>
            </div>
            <div class="weapon-summary-detail d-flex align-items-center gap-1" title="Valore">
                <span class="weapon-icon" aria-hidden="true">🪙</span>
                <span class="weapon-value"><?= e($armor['cost']) ?></span>
            </div>
        </div>
        <div class="armor-details d-flex flex-column flex-md-row gap-3 mt-3">
            <div class="flex-grow-1">
                <div class="row row-cols-1 g-2">
                    <div class="col weapon-detail">
                        <span class="weapon-icon" aria-hidden="true">💪</span>
                        <div>
                            <span class="weapon-label">Forza</span>
                            <span class="weapon-value"><?= $armor['strength_requirement'] === null ? 'Nessun requisito' : e('Forza ' . $armor['strength_requirement']) ?></span>
                        </div>
                    </div>
                    <div class="col weapon-detail">
                        <span class="weapon-icon" aria-hidden="true">🥷</span>
                        <div>
                            <span class="weapon-label">Furtività</span>
                            <span class="weapon-value"><?= $armor['stealth_disadvantage'] ? 'Svantaggio' : 'Nessuno svantaggio' ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="armor-timing-details flex-shrink-0 ms-md-auto">
                <div class="row row-cols-1 g-2">
                    <div class="col weapon-detail">
                        <span class="weapon-icon" aria-hidden="true">⏱️</span>
                        <div>
                            <span class="weapon-label">Indossare</span>
                            <span class="weapon-value"><?= e($armorData['donning'][$armor['armor_type']]['wear']) ?></span>
                        </div>
                    </div>
                    <div class="col weapon-detail">
                        <span class="weapon-icon" aria-hidden="true">⏱️</span>
                        <div>
                            <span class="weapon-label">Togliere</span>
                            <span class="weapon-value"><?= e($armorData['donning'][$armor['armor_type']]['remove']) ?></span>
                        </div>
                    </div>
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
        <p>Prova a modificare la ricerca o a scegliere un’altra categoria.</p>
    </div>
</section>
<?php $paginationPosition = 'bottom'; require __DIR__ . '/../partials/pagination.php'; ?>
