<section class="home-view">
    <div class="hero">
        <div>
            <p class="eyebrow">Archivio SRD · Italiano</p>
            <h1>Cosa stai cercando?</h1>
            <p class="hero-copy">Scegli un’area per consultare rapidamente regole, incantesimi, mostri e oggetti.</p>
        </div>
        <span class="hero-decoration" aria-hidden="true">✦</span>
    </div>

    <div class="area-grid">
        <article class="area-card area-card-spells">
            <div class="area-card-heading">
                <div class="area-card-icon" aria-hidden="true">✨</div>
                <div>
                    <h2>Incantesimi</h2>
                </div>
            </div>
            <p>Consulta incantesimi per livello, scuola e classe.</p>
            <div class="category-buttons home-card-links spell-level-links">
                <a class="taxonomy-filter" href="<?= e(appUrl('incantesimi')) ?>">Tutti <span><?= $spellsCount ?></span></a>
                <a class="taxonomy-filter" href="<?= e(appUrl('incantesimi?livello=0')) ?>">Trucchetti</a>
<?php for ($level = 1; $level <= 9; $level++): ?>
                <a class="taxonomy-filter" href="<?= e(appUrl('incantesimi?livello=' . $level)) ?>"><?= $level ?>° Liv.</a>
<?php endfor; ?>
            </div>
        </article>
        <article class="area-card area-card-monsters">
            <div class="area-card-heading">
                <div class="area-card-icon" aria-hidden="true">👹</div>
                <div>
                    <h2>Mostri</h2>
                </div>
            </div>
            <p>Trova blocchi statistiche per tipo e grado di sfida.</p>
            <div class="category-buttons home-card-links"><a class="taxonomy-filter" href="<?= e(appUrl('mostri')) ?>">Tutti i mostri <span><?= $monsterCount ?></span></a></div>
        </article>
        <article class="area-card area-card-items">
            <div class="area-card-heading">
                <div class="area-card-icon" aria-hidden="true">🎒</div>
                <div>
                    <h2>Oggetti</h2>
                </div>
            </div>
            <p>Esplora armi, armature, strumenti e oggetti magici.</p>
            <div class="category-buttons home-card-links">
                <a class="taxonomy-filter" href="<?= e(appUrl('oggetti')) ?>">Tutti gli oggetti <span><?= $objectCount ?></span></a>
                <a class="taxonomy-filter" href="<?= e(appUrl('oggetti/armi')) ?>">Armi <span>→</span></a>
                <a class="taxonomy-filter" href="<?= e(appUrl('oggetti/armature')) ?>">Armature <span>→</span></a>
                <a class="taxonomy-filter" href="<?= e(appUrl('oggetti/strumenti')) ?>">Strumenti <span>→</span></a>
                <a class="taxonomy-filter" href="<?= e(appUrl('oggetti/avventura')) ?>">Equipaggiamento <span>→</span></a>
            </div>
        </article>
    </div>

    <div class="taxonomy-note">
        <span class="taxonomy-note-icon">📚</span>
        <div>
            <strong>Archivio SRD 5.2.1 in italiano</strong>
            <p>Usa la ricerca globale nella barra superiore oppure apri una sezione per filtrare e ordinare i contenuti.</p>
        </div>
    </div>
</section>
