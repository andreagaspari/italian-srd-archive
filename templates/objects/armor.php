<?php
$armorTypeLabels = [
    'leggera' => 'Armature leggere',
    'media' => 'Armature medie',
    'pesante' => 'Armature pesanti',
    'scudo' => 'Scudi'
];
?>
<section class="results-view armor-list-view">
    <div class="breadcrumb">
        <a class="breadcrumb-home" href="<?= e(appUrl('oggetti')) ?>">Oggetti</a>
        <span>/</span>
        <a href="<?= e(appUrl('oggetti/armature')) ?>">Armature</a>
        <span>/</span>
        <strong><?= e($pageTitle) ?></strong>
    </div>

    <div class="section-heading">
        <div>
            <h1><?= e($pageTitle) ?></h1>
        </div>
    </div>
    <?php require __DIR__ . '/search.php'; ?>

    <nav class="taxonomy-filters" aria-label="Categorie armature">
<?php foreach ($categoryLabels as $category => $label): ?>
        <a class="taxonomy-filter <?= $category === $selectedCategory ? 'is-selected' : '' ?>" href="<?= e(appUrl('oggetti/armature?categoria=' . urlencode($category))) ?>"><?= e($label) ?></a>
<?php endforeach; ?>
    </nav>

    <div class="result-summary"><strong id="result-count"><?= count($filteredArmor) ?></strong> elementi trovati</div>

    <div id="results-grid" class="results-grid">
<?php foreach ($filteredArmor as $armor): ?>
<?php $armorCategory = 'armor-' . match ($armor['armor_type']) {
    'leggera' => 'light',
    'media' => 'medium',
    'pesante' => 'heavy',
    'scudo' => 'shields',
}; ?>
<?php
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
        <article class="result-card armor-card" data-export="<?= e(json_encode($exportData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)) ?>" data-sort-name="<?= e($armor['name']) ?>" data-sort-category="<?= e($armor['armor_type']) ?>" data-sort-weight="<?= e($armor['weight']) ?>" data-sort-value="<?= e($armor['cost']) ?>" data-search="<?= e(implode(' ', [
            $armor['name'],
            $armor['armor_type'],
            $armor['armor_class'],
            $armor['strength_requirement'] ?? '',
            $armor['stealth_disadvantage'] ? 'svantaggio furtività' : 'furtività normale',
            $armor['weight'],
            $armor['cost']
        ])) ?>">
            <div class="weapon-category">
                <a class="weapon-category-main" href="<?= e(appUrl('oggetti/armature')) ?>">Armature</a>
                <span class="weapon-category-arrow">→</span>
                <a class="weapon-category-subcategory" href="<?= e(appUrl('oggetti/armature?categoria=' . $armorCategory)) ?>"><?= e($armorTypeLabels[$armor['armor_type']]) ?></a>
            </div>
            <div class="weapon-heading">
                <h3><?= e($armor['name']) ?></h3>
                <span class="weapon-mode"><?= e($armorTypeLabels[$armor['armor_type']]) ?></span>
            </div>
            <div class="armor-class">
                <span class="armor-icon" aria-hidden="true">🛡️</span>
                <div>
                    <span class="weapon-label">Classe Armatura</span>
                    <strong><?= e($armor['armor_class']) ?></strong>
                </div>
            </div>
            <div class="weapon-details armor-details">
                <div class="weapon-detail">
                    <span class="weapon-icon" aria-hidden="true">💪</span>
                    <div>
                        <span class="weapon-label">Forza</span>
                        <span class="weapon-value"><?= $armor['strength_requirement'] === null ? 'Nessun requisito' : e('Forza ' . $armor['strength_requirement']) ?></span>
                    </div>
                </div>
                <div class="weapon-detail">
                    <span class="weapon-icon" aria-hidden="true">🥷</span>
                    <div>
                        <span class="weapon-label">Furtività</span>
                        <span class="weapon-value"><?= $armor['stealth_disadvantage'] ? 'Svantaggio' : 'Nessuno svantaggio' ?></span>
                    </div>
                </div>
                <div class="weapon-detail">
                    <span class="weapon-icon" aria-hidden="true">⚖️</span>
                    <div>
                        <span class="weapon-label">Peso</span>
                        <span class="weapon-value"><?= e($armor['weight']) ?></span>
                    </div>
                </div>
                <div class="weapon-detail">
                    <span class="weapon-icon" aria-hidden="true">🪙</span>
                    <div>
                        <span class="weapon-label">Valore</span>
                        <span class="weapon-value"><?= e($armor['cost']) ?></span>
                    </div>
                </div>
            </div>
            <div class="armor-wear">
                Indossare: <?= e($armorData['donning'][$armor['armor_type']]['wear']) ?>
                <span>·</span>
                Togliere: <?= e($armorData['donning'][$armor['armor_type']]['remove']) ?>
            </div>
            <button class="export-button" type="button">⇩ Esporta</button>
        </article>
<?php endforeach; ?>
    </div>

    <div id="empty-state" class="empty-state" hidden>
        <span class="empty-icon">⌕</span>
        <h3>Nessun risultato</h3>
        <p>Prova a modificare la ricerca o a scegliere un’altra categoria.</p>
    </div>
</section>
