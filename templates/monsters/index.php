<section class="results-view monster-list-view">
    <div class="breadcrumb"><strong>Mostri</strong></div>
    <div class="section-heading"><div><h1><?= e($pageTitle) ?></h1></div></div>
    <?php require __DIR__ . '/../objects/search.php'; ?>

<?php
$monsterFilterQuery = [];
if ($selectedMonsterSize !== '') {
    $monsterFilterQuery['taglia'] = $selectedMonsterSize;
}
if ($selectedMonsterChallenge !== '') {
    $monsterFilterQuery['gs'] = $selectedMonsterChallenge;
}
if ($selectedMonsterAlignment !== '') {
    $monsterFilterQuery['allineamento'] = $selectedMonsterAlignment;
}
$allMonsterTypesUrl = appUrl('mostri' . ($monsterFilterQuery ? '?' . http_build_query($monsterFilterQuery) : ''));
?>
    <nav class="taxonomy-filters monster-type-filters" aria-label="Specie dei mostri">
        <a class="taxonomy-filter <?= $selectedMonsterType === '' ? 'is-selected' : '' ?>" href="<?= e($allMonsterTypesUrl) ?>">Tutte le specie</a>
<?php foreach ($monsterTypes as $type => $label): ?>
<?php
    $typeFilterQuery = ['tipo' => $type] + $monsterFilterQuery;
?>
        <a class="taxonomy-filter <?= $type === $selectedMonsterType ? 'is-selected' : '' ?>" href="<?= e(appUrl('mostri?' . http_build_query($typeFilterQuery))) ?>"><?= e($label) ?></a>
<?php endforeach; ?>
    </nav>

    <form class="monster-filters" method="get" action="<?= e(appUrl('mostri')) ?>">
<?php if ($selectedMonsterType !== ''): ?>
        <input type="hidden" name="tipo" value="<?= e($selectedMonsterType) ?>">
<?php endif; ?>
        <label>
            <span>Taglia</span>
            <select class="monster-filter-select" name="taglia">
                <option value="">Tutte le taglie</option>
<?php foreach ($monsterSizes as $size => $label): ?>
                <option value="<?= e($size) ?>" <?= $size === $selectedMonsterSize ? 'selected' : '' ?>><?= e($label) ?></option>
<?php endforeach; ?>
            </select>
        </label>
        <label>
            <span>GS</span>
            <select class="monster-filter-select" name="gs">
                <option value="">Tutti i GS</option>
<?php foreach ($monsterChallenges as $challenge => $label): ?>
                <option value="<?= e((string) $challenge) ?>" <?= (string) $challenge === $selectedMonsterChallenge ? 'selected' : '' ?>><?= e($label) ?></option>
<?php endforeach; ?>
            </select>
        </label>
        <label>
            <span>Allineamento</span>
            <select class="monster-filter-select" name="allineamento">
                <option value="">Tutti gli allineamenti</option>
<?php foreach ($monsterAlignments as $alignment => $label): ?>
                <option value="<?= e($alignment) ?>" <?= $alignment === $selectedMonsterAlignment ? 'selected' : '' ?>><?= e($label) ?></option>
<?php endforeach; ?>
            </select>
        </label>
        <?php if ($selectedMonsterType !== '' || $selectedMonsterSize !== '' || $selectedMonsterChallenge !== '' || $selectedMonsterAlignment !== ''): ?>
            <a class="button-secondary monster-reset-filters" href="<?= e(appUrl('mostri')) ?>">Resetta filtri</a>
        <?php endif; ?>
    </form>

    <div class="result-summary"><strong id="result-count"><?= count($monsters) ?></strong> mostri trovati</div>
    <div id="results-grid" class="results-grid monster-results-grid">
