<aside class="sidebar" aria-label="Navigazione principale">
    <div class="sidebar-section">
        <p class="sidebar-label">Esplora</p>
        <nav class="main-nav">
            <a class="nav-item <?= $route === 'home' ? 'is-active' : '' ?>" href="<?= e(appUrl()) ?>">
                <span class="nav-icon" aria-hidden="true">🔎</span>
                Tutto
            </a>
            <div class="nav-group <?= $route === 'incantesimi' ? 'is-active' : '' ?>">
                <a class="nav-item <?= $route === 'incantesimi' ? 'is-active' : '' ?>" href="<?= e(appUrl('incantesimi')) ?>">
                    <span class="nav-icon" aria-hidden="true">✨</span>
                    Incantesimi
                    <span class="nav-count"><?= count($spellsData['items'] ?? []) ?: '' ?></span>
                </a>
            <?php
            $spellLevelLabels = [
                'all' => 'Tutti',
                '0' => 'Trucchetti',
                '1' => '1° livello',
                '2' => '2° livello',
                '3' => '3° livello',
                '4' => '4° livello',
                '5' => '5° livello',
                '6' => '6° livello',
                '7' => '7° livello',
                '8' => '8° livello',
                '9' => '9° livello'
            ];
            $activeSpellLevel = $route === 'incantesimi' ? (string) ($_GET['livello'] ?? 'all') : '';
            ?>
            <div class="nav-submenu" aria-label="Livelli degli incantesimi">
<?php foreach ($spellLevelLabels as $level => $label): ?>
                <a class="nav-subitem <?= $route === 'incantesimi' && $activeSpellLevel === (string) $level ? 'is-active' : '' ?>" href="<?= e(appUrl('incantesimi' . ($level === 'all' ? '' : '?livello=' . $level))) ?>">
                    <span class="nav-subitem-marker" aria-hidden="true"><?= $level === 'all' ? '✦' : ($level === '0' ? '•' : $level) ?></span>
                    <?= e($label) ?>
                </a>
<?php endforeach; ?>
            </div>
            </div>
            <a class="nav-item <?= $route === 'mostri' ? 'is-active' : '' ?>" href="<?= e(appUrl('mostri')) ?>">
                <span class="nav-icon" aria-hidden="true">👹</span>
                Mostri
                <span class="nav-count"><?= $monsterCount ?: '' ?></span>
            </a>
            <div class="nav-group <?= str_starts_with($route, 'oggetti') ? 'is-active' : '' ?>">
                <a class="nav-item <?= str_starts_with($route, 'oggetti') ? 'is-active' : '' ?>" href="<?= e(appUrl('oggetti')) ?>">
                    <span class="nav-icon" aria-hidden="true">🎒</span>
                    Oggetti
                    <span class="nav-count"><?= $objectCount ?: '' ?></span>
                </a>
                <div class="nav-submenu" aria-label="Categorie oggetti">
                <a class="nav-subitem <?= $route === 'oggetti/armi' ? 'is-active' : '' ?>" href="<?= e(appUrl('oggetti/armi')) ?>">
                    <span class="nav-subitem-marker" aria-hidden="true">⚔️</span>
                    Armi
                </a>
                <a class="nav-subitem <?= $route === 'oggetti/armature' ? 'is-active' : '' ?>" href="<?= e(appUrl('oggetti/armature')) ?>">
                    <span class="nav-subitem-marker" aria-hidden="true">🛡️</span>
                    Armature
                </a>
                <a class="nav-subitem <?= $route === 'oggetti/munizioni' ? 'is-active' : '' ?>" href="<?= e(appUrl('oggetti/munizioni')) ?>">
                    <span class="nav-subitem-marker" aria-hidden="true">🎯</span>
                    Munizioni
                </a>
                <a class="nav-subitem <?= $route === 'oggetti/strumenti' ? 'is-active' : '' ?>" href="<?= e(appUrl('oggetti/strumenti')) ?>">
                    <span class="nav-subitem-marker" aria-hidden="true">🔧</span>
                    Strumenti
                </a>
                <a class="nav-subitem <?= $route === 'oggetti/avventura' ? 'is-active' : '' ?>" href="<?= e(appUrl('oggetti/avventura')) ?>">
                    <span class="nav-subitem-marker" aria-hidden="true">🎒</span>
                    Equipaggiamento
                </a>
                </div>
            </div>
        </nav>
    </div>

    <div class="sidebar-section sidebar-bottom">
        <button class="sidebar-link sidebar-link-button" type="button" data-source-dialog>Fonti e Licenza</button>
    </div>
</aside>
