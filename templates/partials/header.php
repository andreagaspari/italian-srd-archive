<header class="topbar">
    <a class="brand" href="<?= e(appUrl()) ?>" aria-label="Torna alla home">
        <span class="brand-mark">S</span>
        <span>
            <strong>SRD Italia</strong>
            <small>Archivio locale</small>
        </span>
    </a>

    <div class="topbar-actions">
        <div class="top-search-wrap">
        <label class="top-search">
            <span class="search-icon" aria-hidden="true">🔎</span>
            <input id="search-input" type="search" placeholder="Cerca nell’archivio..." autocomplete="off" aria-controls="global-search-preview" aria-expanded="false">
        </label>
        <div id="global-search-preview" class="global-search-preview" hidden></div>
        </div>
        <span class="version-badge">SRD 5.2.1</span>
        <button class="icon-button menu-button" type="button" aria-label="Apri il menu" aria-expanded="false">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>