<?php foreach ($monsters as $monster): ?>
<?php
    $monster['description_blocks'] = normalizeMonsterDescriptionBlocks($monster);
    $exportData = buildMonsterExportData($monster);
    $activeMonsterFilters = $monsterFilterQuery;
    if ($selectedMonsterType !== '') {
        $activeMonsterFilters['tipo'] = $selectedMonsterType;
    }
    $monsterTypeUrl = appUrl('mostri?' . http_build_query(array_merge($activeMonsterFilters, ['tipo' => $monster['type']])));
    $monsterChallengeUrl = appUrl('mostri?' . http_build_query(array_merge($activeMonsterFilters, ['gs' => $monster['challenge_rating']])));
    $monsterSizeUrl = $monster['size'] !== ''
        ? appUrl('mostri?' . http_build_query(array_merge($activeMonsterFilters, ['taglia' => $monster['size']])))
        : '';
    $monsterAlignmentUrl = appUrl('mostri?' . http_build_query(array_merge($activeMonsterFilters, ['allineamento' => $monster['alignment']])));
    $abilityStats = monsterAbilityStats($monster['abilities']);
    $hitPointsParts = preg_match('/^(\d+)(?:\s+\(([^)]+)\))?$/u', $monster['hit_points'], $hitPointsMatch) === 1
        ? $hitPointsMatch
        : [null, $monster['hit_points'], ''];
    $hitPointsTotal = $hitPointsParts[1];
    $hitDice = $hitPointsParts[2];
    $proficiencyBonus = '';
    foreach ($monster['extra_stats'] as $extraStat) {
        if ($extraStat['title'] === 'BC') {
            $proficiencyBonus = $extraStat['value'];
            break;
        }
    }
    $description = implode(' ', array_map(static fn (array $block): string => strip_tags($block['content']), $monster['description_blocks']));
    $searchText = implode(' ', [
        $monster['name'],
        $monster['type'],
        $monster['size'],
        $monster['alignment'],
        $monster['challenge_rating'],
        $monster['armor_class'],
        $monster['hit_points'],
        $monster['speed'],
        implode(' ', $monster['abilities']),
        $monster['skills'],
        $monster['vulnerabilities'],
        $monster['resistances'],
        $monster['immunities'],
        $monster['senses'],
        $monster['languages'],
        $description
    ]);
?>
        <article class="result-card monster-card" data-export="<?= e(json_encode($exportData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)) ?>" data-sort-name="<?= e($monster['name']) ?>" data-sort-category="<?= e($monster['type']) ?>" data-sort-weight="0" data-sort-value="<?= e($monster['challenge_rating']) ?>" data-search="<?= e($searchText) ?>">
        <div class="weapon-category"><a class="weapon-category-main monster-filter-link" href="<?= e($monsterTypeUrl) ?>"><?= e($monster['type']) ?></a></div>
        <div class="weapon-heading"><h3><?= e($monster['name']) ?></h3><a class="weapon-mode monster-filter-link" href="<?= e($monsterChallengeUrl) ?>">GS <?= e($monster['challenge_rating']) ?></a></div>
        <div class="weapon-details monster-details">
            <div class="weapon-detail"><span class="weapon-icon" aria-hidden="true">❤️</span><div><span class="weapon-label">Punti Ferita</span><span class="weapon-value"><?= e($hitPointsTotal) ?></span></div></div>
            <div class="weapon-detail"><span class="weapon-icon" aria-hidden="true">🛡️</span><div><span class="weapon-label">Classe Armatura</span><span class="weapon-value"><?= e($monster['armor_class']) ?></span></div></div>
            <div class="weapon-detail"><span class="weapon-icon" aria-hidden="true">⚡</span><div><span class="weapon-label">Iniziativa</span><span class="weapon-value"><?= e($monster['initiative']) ?></span></div></div>
            <div class="weapon-detail"><span class="weapon-icon" aria-hidden="true">👣</span><div><span class="weapon-label">Velocità</span><span class="weapon-value"><?= e($monster['speed']) ?></span></div></div>
            <?php if ($hitDice !== ''): ?><div class="weapon-detail"><span class="weapon-icon" aria-hidden="true">🎲</span><div><span class="weapon-label">Dadi Vita</span><span class="weapon-value"><?= e($hitDice) ?></span></div></div><?php endif; ?>
            <?php if ($proficiencyBonus !== ''): ?><div class="weapon-detail"><span class="weapon-icon" aria-hidden="true">📈</span><div><span class="weapon-label">Bonus di Competenza</span><span class="weapon-value"><?= e($proficiencyBonus) ?></span></div></div><?php endif; ?>
        </div>
        <div class="weapon-details monster-subdetails">
            <div class="weapon-detail">
                <span class="weapon-icon" aria-hidden="true">📏</span>
                <div>
                    <span class="weapon-label">Taglia</span>
                    <span class="weapon-value">
