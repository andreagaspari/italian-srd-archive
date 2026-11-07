<section class="results-view spell-list-view container-fluid px-0">
    <div class="breadcrumb d-flex flex-wrap gap-2 mb-4 text-body-secondary small">
        <a href="<?= e(appUrl('incantesimi')) ?>">Incantesimi</a>
        <span>/</span>
        <strong><?= e($pageTitle) ?></strong>
    </div>

    <div class="section-heading d-flex justify-content-between align-items-end mb-3">
        <div>
            <h1><?= e($pageTitle) ?></h1>
        </div>
    </div>
    <?php require __DIR__ . '/../objects/search.php'; ?>

    <nav class="taxonomy-filters d-flex flex-wrap gap-2 mb-3" aria-label="Livello degli incantesimi">
<?php foreach ($levelLabels as $level => $label): ?>
        <?php $query = ['livello' => $level]; if ($selectedSchool !== '') $query['scuola'] = $selectedSchool; if ($selectedClass !== '') $query['classe'] = $selectedClass; ?>
        <a class="taxonomy-filter btn btn-sm btn-outline-secondary <?= (string) $level === $selectedLevel ? 'is-selected' : '' ?>" href="<?= e(appUrl('incantesimi?' . http_build_query($query))) ?>"><?= e($label) ?></a>
<?php endforeach; ?>
    </nav>

    <form class="spell-filters row g-2 align-items-center mb-3" method="get" action="<?= e(appUrl('incantesimi')) ?>">
        <label class="col-12 col-sm-auto d-flex flex-column gap-1">
            <span>Scuola</span>
            <select class="spell-filter-select form-select" name="scuola">
                <?php foreach ($schoolLabels as $school => $label): ?>
                    <option value="<?= e($school) ?>" <?= $school === $selectedSchool ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label class="col-12 col-sm-auto d-flex flex-column gap-1">
            <span>Classe</span>
            <select class="spell-filter-select form-select" name="classe">
                <option value="">Tutte le classi</option>
                <?php foreach ($spellClasses as $class): ?>
                    <option value="<?= e($class) ?>" <?= $class === $selectedClass ? 'selected' : '' ?>><?= e(ucfirst($class)) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <?php if ($selectedLevel !== 'all' || $selectedSchool !== '' || $selectedClass !== ''): ?>
            <a class="btn btn-outline-secondary col-12 col-sm-auto align-self-end" href="<?= e(appUrl('incantesimi')) ?>">× Resetta filtri</a>
        <?php endif; ?>
        <div class="description-expansion-control form-check form-switch col-12 col-sm-auto ms-auto align-self-end mb-0">
            <input class="form-check-input" type="checkbox" role="switch" id="expand-all-descriptions">
            <label class="form-check-label" for="expand-all-descriptions">Espandi tutte le descrizioni</label>
        </div>
        <?php if ($selectedLevel !== 'all'): ?><input type="hidden" name="livello" value="<?= e($selectedLevel) ?>"><?php endif; ?>
    </form>

<?php $paginationPosition = 'top'; require __DIR__ . '/../partials/pagination.php'; ?>
    <div id="results-grid" class="results-grid row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4 align-items-start">
<?php foreach ($spells as $spell): ?>
<?php $exportData = buildSpellExportData($spell); ?>
<?php
    $spellLevelQuery = ['livello' => $spell['level'] === 0 ? '0' : (string) $spell['level']];
    if ($selectedSchool !== '') {
        $spellLevelQuery['scuola'] = $selectedSchool;
    }
    if ($selectedClass !== '') {
        $spellLevelQuery['classe'] = $selectedClass;
    }
