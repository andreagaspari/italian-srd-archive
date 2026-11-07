<?php
$searchFormParameters = $_GET;
unset($searchFormParameters['route'], $searchFormParameters['page'], $searchFormParameters['pageSize'], $searchFormParameters['q']);
?>
<form class="object-toolbar row g-2 align-items-end mb-3" id="section-search-form" method="get" action="<?= e(appUrl($route)) ?>">
    <?php foreach ($searchFormParameters as $name => $value): ?>
        <?php if (is_scalar($value)): ?>
            <input type="hidden" name="<?= e((string) $name) ?>" value="<?= e((string) $value) ?>">
        <?php endif; ?>
    <?php endforeach; ?>
    <label class="object-search input-group col-12 col-lg">
        <span class="search-icon input-group-text" aria-hidden="true">🔎</span>
        <input class="form-control" id="object-search-input" name="q" type="search" value="<?= e(is_string($_GET['q'] ?? null) ? $_GET['q'] : '') ?>" placeholder="Cerca in questa sezione..." autocomplete="off">
    </label>
    <label class="object-sort d-flex align-items-center gap-2 text-body-secondary small col-12 col-sm-auto">
        <span>Ordina</span>
        <select class="form-select" id="object-sort-select">
            <option value="relevance" <?= trim((string) ($_GET['q'] ?? '')) !== '' ? 'selected' : '' ?>>Rilevanza</option>
            <option value="default" <?= trim((string) ($_GET['q'] ?? '')) === '' ? 'selected' : '' ?>>Ordine SRD</option>
            <option value="name-asc">Nome ↑</option>
            <option value="name-desc">Nome ↓</option>
            <option value="category-asc">Categoria ↑</option>
            <option value="category-desc">Categoria ↓</option>
            <option value="weight-asc">Peso ↑</option>
            <option value="weight-desc">Peso ↓</option>
            <option value="value-asc">Valore ↑</option>
            <option value="value-desc">Valore ↓</option>
        </select>
    </label>
    <button class="export-visible-button btn btn-outline-primary col-12 col-sm-auto" id="export-visible-button" type="button">⇩ Esporta risultati filtrati (<?= e((string) $pagination->totalItems) ?>)</button>
</form>
