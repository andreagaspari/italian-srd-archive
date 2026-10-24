<section class="home-view">

    <div class="hero">
        <div>
            <h1>Italian SRD 5.2.1 Archive</h1>
            <p>Questo archivio raccoglie i dati localizzati in italiano del System Reference Document (SRD 5.2.1) di <i>Wizards of the Coast</i>, compatibili con la <strong>v5.5 (2024)</strong>. I dati sono stati estratti mediante <i>strumenti di AI</i>, pertanto potrebbero contenere <strong>imprecisioni o errori</strong>.</p>
            <p>Per iniziare, usa la <strong>ricerca globale</strong> nella barra superiore oppure apri una sezione dal menu per <strong>filtrare e ordinare i contenuti</strong>.</p>
            <p><i><strong>NB:</strong> Le funzioni di esportazione sono pensate per essere utilizzate con <a href="https://le12avventure.com/pages/makeamaster" target="_blank" rel="noopener noreferrer">MakeAMaster</a> di <i>Le 12 Avventure</i>.</i></p>
        </div>
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
            <strong class="home-card-label">Livelli</strong>
            <div class="category-buttons home-card-links spell-level-links">
                <a class="taxonomy-filter" href="<?= e(appUrl('incantesimi')) ?>">Tutti <span><?= $spellsCount ?></span></a>
<?php foreach ($spellCategories as $category): ?>
                <a class="taxonomy-filter" href="<?= e(appUrl($category['route'])) ?>"><?= e($category['label']) ?> <span><?= $category['count'] ?></span></a>
<?php endforeach; ?>
            </div>
            <strong class="home-card-label">Classi</strong>
            <div class="category-buttons home-card-links spell-class-links">
<?php foreach ($spellClassCategories as $category): ?>
                <a class="taxonomy-filter" href="<?= e(appUrl($category['route'])) ?>"><?= e($category['label']) ?> <span><?= $category['count'] ?></span></a>
<?php endforeach; ?>
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
            <div class="category-buttons home-card-links">
                <a class="taxonomy-filter" href="<?= e(appUrl('mostri')) ?>">Tutti i mostri <span><?= $monsterCount ?></span></a>
<?php foreach ($monsterCategories as $category): ?>
                <a class="taxonomy-filter" href="<?= e(appUrl($category['route'])) ?>"><?= e($category['label']) ?> <span><?= $category['count'] ?></span></a>
<?php endforeach; ?>
            </div>
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
<?php foreach ($objectCategories as $category): ?>
                <a class="taxonomy-filter" href="<?= e(appUrl($category['route'])) ?>"><?= e($category['label']) ?> <span><?= $category['count'] ?></span></a>
<?php endforeach; ?>
            </div>
        </article>
    </div>

</section>
