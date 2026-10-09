<?php

declare(strict_types=1);

if (!isset($pagination) || !$pagination instanceof PageResult) {
    return;
}

$totalPages = $pagination->totalPages();
$currentPage = $pagination->page;
$pageSize = $pagination->pageSize ?? 24;
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
<div class="pagination-summary">
    <span>Mostrati <?= e((string) count($pagination->items)) ?> di <?= e((string) $pagination->totalItems) ?> risultati</span>
    <form method="get" action="<?= e(appUrl($route)) ?>">
        <?php foreach ($formParameters as $name => $value): ?>
            <?php if (is_scalar($value)): ?>
                <input type="hidden" name="<?= e((string) $name) ?>" value="<?= e((string) $value) ?>">
            <?php endif; ?>
        <?php endforeach; ?>
        <label for="page-size">Risultati per pagina</label>
        <select id="page-size" name="pageSize" onchange="this.form.submit()">
            <?php foreach ([12, 24, 48, 96] as $option): ?>
                <option value="<?= $option ?>"<?= $pageSize === $option ? ' selected' : '' ?>><?= $option ?></option>
            <?php endforeach; ?>
        </select>
    </form>
</div>
<?php if ($totalPages > 1): ?>
<nav class="pagination" aria-label="Paginazione">
    <?php if ($currentPage > 1): ?>
        <a href="<?= e($buildPageUrl($currentPage - 1)) ?>" rel="prev">‹ Precedente</a>
    <?php endif; ?>
    <span>Pagina <?= e((string) $currentPage) ?> di <?= e((string) $totalPages) ?> · <?= e((string) $pagination->totalItems) ?> risultati</span>
    <?php if ($currentPage < $totalPages): ?>
        <a href="<?= e($buildPageUrl($currentPage + 1)) ?>" rel="next">Successiva ›</a>
    <?php endif; ?>
</nav>
<?php endif; ?>
