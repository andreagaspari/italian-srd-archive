<?php

$introduction = $rulesDocument['introduction']['sections'] ?? [];
$abbreviations = $rulesDocument['abbreviations'] ?? [];
$source = $rulesDocument['source'] ?? [];
$categoryLabels = [
    'azione' => 'Azioni',
    'area di effetto' => 'Aree di effetto',
    'atteggiamento' => 'Atteggiamenti',
    'condizione' => 'Condizioni',
    'pericolo' => 'Pericoli'
];
?>
<section class="results-view rules-view container-fluid px-0">
    <div class="breadcrumb d-flex flex-wrap gap-2 mb-4 text-body-secondary small">
        <a href="<?= e(appUrl('regole')) ?>">Regole</a>
        <span>/</span>
        <strong><?= e($selectedRuleCategory !== ''
            ? ($categoryLabels[$selectedRuleCategory] ?? ucfirst($selectedRuleCategory))
            : 'Tutte le regole') ?></strong>
    </div>

    <div class="section-heading d-flex justify-content-between align-items-end mb-3">
        <div>
            <h1>Glossario delle regole</h1>
        </div>
    </div>

    <div class="rules-introduction">
<?php foreach ($introduction as $section): ?>
        <details class="rules-introduction-section">
            <summary><?= e((string) ($section['title'] ?? '')) ?></summary>
            <div class="rules-introduction-content">
<?php foreach (($section['paragraphs'] ?? []) as $paragraph): ?>
            <p><?= e((string) $paragraph) ?></p>
<?php endforeach; ?>
            <section class="rules-abbreviations">
                <h3>Abbreviazioni</h3>
                <div class="rules-abbreviations-content">
                <dl>
<?php foreach ($abbreviations as $abbreviation): ?>
                    <div>
                        <dt><?= e((string) ($abbreviation['abbreviation'] ?? '')) ?></dt>
                        <dd><?= e((string) ($abbreviation['meaning'] ?? '')) ?></dd>
                    </div>
<?php endforeach; ?>
                </dl>
                </div>
            </section>
            </div>
        </details>
<?php endforeach; ?>
    </div>

    <?php require __DIR__ . '/../objects/search.php'; ?>

    <nav class="taxonomy-filters d-flex flex-wrap gap-2 mb-3" aria-label="Categorie delle regole">
        <a class="taxonomy-filter btn btn-sm btn-outline-secondary <?= $selectedRuleCategory === '' ? 'is-selected' : '' ?>" href="<?= e(appUrl('regole')) ?>">Tutte</a>
<?php foreach ($ruleCategories as $category): ?>
        <a class="taxonomy-filter btn btn-sm btn-outline-secondary <?= $selectedRuleCategory === $category ? 'is-selected' : '' ?>" href="<?= e(appUrl('regole?' . http_build_query(['categoria' => $category]))) ?>"><?= e($categoryLabels[$category] ?? ucfirst($category)) ?></a>
<?php endforeach; ?>
    </nav>

    <div class="description-expansion-control form-check form-switch mb-3">
        <input class="form-check-input" type="checkbox" role="switch" id="expand-all-descriptions">
        <label class="form-check-label" for="expand-all-descriptions">Espandi tutte le descrizioni</label>
    </div>

<?php $paginationPosition = 'top'; require __DIR__ . '/../partials/pagination.php'; ?>
    <?php
    $ruleColumns = [[], []];
    foreach ($rules as $ruleIndex => $rule) {
        $rule['display_order'] = $ruleIndex;
        $ruleColumns[$ruleIndex % 2][] = $rule;
    }
    ?>
    <div class="rules-list row row-cols-1 row-cols-md-2 g-4 align-items-start">
<?php foreach ($ruleColumns as $ruleColumn): ?>
        <div class="rules-column col d-flex flex-column gap-3">
<?php foreach ($ruleColumn as $rule): ?>
<?php $exportData = buildRuleExportData($rule, $abbreviations); ?>
        <article class="result-card rule-card card h-100 position-relative" style="--rule-order: <?= (int) $rule['display_order'] ?>" data-rule-order="<?= (int) $rule['display_order'] ?>" data-favorite-dataset="rules" data-favorite-id="<?= e((string) $rule['id']) ?>" data-export="<?= e(json_encode($exportData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)) ?>" data-sort-name="<?= e((string) $rule['name']) ?>" data-sort-category="<?= e((string) ($rule['category'] ?? '')) ?>" data-search="<?= e(implode(' ', [(string) $rule['name'], (string) ($rule['category'] ?? ''), (string) $rule['content']])) ?>">
            <div class="card-header d-flex align-items-start justify-content-between gap-3">
                <h3><?= e((string) $rule['name']) ?></h3>
                <?php if (($rule['category'] ?? '') !== ''): ?>
                    <a class="rule-category" href="<?= e(appUrl('regole?' . http_build_query(['categoria' => $rule['category']]))) ?>"><?= e($categoryLabels[$rule['category']] ?? ucfirst((string) $rule['category'])) ?></a>
                <?php endif; ?>
            </div>
            <div class="card-body">
            <div class="rule-content"><?= renderRuleContent($rule, $abbreviations) ?></div>
            <span class="rule-source">pag. <?= e((string) ($rule['source_page'] ?? '')) ?></span>
            </div>
            <div class="card-footer bg-transparent border-0 d-flex justify-content-between align-items-center px-3 pb-3">
                <button class="export-button btn btn-sm btn-outline-primary position-static" type="button">⇩ Esporta</button>
            </div>
        </article>
<?php endforeach; ?>
        </div>
<?php endforeach; ?>
    </div>
</section>
<?php $paginationPosition = 'bottom'; require __DIR__ . '/../partials/pagination.php'; ?>
