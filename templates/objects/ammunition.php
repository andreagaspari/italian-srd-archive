<section class="results-view ammunition-list-view">
    <div class="breadcrumb">
        <a class="breadcrumb-home" href="<?= e(appUrl('oggetti')) ?>">Oggetti</a>
        <span>/</span>
        <strong>Munizioni</strong>
    </div>

    <div class="section-heading">
        <div>
            <h1>Munizioni</h1>
        </div>
    </div>

    <?php require __DIR__ . '/search.php'; ?>

    <div class="result-summary">Mostrati <strong id="result-count"><?= count($ammunition) ?></strong> di <strong><?= e((string) $pagination->totalItems) ?></strong> elementi</div>

    <div id="results-grid" class="results-grid">
<?php foreach ($ammunition as $item): ?>
<?php
    $exportDescription = '<div><b>Quantità per acquisto:</b> ' . $item['quantity'] . '</div>';
    $exportDescription .= '<div><b>Armi compatibili:</b> ' . implode(' · ', $item['compatible_weapons']) . '</div>';
    $exportData = buildObjectExportData($item['name'], $item['cost'], $item['weight'], $exportDescription);
?>
        <article class="result-card ammunition-card" data-export="<?= e(json_encode($exportData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)) ?>" data-sort-name="<?= e($item['name']) ?>" data-sort-category="<?= e($item['container']) ?>" data-sort-weight="<?= e($item['weight']) ?>" data-sort-value="<?= e($item['cost']) ?>" data-search="<?= e(implode(' ', [
            $item['name'],
            $item['container'],
            implode(' ', $item['compatible_weapons']),
            $item['weight'],
            $item['cost']
        ])) ?>">
            <div class="weapon-category">
                <span class="weapon-category-main">Munizioni</span>
                <span class="weapon-category-arrow">→</span>
                <span><?= e($item['container']) ?></span>
            </div>
            <div class="weapon-heading">
                <h3><?= e($item['name']) ?></h3>
                <span class="weapon-mode">Oggetto</span>
            </div>
            <div class="ammo-quantity">
                <span class="armor-icon" aria-hidden="true">🎯</span>
                <div>
                    <span class="weapon-label">Quantità per acquisto</span>
                    <strong><?= e((string) $item['quantity']) ?></strong>
                </div>
            </div>
            <div class="weapon-details">
                <div class="weapon-detail">
                    <span class="weapon-icon" aria-hidden="true">🏹</span>
                    <div>
                        <span class="weapon-label">Armi compatibili</span>
                        <span class="weapon-value"><?= e(implode(' · ', $item['compatible_weapons'])) ?></span>
                    </div>
                </div>
                <div class="weapon-detail">
                    <span class="weapon-icon" aria-hidden="true">⚖️</span>
                    <div>
                        <span class="weapon-label">Peso</span>
                        <span class="weapon-value"><?= e($item['weight']) ?></span>
                    </div>
                </div>
                <div class="weapon-detail">
                    <span class="weapon-icon" aria-hidden="true">🪙</span>
                    <div>
                        <span class="weapon-label">Valore</span>
                        <span class="weapon-value"><?= e($item['cost']) ?></span>
                    </div>
                </div>
            </div>
            <button class="export-button" type="button">⇩ Esporta</button>
        </article>
<?php endforeach; ?>
    </div>

    <div id="empty-state" class="empty-state" hidden>
        <span class="empty-icon">⌕</span>
        <h3>Nessun risultato</h3>
        <p>Prova a modificare la ricerca.</p>
    </div>
</section>
<?php require __DIR__ . '/../partials/pagination.php'; ?>