$spellSchoolQuery = [];
if ($selectedLevel !== 'all') {
    $spellSchoolQuery['livello'] = $selectedLevel;
}
if ($selectedClass !== '') {
    $spellSchoolQuery['classe'] = $selectedClass;
}
$spellSchoolQuery['scuola'] = $spell['school'];
$spellClassBaseQuery = [];
if ($selectedLevel !== 'all') {
    $spellClassBaseQuery['livello'] = $selectedLevel;
}
if ($selectedSchool !== '') {
    $spellClassBaseQuery['scuola'] = $selectedSchool;
}
?>
        <div class="col">
        <article class="result-card spell-card card h-100 position-relative" data-favorite-dataset="spells" data-favorite-id="<?= e($spell['id']) ?>" data-export="<?= e(json_encode($exportData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)) ?>" data-sort-name="<?= e($spell['name']) ?>" data-sort-category="<?= e($spell['school']) ?>" data-sort-weight="0" data-sort-value="0" data-search="<?= e(implode(' ', [$spell['name'], $spell['school'], implode(' ', $spell['classes']), $spell['casting_time'], $spell['range'], $spell['components'], $spell['duration'], $spell['higher_level']])) ?>">
            <div class="card-header d-flex align-items-start justify-content-between gap-3">
                <div>
                    <a class="spell-level spell-filter-link" href="<?= e(appUrl('incantesimi?' . http_build_query($spellLevelQuery))) ?>"><?= $spell['level'] === 0 ? 'Trucchetto' : e((string) $spell['level']) . '° livello' ?></a>
                    <h3><?= e($spell['name']) ?></h3>
                    <p class="spell-classes mb-0">
<?php foreach ($spell['classes'] as $classIndex => $class): ?>
                        <?php $spellClassQuery = $spellClassBaseQuery + ['classe' => $class]; ?>
                        <a class="spell-filter-link" href="<?= e(appUrl('incantesimi?' . http_build_query($spellClassQuery))) ?>"><?= e(ucfirst($class)) ?></a><?= $classIndex < count($spell['classes']) - 1 ? ' · ' : '' ?>
<?php endforeach; ?>
                    </p>
                </div>
                <a class="spell-school spell-filter-link" href="<?= e(appUrl('incantesimi?' . http_build_query($spellSchoolQuery))) ?>"><?= e($spell['school']) ?></a>
            </div>
            <div class="card-body">
            <div class="spell-stats row g-2 mt-0 mb-3 position-relative">
<?php if ($spell['damage'] !== null): ?>
                <div class="col-6 d-flex gap-2 align-items-start small"><span class="flex-shrink-0" aria-hidden="true">⚔️</span><div><span class="weapon-label">Danni base</span><span class="weapon-value"><?= e($spell['damage']) ?></span></div></div>
<?php endif; ?>
                <div class="col-6 d-flex gap-2 align-items-start small"><span class="flex-shrink-0" aria-hidden="true">🎯</span><div><span class="weapon-label">Gittata</span><span class="weapon-value"><?= e($spell['range']) ?></span></div></div>
<?php if ($spell['concentration']): ?>
                <span class="spell-concentration position-absolute top-0 end-0 fs-4 lh-1" title="Concentrazione" aria-label="Concentrazione" role="img">🌀</span>
<?php endif; ?>
<?php if ($spell['area_diameter'] !== null): ?>
                <div class="col-6 d-flex gap-2 align-items-start small"><span class="flex-shrink-0" aria-hidden="true">💥</span><div><span class="weapon-label">Area</span><span class="weapon-value"><?= e($spell['area_diameter']) ?> m</span></div></div>
<?php endif; ?>
                <div class="col-6 d-flex gap-2 align-items-start small"><span class="flex-shrink-0" aria-hidden="true">⏱️</span><div><span class="weapon-label">Tempo</span><span class="weapon-value"><?= e($spell['casting_time']) ?></span></div></div>
                <div class="col-6 d-flex gap-2 align-items-start small"><span class="flex-shrink-0" aria-hidden="true">⌛</span><div><span class="weapon-label">Durata</span><span class="weapon-value"><?= e($spell['duration']) ?></span></div></div>
                <div class="col-6 d-flex gap-2 align-items-start small"><span class="flex-shrink-0" aria-hidden="true">🧩</span><div><span class="weapon-label">Componenti</span><span class="weapon-value"><?= e($spell['components']) ?></span></div></div>
            </div>
            <div class="spell-description"><?= renderSpellContent($spell) ?></div>
<?php if ($spell['higher_level'] !== ''): ?>
            <div class="spell-higher"><strong>A slot superiore:</strong> <?= renderSpellHigherLevel($spell) ?></div>
<?php endif; ?>
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
        <p>Prova a modificare la ricerca o a scegliere un altro filtro.</p>
    </div>
</section>
<?php $paginationPosition = 'bottom'; require __DIR__ . '/../partials/pagination.php'; ?>
