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
<section class="results-view rules-view">
    <div class="breadcrumb">
        <a href="<?= e(appUrl()) ?>">Archivio</a>
        <span>/</span>
        <strong>Glossario delle regole</strong>
    </div>

    <div class="section-heading">
        <div>
            <h1>Glossario delle regole</h1>
        </div>
    </div>

    <div class="rules-introduction">
<?php foreach ($introduction as $section): ?>
        <section class="rules-introduction-section">
            <h2><?= e((string) ($section['title'] ?? '')) ?></h2>
<?php foreach (($section['paragraphs'] ?? []) as $paragraph): ?>
            <p><?= e((string) $paragraph) ?></p>
<?php endforeach; ?>
        </section>
<?php endforeach; ?>
        <section class="rules-abbreviations">
            <h2>Abbreviazioni</h2>
            <dl>
<?php foreach ($abbreviations as $abbreviation): ?>
                <div>
                    <dt><?= e((string) ($abbreviation['abbreviation'] ?? '')) ?></dt>
                    <dd><?= e((string) ($abbreviation['meaning'] ?? '')) ?></dd>
                </div>
<?php endforeach; ?>
            </dl>
        </section>
    </div>

    <div class="rules-divider">
        <h2>Definizione delle regole</h2>
        <p>Consulta le voci del glossario oppure filtra per gruppo di appartenenza.</p>
    </div>
    <?php require __DIR__ . '/../objects/search.php'; ?>

    <nav class="taxonomy-filters" aria-label="Categorie delle regole">
        <a class="taxonomy-filter <?= $selectedRuleCategory === '' ? 'is-selected' : '' ?>" href="<?= e(appUrl('regole')) ?>">Tutte</a>
<?php foreach ($ruleCategories as $category): ?>
        <a class="taxonomy-filter <?= $selectedRuleCategory === $category ? 'is-selected' : '' ?>" href="<?= e(appUrl('regole?' . http_build_query(['categoria' => $category]))) ?>"><?= e($categoryLabels[$category] ?? ucfirst($category)) ?></a>
<?php endforeach; ?>
    </nav>

    <div class="result-summary">Mostrate <strong id="result-count"><?= count($rules) ?></strong> di <strong><?= $pagination->totalItems ?></strong> regole</div>
    <?php
    $ruleColumns = [[], []];
    foreach ($rules as $ruleIndex => $rule) {
        $rule['display_order'] = $ruleIndex;
        $ruleColumns[$ruleIndex % 2][] = $rule;
    }
    ?>
    <div class="rules-list">
<?php foreach ($ruleColumns as $ruleColumn): ?>
        <div class="rules-column">
<?php foreach ($ruleColumn as $rule): ?>
        <article class="result-card rule-card" style="--rule-order: <?= (int) $rule['display_order'] ?>" data-favorite-dataset="rules" data-favorite-id="<?= e((string) $rule['id']) ?>" data-sort-name="<?= e((string) $rule['name']) ?>" data-sort-category="<?= e((string) ($rule['category'] ?? '')) ?>" data-search="<?= e(implode(' ', [(string) $rule['name'], (string) ($rule['category'] ?? ''), (string) $rule['content']])) ?>">
            <div class="rule-card-heading">
                <h3><?= e((string) $rule['name']) ?></h3>
                <?php if (($rule['category'] ?? '') !== ''): ?>
                    <a class="rule-category" href="<?= e(appUrl('regole?' . http_build_query(['categoria' => $rule['category']]))) ?>"><?= e($categoryLabels[$rule['category']] ?? ucfirst((string) $rule['category'])) ?></a>
                <?php endif; ?>
            </div>
            <p class="rule-content"><?= ruleContentWithAbbreviations((string) $rule['content'], $abbreviations) ?></p>
            <span class="rule-source">pag. <?= e((string) ($rule['source_page'] ?? '')) ?></span>
        </article>
<?php endforeach; ?>
        </div>
<?php endforeach; ?>
    </div>
</section>
<?php require __DIR__ . '/../partials/pagination.php'; ?>