<?php if ($monsterSizeUrl !== ''): ?><a class="monster-filter-link" href="<?= e($monsterSizeUrl) ?>"><?= e($monster['size']) ?></a><?php else: ?>Non specificata<?php endif; ?>
                    </span>
                </div>
            </div>
            <div class="weapon-detail">
                <span class="weapon-icon" aria-hidden="true">⚖️</span>
                <div>
                    <span class="weapon-label">Allineamento</span>
                    <span class="weapon-value"><a class="monster-filter-link" href="<?= e($monsterAlignmentUrl) ?>"><?= e($monster['alignment']) ?></a></span>
                </div>
            </div>
        </div>
        <table class="monster-abilities">
            <tbody>
<?php foreach (array_chunk($abilityStats, 3) as $abilityRow): ?>
                <tr>
<?php foreach ($abilityRow as $ability): ?>
                    <td><div class="monster-ability-cell"><div class="monster-ability-legend"><span>MOD</span><span>SALV</span></div><div class="monster-ability-values"><span class="monster-ability-name"><?= e($ability['name']) ?></span><strong><?= e($ability['score']) ?></strong><span class="monster-ability-mod"><?= e($ability['modifier']) ?></span><span class="monster-ability-save"><?= e($ability['save']) ?></span></div></div></td>
<?php endforeach; ?>
                </tr>
<?php endforeach; ?>
            </tbody>
        </table>
<?php if ($monster['skills'] !== '' || $monster['vulnerabilities'] !== '' || $monster['resistances'] !== '' || $monster['immunities'] !== '' || $monster['senses'] !== '' || $monster['languages'] !== ''): ?>
            <div class="monster-meta">
<?php if ($monster['skills'] !== ''): ?><div><strong>Abilità:</strong> <?= e($monster['skills']) ?></div><?php endif; ?>
<?php if ($monster['vulnerabilities'] !== ''): ?><div><strong>Vulnerabilità:</strong> <?= e($monster['vulnerabilities']) ?></div><?php endif; ?>
<?php if ($monster['resistances'] !== ''): ?><div><strong>Resistenze:</strong> <?= e($monster['resistances']) ?></div><?php endif; ?>
<?php if ($monster['immunities'] !== ''): ?><div><strong>Immunità:</strong> <?= e($monster['immunities']) ?></div><?php endif; ?>
<?php if ($monster['senses'] !== ''): ?><div><strong>Sensi:</strong> <?= e($monster['senses']) ?></div><?php endif; ?>
<?php if ($monster['languages'] !== ''): ?><div><strong>Lingue:</strong> <?= e($monster['languages']) ?></div><?php endif; ?>
            </div>
<?php endif; ?>
<?php foreach ($monster['description_blocks'] as $block): ?>
<?php $features = monsterDescriptionFeatures($block['content']); ?>
            <section class="monster-block">
                <div class="weapon-detail">
                    <div>
                        <span class="weapon-label"><?= e($block['title']) ?></span>
                        <span class="weapon-value weapon-tooltip-list">
<?php foreach ($features as $featureIndex => $feature): ?>
                            <button class="tooltip-term" type="button" aria-label="<?= e($feature['label'] . ': ' . $feature['description']) ?>">
                                <?= e($feature['label']) ?>
                                <span class="tooltip-box" role="tooltip"><?= e($feature['description']) ?></span>
                            </button><?= $featureIndex < count($features) - 1 ? ' ·' : '' ?>
<?php endforeach; ?>
                        </span>
                    </div>
                </div>
            </section>
<?php endforeach; ?>
            <button class="export-button" type="button">⇩ Esporta</button>
        </article>
<?php endforeach; ?>
    </div>
    <div id="empty-state" class="empty-state" hidden><span class="empty-icon">⌕</span><h3>Nessun risultato</h3><p>Prova a modificare la ricerca o i filtri.</p></div>
</section>
