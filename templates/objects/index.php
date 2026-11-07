<section class="home-view container-fluid px-0">
    <div class="hero">
        <div>
            <h1>Oggetti</h1>
            <p class="hero-copy">Scegli una categoria per consultare gli elementi dell’equipaggiamento.</p>
        </div>
    </div>

    <div class="area-grid object-area-grid row g-3">
        <article class="area-card area-card-items card col h-100 p-4 shadow-sm">
            <div class="area-card-heading d-flex align-items-center gap-2 mb-3 p-2 rounded bg-primary-subtle">
                <div class="area-card-icon fs-4" aria-hidden="true">⚔️</div>
                <h2 class="fs-4 mb-0">Armi</h2>
            </div>
            <p>Armi semplici e da guerra, da mischia e a distanza.</p>
            <div class="category-buttons row row-cols-1 row-cols-sm-2 g-2">
                <a class="taxonomy-filter btn btn-sm btn-outline-secondary" href="<?= e(appUrl('oggetti/armi')) ?>">Tutte</a>
                <a class="taxonomy-filter btn btn-sm btn-outline-secondary" href="<?= e(appUrl('oggetti/armi?categoria=items-weapons-simple-melee')) ?>">Da mischia semplici</a>
                <a class="taxonomy-filter btn btn-sm btn-outline-secondary" href="<?= e(appUrl('oggetti/armi?categoria=items-weapons-simple-ranged')) ?>">A distanza semplici</a>
                <a class="taxonomy-filter btn btn-sm btn-outline-secondary" href="<?= e(appUrl('oggetti/armi?categoria=items-weapons-martial-melee')) ?>">Da mischia da guerra</a>
                <a class="taxonomy-filter btn btn-sm btn-outline-secondary" href="<?= e(appUrl('oggetti/armi?categoria=items-weapons-martial-ranged')) ?>">A distanza da guerra</a>
            </div>
        </article>
        <article class="area-card card col h-100 p-4 shadow-sm">
            <div class="area-card-heading d-flex align-items-center gap-2 mb-3 p-2 rounded bg-primary-subtle">
                <div class="area-card-icon fs-4" aria-hidden="true">🛡️</div>
                <h2 class="fs-4 mb-0">Armature</h2>
            </div>
            <p>Armature leggere, medie, pesanti e scudi.</p>
            <div class="category-buttons row row-cols-1 row-cols-sm-2 g-2">
                <a class="taxonomy-filter btn btn-sm btn-outline-secondary" href="<?= e(appUrl('oggetti/armature')) ?>">Tutte</a>
                <a class="taxonomy-filter btn btn-sm btn-outline-secondary" href="<?= e(appUrl('oggetti/armature?categoria=armor-light')) ?>">Leggere</a>
                <a class="taxonomy-filter btn btn-sm btn-outline-secondary" href="<?= e(appUrl('oggetti/armature?categoria=armor-medium')) ?>">Medie</a>
                <a class="taxonomy-filter btn btn-sm btn-outline-secondary" href="<?= e(appUrl('oggetti/armature?categoria=armor-heavy')) ?>">Pesanti</a>
                <a class="taxonomy-filter btn btn-sm btn-outline-secondary" href="<?= e(appUrl('oggetti/armature?categoria=armor-shields')) ?>">Scudi</a>
            </div>
        </article>
        <article class="area-card card col h-100 p-4 shadow-sm">
            <div class="area-card-heading d-flex align-items-center gap-2 mb-3 p-2 rounded bg-primary-subtle">
                <div class="area-card-icon fs-4" aria-hidden="true">🔧</div>
                <h2 class="fs-4 mb-0">Strumenti</h2>
            </div>
            <p>Strumenti da artigiano, musicali, giochi e altri strumenti.</p>
            <div class="category-buttons row row-cols-1 row-cols-sm-2 g-2">
                <a class="taxonomy-filter btn btn-sm btn-outline-secondary" href="<?= e(appUrl('oggetti/strumenti')) ?>">Tutti</a>
                <a class="taxonomy-filter btn btn-sm btn-outline-secondary" href="<?= e(appUrl('oggetti/strumenti?categoria=artigiano')) ?>">Artigiano</a>
                <a class="taxonomy-filter btn btn-sm btn-outline-secondary" href="<?= e(appUrl('oggetti/strumenti?categoria=giochi')) ?>">Giochi</a>
                <a class="taxonomy-filter btn btn-sm btn-outline-secondary" href="<?= e(appUrl('oggetti/strumenti?categoria=musicali')) ?>">Musicali</a>
                <a class="taxonomy-filter btn btn-sm btn-outline-secondary" href="<?= e(appUrl('oggetti/strumenti?categoria=altri')) ?>">Altri</a>
            </div>
        </article>
        <article class="area-card card col h-100 p-4 shadow-sm">
            <div class="area-card-heading d-flex align-items-center gap-2 mb-3 p-2 rounded bg-primary-subtle">
                <div class="area-card-icon fs-4" aria-hidden="true">🎒</div>
                <h2 class="fs-4 mb-0">Equipaggiamento</h2>
            </div>
            <p>Contenitori, consumabili, illuminazione e oggetti utili durante i viaggi.</p>
            <div class="category-buttons row row-cols-1 row-cols-sm-2 g-2">
                <a class="taxonomy-filter btn btn-sm btn-outline-secondary" href="<?= e(appUrl('oggetti/avventura')) ?>">Tutti</a>
                <a class="taxonomy-filter btn btn-sm btn-outline-secondary" href="<?= e(appUrl('oggetti/avventura?categoria=avventura')) ?>">Avventura</a>
                <a class="taxonomy-filter btn btn-sm btn-outline-secondary" href="<?= e(appUrl('oggetti/avventura?categoria=consumabili')) ?>">Consumabili</a>
                <a class="taxonomy-filter btn btn-sm btn-outline-secondary" href="<?= e(appUrl('oggetti/avventura?categoria=contenitori')) ?>">Contenitori</a>
                <a class="taxonomy-filter btn btn-sm btn-outline-secondary" href="<?= e(appUrl('oggetti/avventura?categoria=illuminazione')) ?>">Illuminazione</a>
            </div>
        </article>
        <article class="area-card card col h-100 p-4 shadow-sm">
            <div class="area-card-heading d-flex align-items-center gap-2 mb-3 p-2 rounded bg-primary-subtle">
                <div class="area-card-icon fs-4" aria-hidden="true">🎯</div>
                <h2 class="fs-4 mb-0">Munizioni</h2>
            </div>
            <p>Munizioni acquistabili per archi, balestre, fionde e armi da fuoco.</p>
            <div class="category-buttons row row-cols-1 row-cols-sm-2 g-2">
                <a class="taxonomy-filter btn btn-sm btn-outline-secondary" href="<?= e(appUrl('oggetti/munizioni')) ?>">Tutte le munizioni</a>
            </div>
        </article>
    </div>
</section>
