<section class="home-view">
    <div class="hero">
        <div>
            <h1>Oggetti</h1>
            <p class="hero-copy">Scegli una categoria per consultare gli elementi dell’equipaggiamento.</p>
        </div>
        <span class="hero-decoration" aria-hidden="true">🎒</span>
    </div>

    <div class="area-grid object-area-grid">
        <article class="area-card area-card-items">
            <div class="area-card-heading">
                <div class="area-card-icon" aria-hidden="true">⚔️</div>
                <h2>Armi</h2>
            </div>
            <p>Armi semplici e da guerra, da mischia e a distanza.</p>
            <div class="category-buttons">
                <a class="taxonomy-filter" href="<?= e(appUrl('oggetti/armi')) ?>">Tutte</a>
                <a class="taxonomy-filter" href="<?= e(appUrl('oggetti/armi?categoria=items-weapons-simple-melee')) ?>">Da mischia semplici</a>
                <a class="taxonomy-filter" href="<?= e(appUrl('oggetti/armi?categoria=items-weapons-simple-ranged')) ?>">A distanza semplici</a>
                <a class="taxonomy-filter" href="<?= e(appUrl('oggetti/armi?categoria=items-weapons-martial-melee')) ?>">Da mischia da guerra</a>
                <a class="taxonomy-filter" href="<?= e(appUrl('oggetti/armi?categoria=items-weapons-martial-ranged')) ?>">A distanza da guerra</a>
            </div>
        </article>
        <article class="area-card">
            <div class="area-card-heading">
                <div class="area-card-icon" aria-hidden="true">🛡️</div>
                <h2>Armature</h2>
            </div>
            <p>Armature leggere, medie, pesanti e scudi.</p>
            <div class="category-buttons">
                <a class="taxonomy-filter" href="<?= e(appUrl('oggetti/armature')) ?>">Tutte</a>
                <a class="taxonomy-filter" href="<?= e(appUrl('oggetti/armature?categoria=armor-light')) ?>">Leggere</a>
                <a class="taxonomy-filter" href="<?= e(appUrl('oggetti/armature?categoria=armor-medium')) ?>">Medie</a>
                <a class="taxonomy-filter" href="<?= e(appUrl('oggetti/armature?categoria=armor-heavy')) ?>">Pesanti</a>
                <a class="taxonomy-filter" href="<?= e(appUrl('oggetti/armature?categoria=armor-shields')) ?>">Scudi</a>
            </div>
        </article>
        <article class="area-card">
            <div class="area-card-heading">
                <div class="area-card-icon" aria-hidden="true">🔧</div>
                <h2>Strumenti</h2>
            </div>
            <p>Strumenti da artigiano, musicali, giochi e altri strumenti.</p>
            <div class="category-buttons">
                <a class="taxonomy-filter" href="<?= e(appUrl('oggetti/strumenti')) ?>">Tutti</a>
                <a class="taxonomy-filter" href="<?= e(appUrl('oggetti/strumenti?categoria=artigiano')) ?>">Artigiano</a>
                <a class="taxonomy-filter" href="<?= e(appUrl('oggetti/strumenti?categoria=giochi')) ?>">Giochi</a>
                <a class="taxonomy-filter" href="<?= e(appUrl('oggetti/strumenti?categoria=musicali')) ?>">Musicali</a>
                <a class="taxonomy-filter" href="<?= e(appUrl('oggetti/strumenti?categoria=altri')) ?>">Altri</a>
            </div>
        </article>
        <article class="area-card">
            <div class="area-card-heading">
                <div class="area-card-icon" aria-hidden="true">🎒</div>
                <h2>Equipaggiamento</h2>
            </div>
            <p>Contenitori, consumabili, illuminazione e oggetti utili durante i viaggi.</p>
            <div class="category-buttons">
                <a class="taxonomy-filter" href="<?= e(appUrl('oggetti/avventura')) ?>">Tutti</a>
                <a class="taxonomy-filter" href="<?= e(appUrl('oggetti/avventura?categoria=avventura')) ?>">Avventura</a>
                <a class="taxonomy-filter" href="<?= e(appUrl('oggetti/avventura?categoria=consumabili')) ?>">Consumabili</a>
                <a class="taxonomy-filter" href="<?= e(appUrl('oggetti/avventura?categoria=contenitori')) ?>">Contenitori</a>
                <a class="taxonomy-filter" href="<?= e(appUrl('oggetti/avventura?categoria=illuminazione')) ?>">Illuminazione</a>
            </div>
        </article>
        <article class="area-card">
            <div class="area-card-heading">
                <div class="area-card-icon" aria-hidden="true">🎯</div>
                <h2>Munizioni</h2>
            </div>
            <p>Munizioni acquistabili per archi, balestre, fionde e armi da fuoco.</p>
            <div class="category-buttons">
                <a class="taxonomy-filter" href="<?= e(appUrl('oggetti/munizioni')) ?>">Tutte le munizioni</a>
            </div>
        </article>
    </div>
</section>
