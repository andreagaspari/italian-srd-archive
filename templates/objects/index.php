<section class="object-index-view container-fluid px-0">
    <div class="section-heading mb-4">
        <div>
            <h1>Oggetti</h1>
            <p class="hero-copy">Scegli una categoria per consultare gli elementi dell’equipaggiamento.</p>
        </div>
    </div>

    <div class="area-grid object-area-grid row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4 align-items-start">
        <div class="col">
        <article class="area-card area-card-items card h-100 d-flex flex-column shadow-sm">
            <div class="area-card-heading card-header d-flex align-items-center gap-2 p-3">
                <div class="area-card-icon fs-4" aria-hidden="true">⚔️</div>
                <h2 class="fs-4 mb-0">Armi</h2>
            </div>
            <div class="card-body d-flex flex-column p-3 p-lg-4">
            <p>Armi semplici e da guerra, da mischia e a distanza.</p>
            <div class="category-buttons row row-cols-1 row-cols-sm-2 g-2 mt-3">
                <div class="col"><a class="taxonomy-filter btn btn-outline-secondary w-100" href="<?= e(appUrl('oggetti/armi')) ?>">Tutte <span><?= e((string) $objectCategoryCounts['oggetti/armi']['all']) ?></span></a></div>
                <div class="col"><a class="taxonomy-filter btn btn-outline-secondary w-100" href="<?= e(appUrl('oggetti/armi?categoria=items-weapons-simple-melee')) ?>">Da mischia semplici <span><?= e((string) $objectCategoryCounts['oggetti/armi']['items-weapons-simple-melee']) ?></span></a></div>
                <div class="col"><a class="taxonomy-filter btn btn-outline-secondary w-100" href="<?= e(appUrl('oggetti/armi?categoria=items-weapons-simple-ranged')) ?>">A distanza semplici <span><?= e((string) $objectCategoryCounts['oggetti/armi']['items-weapons-simple-ranged']) ?></span></a></div>
                <div class="col"><a class="taxonomy-filter btn btn-outline-secondary w-100" href="<?= e(appUrl('oggetti/armi?categoria=items-weapons-martial-melee')) ?>">Da mischia da guerra <span><?= e((string) $objectCategoryCounts['oggetti/armi']['items-weapons-martial-melee']) ?></span></a></div>
                <div class="col"><a class="taxonomy-filter btn btn-outline-secondary w-100" href="<?= e(appUrl('oggetti/armi?categoria=items-weapons-martial-ranged')) ?>">A distanza da guerra <span><?= e((string) $objectCategoryCounts['oggetti/armi']['items-weapons-martial-ranged']) ?></span></a></div>
            </div>
            </div>
        </article>
        </div>
        <div class="col">
        <article class="area-card card h-100 d-flex flex-column shadow-sm">
            <div class="area-card-heading card-header d-flex align-items-center gap-2 p-3">
                <div class="area-card-icon fs-4" aria-hidden="true">🛡️</div>
                <h2 class="fs-4 mb-0">Armature</h2>
            </div>
            <div class="card-body d-flex flex-column p-3 p-lg-4">
            <p>Armature leggere, medie, pesanti e scudi.</p>
            <div class="category-buttons row row-cols-1 row-cols-sm-2 g-2 mt-3">
                <div class="col"><a class="taxonomy-filter btn btn-outline-secondary w-100" href="<?= e(appUrl('oggetti/armature')) ?>">Tutte <span><?= e((string) $objectCategoryCounts['oggetti/armature']['all']) ?></span></a></div>
                <div class="col"><a class="taxonomy-filter btn btn-outline-secondary w-100" href="<?= e(appUrl('oggetti/armature?categoria=armor-light')) ?>">Leggere <span><?= e((string) $objectCategoryCounts['oggetti/armature']['armor-light']) ?></span></a></div>
                <div class="col"><a class="taxonomy-filter btn btn-outline-secondary w-100" href="<?= e(appUrl('oggetti/armature?categoria=armor-medium')) ?>">Medie <span><?= e((string) $objectCategoryCounts['oggetti/armature']['armor-medium']) ?></span></a></div>
                <div class="col"><a class="taxonomy-filter btn btn-outline-secondary w-100" href="<?= e(appUrl('oggetti/armature?categoria=armor-heavy')) ?>">Pesanti <span><?= e((string) $objectCategoryCounts['oggetti/armature']['armor-heavy']) ?></span></a></div>
                <div class="col"><a class="taxonomy-filter btn btn-outline-secondary w-100" href="<?= e(appUrl('oggetti/armature?categoria=armor-shields')) ?>">Scudi <span><?= e((string) $objectCategoryCounts['oggetti/armature']['armor-shields']) ?></span></a></div>
            </div>
            </div>
        </article>
        </div>
        <div class="col">
        <article class="area-card card h-100 d-flex flex-column shadow-sm">
            <div class="area-card-heading card-header d-flex align-items-center gap-2 p-3">
                <div class="area-card-icon fs-4" aria-hidden="true">🔧</div>
                <h2 class="fs-4 mb-0">Strumenti</h2>
            </div>
            <div class="card-body d-flex flex-column p-3 p-lg-4">
            <p>Strumenti da artigiano, musicali, giochi e altri strumenti.</p>
            <div class="category-buttons row row-cols-1 row-cols-sm-2 g-2 mt-3">
                <div class="col"><a class="taxonomy-filter btn btn-outline-secondary w-100" href="<?= e(appUrl('oggetti/strumenti')) ?>">Tutti <span><?= e((string) $objectCategoryCounts['oggetti/strumenti']['all']) ?></span></a></div>
                <div class="col"><a class="taxonomy-filter btn btn-outline-secondary w-100" href="<?= e(appUrl('oggetti/strumenti?categoria=artigiano')) ?>">Artigiano <span><?= e((string) $objectCategoryCounts['oggetti/strumenti']['artigiano']) ?></span></a></div>
                <div class="col"><a class="taxonomy-filter btn btn-outline-secondary w-100" href="<?= e(appUrl('oggetti/strumenti?categoria=giochi')) ?>">Giochi <span><?= e((string) $objectCategoryCounts['oggetti/strumenti']['giochi']) ?></span></a></div>
                <div class="col"><a class="taxonomy-filter btn btn-outline-secondary w-100" href="<?= e(appUrl('oggetti/strumenti?categoria=musicali')) ?>">Musicali <span><?= e((string) $objectCategoryCounts['oggetti/strumenti']['musicali']) ?></span></a></div>
                <div class="col"><a class="taxonomy-filter btn btn-outline-secondary w-100" href="<?= e(appUrl('oggetti/strumenti?categoria=altri')) ?>">Altri <span><?= e((string) $objectCategoryCounts['oggetti/strumenti']['altri']) ?></span></a></div>
            </div>
            </div>
        </article>
        </div>
        <div class="col">
        <article class="area-card card h-100 d-flex flex-column shadow-sm">
            <div class="area-card-heading card-header d-flex align-items-center gap-2 p-3">
                <div class="area-card-icon fs-4" aria-hidden="true">🎒</div>
                <h2 class="fs-4 mb-0">Equipaggiamento</h2>
            </div>
            <div class="card-body d-flex flex-column p-3 p-lg-4">
            <p>Contenitori, consumabili, illuminazione e oggetti utili durante i viaggi.</p>
            <div class="category-buttons row row-cols-1 row-cols-sm-2 g-2 mt-3">
                <div class="col"><a class="taxonomy-filter btn btn-outline-secondary w-100" href="<?= e(appUrl('oggetti/avventura')) ?>">Tutti <span><?= e((string) $objectCategoryCounts['oggetti/avventura']['all']) ?></span></a></div>
                <div class="col"><a class="taxonomy-filter btn btn-outline-secondary w-100" href="<?= e(appUrl('oggetti/avventura?categoria=avventura')) ?>">Avventura <span><?= e((string) $objectCategoryCounts['oggetti/avventura']['avventura']) ?></span></a></div>
                <div class="col"><a class="taxonomy-filter btn btn-outline-secondary w-100" href="<?= e(appUrl('oggetti/avventura?categoria=consumabili')) ?>">Consumabili <span><?= e((string) $objectCategoryCounts['oggetti/avventura']['consumabili']) ?></span></a></div>
                <div class="col"><a class="taxonomy-filter btn btn-outline-secondary w-100" href="<?= e(appUrl('oggetti/avventura?categoria=contenitori')) ?>">Contenitori <span><?= e((string) $objectCategoryCounts['oggetti/avventura']['contenitori']) ?></span></a></div>
                <div class="col"><a class="taxonomy-filter btn btn-outline-secondary w-100" href="<?= e(appUrl('oggetti/avventura?categoria=illuminazione')) ?>">Illuminazione <span><?= e((string) $objectCategoryCounts['oggetti/avventura']['illuminazione']) ?></span></a></div>
            </div>
            </div>
        </article>
        </div>
        <div class="col">
        <article class="area-card card h-100 d-flex flex-column shadow-sm">
            <div class="area-card-heading card-header d-flex align-items-center gap-2 p-3">
                <div class="area-card-icon fs-4" aria-hidden="true">🎯</div>
                <h2 class="fs-4 mb-0">Munizioni</h2>
            </div>
            <div class="card-body d-flex flex-column p-3 p-lg-4">
            <p>Munizioni acquistabili per archi, balestre, fionde e armi da fuoco.</p>
            <div class="category-buttons row row-cols-1 row-cols-sm-2 g-2 mt-3">
                <div class="col"><a class="taxonomy-filter btn btn-outline-secondary w-100" href="<?= e(appUrl('oggetti/munizioni')) ?>">Tutte le munizioni <span><?= e((string) $objectCategoryCounts['oggetti/munizioni']['all']) ?></span></a></div>
            </div>
            </div>
        </article>
        </div>
    </div>
</section>
