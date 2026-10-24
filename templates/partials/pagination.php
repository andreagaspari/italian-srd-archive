<?php

declare(strict_types=1);

if (!isset($pagination) || !$pagination instanceof PageResult) {
    return;
}

$totalPages = $pagination->totalPages();
$currentPage = $pagination->page;
$pageSize = $pagination->pageSize ?? 24;
$paginationPosition = $paginationPosition ?? 'bottom';
$pageSizeId = 'page-size-' . $paginationPosition;
$buildPageUrl = static function (int $page) use ($route): string {
    $parameters = $_GET;
    unset($parameters['route']);
    $parameters['page'] = $page;

    $query = http_build_query($parameters);
    return appUrl($route . ($query !== '' ? '?' . $query : ''));
};
$formParameters = $_GET;
unset($formParameters['route'], $formParameters['page'], $formParameters['pageSize']);
?>
<div class="pagination-row pagination-row-<?= e($paginationPosition) ?>">
<div class="pagination-summary pagination-summary-<?= e($paginationPosition) ?>">
    <span>Mostrati <strong id="result-count-<?= e($paginationPosition) ?>"><?= e((string) count($pagination->items)) ?></strong> di <?= e((string) $pagination->totalItems) ?> risultati</span>
</div>
<?php if ($totalPages > 1): ?>
<nav class="pagination pagination-<?= e($paginationPosition) ?>" aria-label="Paginazione">
    <?php if ($currentPage > 1): ?>
        <a href="<?= e($buildPageUrl($currentPage - 1)) ?>" rel="prev">‹ Precedente</a>
    <?php endif; ?>
    <span>Pagina <?= e((string) $currentPage) ?> di <?= e((string) $totalPages) ?></span>
    <?php if ($currentPage < $totalPages): ?>
        <a href="<?= e($buildPageUrl($currentPage + 1)) ?>" rel="next">Successiva ›</a>
    <?php endif; ?>
</nav>
<?php endif; ?>
<form class="pagination-page-size" method="get" action="<?= e(appUrl($route)) ?>">
    <?php foreach ($formParameters as $name => $value): ?>
        <?php if (is_scalar($value)): ?>
            <input type="hidden" name="<?= e((string) $name) ?>" value="<?= e((string) $value) ?>">
        <?php endif; ?>
    <?php endforeach; ?>
    <label for="<?= e($pageSizeId) ?>">Risultati per pagina</label>
    <select id="<?= e($pageSizeId) ?>" name="pageSize" onchange="this.form.submit()">
        <?php foreach ([12, 24, 48, 96] as $option): ?>
            <option value="<?= $option ?>"<?= $pageSize === $option ? ' selected' : '' ?>><?= $option ?></option>
        <?php endforeach; ?>
    </select>
</form>
</div>
