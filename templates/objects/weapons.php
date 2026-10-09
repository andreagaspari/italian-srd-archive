<section class="results-view weapon-list-view">
    <div class="breadcrumb">
        <a class="breadcrumb-home" href="<?= e(appUrl('oggetti')) ?>">Oggetti</a>
        <span>/</span>
        <a href="<?= e(appUrl('oggetti/armi')) ?>">Armi</a>
        <span>/</span>
        <strong><?= e($pageTitle) ?></strong>
    </div>

    <div class="section-heading">
        <div>
            <h1><?= e($pageTitle) ?></h1>
        </div>
    </div>
    <?php require __DIR__ . '/search.php'; ?>
    <nav class="taxonomy-filters" aria-label="Categorie armi">
<?php foreach ($categoryLabels as $category => $label): ?>
        <a class="taxonomy-filter <?= $category === $selectedCategory ? 'is-selected' : '' ?>" href="<?= e(appUrl('oggetti/armi?categoria=' . urlencode($category))) ?>"><?= e($label) ?></a>
<?php endforeach; ?>
    </nav>

    <div class="result-summary"><strong id="result-count"><?= count($filteredWeapons) ?></strong> elementi trovati</div>

    <div id="results-grid" class="results-grid">
<?php foreach ($filteredWeapons as $weapon): ?>
<?php
    [$damageDice, $damageType] = array_pad(explode(' ', $weapon['damage'], 2), 2, '');
    $exportRange = 'Corpo a corpo';
    if ($weapon['mode'] === 'A distanza') {
        $exportRange = 'A distanza';
        foreach ($weapon['properties'] as $property) {
            if (str_contains($property, 'Munizioni (') || str_contains($property, 'Lancio (')) {
                $exportRange = (string) preg_replace('/\bgittata\s+/u', '', $property);
                break;
            }
        }
    }
    $exportDescription = '';
    if ($weapon['properties']) {
        $exportDescription .= '<div><b>Proprietà:</b> ' . implode(' · ', $weapon['properties']) . '</div>';
    }
    $exportDescription .= '<div><b>Padronanza:</b> ' . $weapon['mastery'] . '</div>';
    $exportData = buildObjectExportData($weapon['name'], $weapon['cost'], $weapon['weight'], $exportDescription, $weapon['damage'], $exportRange);
    $weaponCategory = match ($weapon['subcategory']) {
        'Armi da mischia semplici' => 'items-weapons-simple-melee',
        'Armi a distanza semplici' => 'items-weapons-simple-ranged',
        'Armi da mischia da guerra' => 'items-weapons-martial-melee',
        'Armi a distanza da guerra' => 'items-weapons-martial-ranged',
        default => 'items-weapons'
    };
    $propertyTooltips = [
        'Accurata' => 'Puoi usare Forza o Destrezza per i tiri per colpire e i tiri per i danni.',
        'Due mani' => 'Questa arma richiede due mani quando effettui un attacco.',
        'Leggera' => 'Quando attacchi con un’arma leggera, puoi effettuare un attacco aggiuntivo con un’altra arma leggera nella stessa azione.',
        'Munizioni' => 'Puoi effettuare un attacco a distanza solo se hai le munizioni richieste. La gittata e il tipo di munizione sono indicati tra parentesi.',
        'Pesante' => 'Le creature di taglia Piccola o minuscola subiscono svantaggio ai tiri per colpire con quest’arma.',
        'Portata' => 'La portata aumenta di 1,5 metri quando attacchi con quest’arma e quando determini la portata per gli attacchi di opportunità.',
        'Ricarica' => 'Puoi effettuare un solo attacco con quest’arma quando usi un’azione, un’azione bonus o una reazione per attaccare, indipendentemente dal numero di attacchi che puoi effettuare normalmente.',
        'Lancio' => 'Puoi lanciare quest’arma per effettuare un attacco a distanza. La gittata è indicata tra parentesi.',
        'Versatile' => 'Quest’arma può essere impugnata con una o due mani. Il valore tra parentesi indica i danni quando viene usata a due mani.'
    ];
    $masteryTooltips = [
        'Doppio fendente' => 'Se colpisci una creatura con quest’arma, puoi effettuare un attacco in mischia contro un’altra creatura entro 1,5 metri dal primo bersaglio, senza aggiungere il modificatore di caratteristica ai danni.',
        'Fiaccare' => 'Se colpisci una creatura, il prossimo tiro per colpire che effettuerà contro di essa prima dell’inizio del tuo turno successivo ha svantaggio.',
        'Colpo di striscio' => 'Se manchi un tiro per colpire con quest’arma, il bersaglio subisce danni pari al modificatore di caratteristica usato per l’attacco.',
        'Graffio' => 'Quando effettui l’attacco aggiuntivo concesso dalla proprietà Leggera, puoi effettuare quell’attacco come parte dell’azione Attacco invece che come azione bonus. Puoi usare questo beneficio una sola volta per turno.',
        'Lentezza' => 'Se colpisci una creatura, la sua velocità si riduce di 3 metri fino all’inizio del tuo turno successivo.',
        'Rovesciamento' => 'Se colpisci una creatura, può dover effettuare un tiro salvezza su Costituzione o cadere a terra prono.',
        'Spinta' => 'Se colpisci una creatura, puoi spingerla di 3 metri lontano da te, se è di taglia Grande o inferiore.',
        'Vessazione' => 'Se colpisci una creatura, hai vantaggio sul prossimo tiro per colpire che effettui contro di essa prima della fine del tuo turno successivo.'
    ];
