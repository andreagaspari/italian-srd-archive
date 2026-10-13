<?php
$searchFormParameters = $_GET;
unset($searchFormParameters['route'], $searchFormParameters['page'], $searchFormParameters['pageSize'], $searchFormParameters['q']);
?>
<form class="object-toolbar" id="section-search-form" method="get" action="<?= e(appUrl($route)) ?>">
    <?php foreach ($searchFormParameters as $name => $value): ?>
        <?php if (is_scalar($value)): ?>
            <input type="hidden" name="<?= e((string) $name) ?>" value="<?= e((string) $value) ?>">
        <?php endif; ?>
    <?php endforeach; ?>
    <label class="object-search">
        <span class="search-icon" aria-hidden="true">🔎</span>
        <input id="object-search-input" name="q" type="search" value="<?= e(is_string($_GET['q'] ?? null) ? $_GET['q'] : '') ?>" placeholder="Cerca in questa sezione..." autocomplete="off">
    </label>
    <label class="object-sort">
        <span>Ordina</span>
        <select id="object-sort-select">
            <option value="default" selected>Ordine SRD</option>
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
    <?php if (($route ?? '') !== 'regole'): ?>
        <button class="export-visible-button" id="export-visible-button" type="button">⇩ Esporta risultati filtrati (<?= e((string) $pagination->totalItems) ?>)</button>
    <?php endif; ?>
</form>
