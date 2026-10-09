<section class="results-view spell-list-view">
    <div class="breadcrumb">
        <a href="<?= e(appUrl('incantesimi')) ?>">Incantesimi</a>
        <span>/</span>
        <strong><?= e($pageTitle) ?></strong>
    </div>

    <div class="section-heading">
        <div>
            <h1><?= e($pageTitle) ?></h1>
        </div>
    </div>
    <?php require __DIR__ . '/../objects/search.php'; ?>

    <nav class="taxonomy-filters" aria-label="Livello degli incantesimi">
<?php foreach ($levelLabels as $level => $label): ?>
        <?php $query = ['livello' => $level]; if ($selectedSchool !== '') $query['scuola'] = $selectedSchool; if ($selectedClass !== '') $query['classe'] = $selectedClass; ?>
        <a class="taxonomy-filter <?= (string) $level === $selectedLevel ? 'is-selected' : '' ?>" href="<?= e(appUrl('incantesimi?' . http_build_query($query))) ?>"><?= e($label) ?></a>
<?php endforeach; ?>
    </nav>

    <form class="spell-filters" method="get" action="<?= e(appUrl('incantesimi')) ?>">
        <label>
            <span>Scuola</span>
            <select class="spell-filter-select" name="scuola">
                <?php foreach ($schoolLabels as $school => $label): ?>
                    <option value="<?= e($school) ?>" <?= $school === $selectedSchool ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>
            <span>Classe</span>
            <select class="spell-filter-select" name="classe">
                <option value="">Tutte le classi</option>
                <?php foreach ($spellClasses as $class): ?>
                    <option value="<?= e($class) ?>" <?= $class === $selectedClass ? 'selected' : '' ?>><?= e(ucfirst($class)) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <?php if ($selectedLevel !== 'all' || $selectedSchool !== '' || $selectedClass !== ''): ?>
            <a class="button-secondary spell-reset-filters" href="<?= e(appUrl('incantesimi')) ?>">Resetta filtri</a>
        <?php endif; ?>
        <?php if ($selectedLevel !== 'all'): ?><input type="hidden" name="livello" value="<?= e($selectedLevel) ?>"><?php endif; ?>
    </form>

    <div class="result-summary"><strong id="result-count"><?= count($spells) ?></strong> incantesimi trovati</div>
    <div id="results-grid" class="results-grid">
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
        <article class="result-card spell-card" data-export="<?= e(json_encode($exportData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)) ?>" data-sort-name="<?= e($spell['name']) ?>" data-sort-category="<?= e($spell['school']) ?>" data-sort-weight="0" data-sort-value="0" data-search="<?= e(implode(' ', [$spell['name'], $spell['school'], implode(' ', $spell['classes']), $spell['casting_time'], $spell['range'], $spell['components'], $spell['duration'], $spell['description'], $spell['higher_level']])) ?>">
            <div class="spell-heading">
                <div>
                    <a class="spell-level spell-filter-link" href="<?= e(appUrl('incantesimi?' . http_build_query($spellLevelQuery))) ?>"><?= $spell['level'] === 0 ? 'Trucchetto' : e((string) $spell['level']) . '° livello' ?></a>
                    <h3><?= e($spell['name']) ?></h3>
                </div>
                <a class="spell-school spell-filter-link" href="<?= e(appUrl('incantesimi?' . http_build_query($spellSchoolQuery))) ?>"><?= e($spell['school']) ?></a>
            </div>
            <p class="spell-classes">
<?php foreach ($spell['classes'] as $classIndex => $class): ?>
                <?php $spellClassQuery = $spellClassBaseQuery + ['classe' => $class]; ?>
                <a class="spell-filter-link" href="<?= e(appUrl('incantesimi?' . http_build_query($spellClassQuery))) ?>"><?= e(ucfirst($class)) ?></a><?= $classIndex < count($spell['classes']) - 1 ? ' · ' : '' ?>
<?php endforeach; ?>
            </p>
            <div class="weapon-details spell-details">
<?php if ($spell['damage'] !== null): ?>
                <div class="weapon-detail"><span class="weapon-icon" aria-hidden="true">⚔️</span><div><span class="weapon-label">Danni base</span><span class="weapon-value"><?= e($spell['damage']) ?></span></div></div>
<?php endif; ?>
                <div class="weapon-detail"><span class="weapon-icon" aria-hidden="true">🎯</span><div><span class="weapon-label">Gittata</span><span class="weapon-value"><?= e($spell['range']) ?></span></div></div>
<?php if ($spell['area_diameter'] !== null): ?>
                <div class="weapon-detail"><span class="weapon-icon" aria-hidden="true">💥</span><div><span class="weapon-label">Area</span><span class="weapon-value"><?= e($spell['area_diameter']) ?> m</span></div></div>
<?php endif; ?>
                <div class="weapon-detail"><span class="weapon-icon" aria-hidden="true">⏱️</span><div><span class="weapon-label">Tempo</span><span class="weapon-value"><?= e($spell['casting_time']) ?></span></div></div>
                <div class="weapon-detail"><span class="weapon-icon" aria-hidden="true">⌛</span><div><span class="weapon-label">Durata</span><span class="weapon-value"><?= e($spell['duration']) ?></span></div></div>
<?php if ($spell['concentration']): ?>
                <div class="weapon-detail"><span class="weapon-icon" aria-hidden="true">🌀</span><div><span class="weapon-label">Concentrazione</span></div></div>
<?php endif; ?>
                <div class="weapon-detail"><span class="weapon-icon" aria-hidden="true">🧩</span><div><span class="weapon-label">Componenti</span><span class="weapon-value"><?= e($spell['components']) ?></span></div></div>
            </div>
            <div class="spell-description"><?= e($spell['description']) ?></div>
<?php if ($spell['higher_level'] !== ''): ?>
            <div class="spell-higher"><strong>A slot superiore:</strong> <?= e($spell['higher_level']) ?></div>
<?php endif; ?>
            <button class="export-button" type="button">⇩ Esporta</button>
        </article>
<?php endforeach; ?>
    </div>
    <div id="empty-state" class="empty-state" hidden>
        <span class="empty-icon">⌕</span>
        <h3>Nessun risultato</h3>
        <p>Prova a modificare la ricerca o a scegliere un altro filtro.</p>
    </div>
</section>
<?php require __DIR__ . '/../partials/pagination.php'; ?>