?>
        <article class="result-card" data-export="<?= e(json_encode($exportData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)) ?>" data-sort-name="<?= e($weapon['name']) ?>" data-sort-category="<?= e($weapon['subcategory']) ?>" data-sort-weight="<?= e($weapon['weight']) ?>" data-sort-value="<?= e($weapon['cost']) ?>" data-search="<?= e(implode(' ', [
            $weapon['name'],
            $weapon['proficiency'],
            $weapon['mode'],
            $weapon['subcategory'],
            $weapon['damage'],
            implode(' ', $weapon['properties']),
            $weapon['mastery']
        ])) ?>">
            <div class="weapon-category">
                <a class="weapon-category-main" href="<?= e(appUrl('oggetti/armi')) ?>">Armi</a>
                <span class="weapon-category-arrow">→</span>
                <a class="weapon-category-subcategory" href="<?= e(appUrl('oggetti/armi?categoria=' . $weaponCategory)) ?>"><?= e($weapon['subcategory']) ?></a>
            </div>
            <div class="weapon-heading">
                <h3><?= e($weapon['name']) ?></h3>
                <span class="weapon-mode"><?= e($weapon['mode']) ?></span>
            </div>
            <div class="weapon-damage">
                <span class="weapon-icon" aria-hidden="true">⚔️</span>
                <strong><?= e($damageDice) ?></strong>
                <span><?= e(ucfirst($damageType)) ?></span>
            </div>
            <div class="weapon-details">
                <div class="weapon-detail weapon-properties">
                    <span class="weapon-icon" aria-hidden="true">🧩</span>
                    <div>
                        <span class="weapon-label">Proprietà</span>
                        <span class="weapon-value weapon-tooltip-list">
<?php if ($weapon['properties']): ?>
<?php foreach ($weapon['properties'] as $propertyIndex => $property): ?>
<?php
    $propertyName = trim((string) preg_replace('/\s*\(.*/', '', $property));
    $propertyTooltip = $propertyTooltips[$propertyName] ?? 'Questa proprietà modifica il modo in cui l’arma può essere utilizzata.';
?>
                            <button class="tooltip-term" type="button" aria-label="<?= e($property . ': ' . $propertyTooltip) ?>">
                                <?= e($property) ?>
                                <span class="tooltip-box" role="tooltip"><?= e($propertyTooltip) ?></span>
                            </button><?= $propertyIndex < count($weapon['properties']) - 1 ? ' ·' : '' ?>
<?php endforeach; ?>
<?php else: ?>
                            Nessuna
<?php endif; ?>
                        </span>
                    </div>
                </div>
                <div class="weapon-detail">
                    <span class="weapon-icon" aria-hidden="true">✨</span>
                    <div>
                        <span class="weapon-label">Padronanza</span>
                        <span class="weapon-value">
                            <button class="tooltip-term" type="button" aria-label="<?= e($weapon['mastery'] . ': ' . $masteryTooltips[$weapon['mastery']]) ?>">
                                <?= e($weapon['mastery']) ?>
                                <span class="tooltip-box" role="tooltip"><?= e($masteryTooltips[$weapon['mastery']]) ?></span>
                            </button>
                        </span>
                    </div>
                </div>
                <div class="weapon-detail">
                    <span class="weapon-icon" aria-hidden="true">⚖️</span>
                    <div>
                        <span class="weapon-label">Peso</span>
                        <span class="weapon-value"><?= e($weapon['weight']) ?></span>
                    </div>
                </div>
                <div class="weapon-detail">
                    <span class="weapon-icon" aria-hidden="true">🪙</span>
                    <div>
                        <span class="weapon-label">Valore</span>
                        <span class="weapon-value"><?= e($weapon['cost']) ?></span>
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
        <p>Prova a modificare la ricerca o a scegliere un’altra categoria.</p>
    </div>
</section>
