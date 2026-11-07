<header class="topbar navbar bg-body-tertiary d-flex align-items-center justify-content-between">
    <a class="brand d-flex align-items-center gap-2 text-reset text-decoration-none" href="<?= e(appUrl()) ?>" aria-label="Torna alla home" title="Torna alla home">
        <img src="assets/images/italian_srd_archive.png" class="brand-mark" alt="Italian SRD Archive">
        <span class="brand-name fw-bold">Italian SRD Archive</span>
    </a>

    <div class="topbar-actions d-flex align-items-center gap-3">
        <div class="top-search-wrap">
        <label class="top-search">
            <span class="search-icon" aria-hidden="true">🔎</span>
            <input id="search-input" type="search" placeholder="Cerca nell’archivio..." autocomplete="off" aria-controls="global-search-preview" aria-expanded="false">
        </label>
        <button class="mobile-search-button" type="button" aria-label="Apri la ricerca globale" aria-expanded="false">🔎</button>
        <div id="global-search-preview" class="global-search-preview" role="listbox" hidden></div>
        </div>
        <a class="favorites-link" href="<?= e(appUrl('preferiti')) ?>" aria-label="Apri i preferiti">
            <span class="favorites-icon" aria-hidden="true">🤍</span>
            <strong id="favorites-count" aria-label="Numero di preferiti">0</strong>
        </a>
        <button class="theme-toggle" type="button" aria-label="Attiva il tema scuro" aria-pressed="false">
            <span class="theme-toggle-icon" aria-hidden="true">🌙</span>
        </button>
        <button class="icon-button menu-button" type="button" aria-label="Apri il menu" aria-expanded="false">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>
