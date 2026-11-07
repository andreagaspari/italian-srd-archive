<section class="results-view favorites-view container-fluid px-0">
    <div class="section-heading d-flex justify-content-between align-items-end mb-3">
        <div>
            <h1>Preferiti</h1>
        </div>
    </div>
    <p class="favorites-intro">Questi elementi sono salvati solo in questo browser. Nessun dato viene salvato sul server.</p>
    <form class="object-toolbar favorites-filters row g-2 align-items-end mb-3" role="search" aria-label="Filtra i preferiti">
        <label class="object-search input-group col-12 col-lg">
            <span class="search-icon input-group-text" aria-hidden="true">🔎</span>
            <input class="form-control" id="favorites-search" type="search" placeholder="Cerca nei preferiti..." autocomplete="off">
        </label>
        <label class="object-sort d-flex align-items-center gap-2 text-body-secondary small col-12 col-sm-auto">
            <span>Ordina</span>
            <select class="form-select" id="favorites-sort-select">
                <option value="default">Ordine preferiti</option>
                <option value="name-asc">Nome ↑</option>
                <option value="name-desc">Nome ↓</option>
                <option value="category-asc">Categoria ↑</option>
                <option value="category-desc">Categoria ↓</option>
                <option value="weight-asc">Peso ↑</option>
                <option value="weight-desc">Peso ↓</option>
                <option value="value-asc">Valore ↑</option>
                <option value="value-desc">Valore ↓</option>
            </select>
        </label>
        <button class="export-visible-button favorites-export-button btn btn-outline-primary col-12 col-sm-auto" id="export-favorites-button" type="button" disabled>⇩ Esporta preferiti (0)</button>
    </form>
    <div class="favorites-category-filters d-flex flex-wrap gap-2 mb-4" role="group" aria-label="Filtra i preferiti per categoria">
        <button class="taxonomy-filter btn btn-sm btn-outline-secondary is-selected" type="button" data-favorites-category="">Tutti</button>
        <button class="taxonomy-filter btn btn-sm btn-outline-secondary" type="button" data-favorites-category="spells">Incantesimi</button>
        <button class="taxonomy-filter btn btn-sm btn-outline-secondary" type="button" data-favorites-category="feats">Talenti</button>
        <button class="taxonomy-filter btn btn-sm btn-outline-secondary" type="button" data-favorites-category="monsters">Mostri</button>
        <button class="taxonomy-filter btn btn-sm btn-outline-secondary" type="button" data-favorites-category="weapons">Oggetti · Armi</button>
        <button class="taxonomy-filter btn btn-sm btn-outline-secondary" type="button" data-favorites-category="armor">Oggetti · Armature</button>
        <button class="taxonomy-filter btn btn-sm btn-outline-secondary" type="button" data-favorites-category="ammunition">Oggetti · Munizioni</button>
        <button class="taxonomy-filter btn btn-sm btn-outline-secondary" type="button" data-favorites-category="tools">Oggetti · Strumenti</button>
        <button class="taxonomy-filter btn btn-sm btn-outline-secondary" type="button" data-favorites-category="adventuring-gear">Oggetti · Equipaggiamento</button>
        <button class="taxonomy-filter btn btn-sm btn-outline-secondary" type="button" data-favorites-category="rules">Glossario</button>
    </div>
    <div id="favorites-groups"></div>
    <p id="favorites-filter-empty" class="favorites-filter-empty" hidden>Nessun preferito corrisponde ai filtri selezionati.</p>
    <div id="favorites-empty" class="empty-state text-center p-5" hidden>
        <span class="empty-icon">♡</span>
        <h3>Nessun preferito</h3>
        <p>Aggiungi un elemento dall’elenco per ritrovarlo qui.</p>
    </div>
    <div id="favorites-missing" class="favorites-missing mt-3 p-3 text-body-secondary border rounded" hidden>
        Alcuni preferiti non sono più presenti nei dati correnti.
        <button class="btn btn-outline-secondary" type="button" id="remove-missing-favorites">Rimuovili</button>
    </div>
</section>
