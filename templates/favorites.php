<section class="results-view favorites-view container-fluid px-0">
    <div class="section-heading d-flex justify-content-between align-items-end mb-3">
        <div>
            <p class="eyebrow">Archivio personale</p>
            <h1>Preferiti</h1>
        </div>
    </div>
    <p class="favorites-intro">Questi elementi sono salvati solo in questo browser. Nessun dato viene salvato sul server.</p>
    <div class="favorites-filters d-flex flex-wrap align-items-end gap-3 my-4" role="search" aria-label="Filtra i preferiti">
        <label class="favorites-search d-flex align-items-center gap-2 flex-grow-1">
            <span class="search-icon" aria-hidden="true">🔎</span>
            <input class="form-control" id="favorites-search" type="search" placeholder="Cerca nei preferiti..." autocomplete="off">
        </label>
        <label class="favorites-type-filter d-flex flex-column gap-1 text-body-secondary small">
            <span>Categoria</span>
            <select class="form-select" id="favorites-type-filter">
                <option value="">Tutte</option>
                <option value="spells">Incantesimi</option>
                <option value="feats">Talenti</option>
                <option value="monsters">Mostri</option>
                <option value="weapons">Oggetti · Armi</option>
                <option value="armor">Oggetti · Armature</option>
                <option value="ammunition">Oggetti · Munizioni</option>
                <option value="tools">Oggetti · Strumenti</option>
                <option value="adventuring-gear">Oggetti · Equipaggiamento</option>
            </select>
        </label>
        <button class="export-visible-button favorites-export-button btn btn-sm btn-outline-primary" id="export-favorites-button" type="button" disabled>⇩ Esporta preferiti (0)</button>
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
