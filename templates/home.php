<section class="home-view container-fluid px-0">

    <div class="hero">
        <div>
            <h1>Italian SRD 5.2.1 Archive</h1>
            <p>Questo archivio raccoglie i dati localizzati in italiano del System Reference Document (SRD 5.2.1) di <i>Wizards of the Coast</i>, compatibili con la <strong>v5.5 (2024)</strong>. I dati sono stati estratti mediante <i>strumenti di AI</i>, pertanto potrebbero contenere <strong>imprecisioni o errori</strong>.</p>
            <p>Per iniziare, usa la <strong>ricerca globale</strong> nella barra superiore oppure apri una sezione dal menu per <strong>filtrare e ordinare i contenuti</strong>.</p>
            <p><i><strong>NB:</strong> Le funzioni di esportazione sono pensate per essere utilizzate con <a href="https://le12avventure.com/pages/makeamaster" target="_blank" rel="noopener noreferrer">MakeAMaster</a> di <i>Le 12 Avventure</i>.</i></p>
        </div>
    </div>

    <div class="area-grid row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4 align-items-start">
        <div class="col">
        <article class="area-card area-card-spells card h-100 d-flex flex-column shadow-sm">
            <div class="area-card-heading card-header d-flex align-items-center gap-2 p-3">
                <div class="area-card-icon fs-4" aria-hidden="true">✨</div>
                <div>
                    <h2 class="fs-4 mb-0">Incantesimi</h2>
                </div>
            </div>
            <div class="card-body d-flex flex-column p-3 p-lg-4">
            <p>Consulta incantesimi per livello, scuola e classe.</p>
            <strong class="home-card-label">Livelli</strong>
            <div class="category-buttons row row-cols-1 row-cols-sm-2 g-2 home-card-links spell-level-links">
                <a class="taxonomy-filter btn btn-outline-secondary" href="<?= e(appUrl('incantesimi')) ?>">Tutti <span><?= $spellsCount ?></span></a>
<?php foreach ($spellCategories as $category): ?>
                <a class="taxonomy-filter btn btn-outline-secondary" href="<?= e(appUrl($category['route'])) ?>"><?= e($category['label']) ?> <span><?= $category['count'] ?></span></a>
<?php endforeach; ?>
            </div>
            <strong class="home-card-label">Classi</strong>
            <div class="category-buttons row row-cols-1 row-cols-sm-2 g-2 home-card-links spell-class-links">
<?php foreach ($spellClassCategories as $category): ?>
                <a class="taxonomy-filter btn btn-outline-secondary" href="<?= e(appUrl($category['route'])) ?>"><?= e($category['label']) ?> <span><?= $category['count'] ?></span></a>
<?php endforeach; ?>
            </div>
            </div>
        </article>
        </div>
        <div class="col">
        <article class="area-card area-card-monsters card h-100 d-flex flex-column shadow-sm">
            <div class="area-card-heading card-header d-flex align-items-center gap-2 p-3">
                <div class="area-card-icon fs-4" aria-hidden="true">👹</div>
                <div>
                    <h2 class="fs-4 mb-0">Mostri</h2>
                </div>
            </div>
            <div class="card-body d-flex flex-column p-3 p-lg-4">
            <p>Trova blocchi statistiche per tipo e grado di sfida.</p>
            <div class="category-buttons row row-cols-1 row-cols-sm-2 g-2 home-card-links">
                <a class="taxonomy-filter btn btn-outline-secondary" href="<?= e(appUrl('mostri')) ?>">Tutti i mostri <span><?= $monsterCount ?></span></a>
<?php foreach ($monsterCategories as $category): ?>
                <a class="taxonomy-filter btn btn-outline-secondary" href="<?= e(appUrl($category['route'])) ?>"><?= e($category['label']) ?> <span><?= $category['count'] ?></span></a>
<?php endforeach; ?>
            </div>
            </div>
        </article>
        </div>
        <div class="col">
        <article class="area-card area-card-items card h-100 d-flex flex-column shadow-sm">
            <div class="area-card-heading card-header d-flex align-items-center gap-2 p-3">
                <div class="area-card-icon fs-4" aria-hidden="true">🎒</div>
                <div>
                    <h2 class="fs-4 mb-0">Oggetti</h2>
                </div>
            </div>
            <div class="card-body d-flex flex-column p-3 p-lg-4">
            <p>Esplora armi, armature, strumenti e oggetti magici.</p>
            <div class="category-buttons row row-cols-1 row-cols-sm-2 g-2 home-card-links">
<?php foreach ($objectCategories as $category): ?>
                <a class="taxonomy-filter btn btn-outline-secondary" href="<?= e(appUrl($category['route'])) ?>"><?= e($category['label']) ?> <span><?= $category['count'] ?></span></a>
<?php endforeach; ?>
            </div>
            </div>
        </article>
        </div>
    </div>

</section>
