<section class="results-view favorites-view">
    <div class="section-heading">
        <div>
            <p class="eyebrow">Archivio personale</p>
            <h1>Preferiti</h1>
        </div>
    </div>
    <p class="favorites-intro">Questi elementi sono salvati solo in questo browser. Nessun dato viene salvato sul server.</p>
    <div class="favorites-filters" role="search" aria-label="Filtra i preferiti">
        <label class="favorites-search">
            <span class="search-icon" aria-hidden="true">🔎</span>
            <input id="favorites-search" type="search" placeholder="Cerca nei preferiti..." autocomplete="off">
        </label>
        <label class="favorites-type-filter">
            <span>Categoria</span>
            <select id="favorites-type-filter">
                <option value="">Tutte</option>
                <option value="spells">Incantesimi</option>
                <option value="monsters">Mostri</option>
                <option value="weapons">Oggetti · Armi</option>
                <option value="armor">Oggetti · Armature</option>
                <option value="ammunition">Oggetti · Munizioni</option>
                <option value="tools">Oggetti · Strumenti</option>
                <option value="adventuring-gear">Oggetti · Equipaggiamento</option>
            </select>
        </label>
    </div>
    <div id="favorites-groups"></div>
    <p id="favorites-filter-empty" class="favorites-filter-empty" hidden>Nessun preferito corrisponde ai filtri selezionati.</p>
    <div id="favorites-empty" class="empty-state" hidden>
        <span class="empty-icon">♡</span>
        <h3>Nessun preferito</h3>
        <p>Aggiungi un elemento dall’elenco per ritrovarlo qui.</p>
    </div>
    <div id="favorites-missing" class="favorites-missing" hidden>
        Alcuni preferiti non sono più presenti nei dati correnti.
        <button class="button-secondary" type="button" id="remove-missing-favorites">Rimuovili</button>
    </div>
</section>
