const searchInput = document.querySelector('#object-search-input');
const resultCards = [...document.querySelectorAll('[data-search]')];
const resultCount = document.querySelector('#result-count');
const emptyState = document.querySelector('#empty-state');
const sortSelect = document.querySelector('#object-sort-select');
const resultsGrid = document.querySelector('#results-grid');
const globalSearchInput = document.querySelector('#search-input');
const globalSearchPreview = document.querySelector('#global-search-preview');
const globalSearchDataElement = document.querySelector('#global-search-data');
const globalSearchData = globalSearchDataElement ? JSON.parse(globalSearchDataElement.textContent) : [];
const spellFilterSelects = [...document.querySelectorAll('.spell-filter-select')];
const monsterFilterSelects = [...document.querySelectorAll('.monster-filter-select')];
const resultContainers = resultsGrid?.classList.contains('monster-species-groups')
    ? [...resultsGrid.querySelectorAll('.monster-results-grid')]
    : (resultsGrid ? [resultsGrid] : []);
const originalCardOrders = resultContainers.map((container) => [...container.querySelectorAll('.result-card')]);

function filterResults() {
    const query = searchInput.value.trim().toLocaleLowerCase('it');
    let visibleCount = 0;

    resultCards.forEach((card) => {
        const matches = card.dataset.search.toLocaleLowerCase('it').includes(query);
        card.hidden = !matches;
        if (matches) {
            visibleCount += 1;
        }

    });

    if (resultCount) {
        resultCount.textContent = visibleCount;
    }

    if (emptyState) {
        emptyState.hidden = visibleCount > 0;
    }
}

function sortableNumber(value) {
    const normalized = value.toLocaleLowerCase('it').trim();
    const numberText = normalized.replace(/\./g, '').replace(',', '.').match(/-?\d+(?:\.\d+)?/);
    const number = numberText ? Number.parseFloat(numberText[0]) : Number.POSITIVE_INFINITY;
    if (normalized.includes('mo')) return number * 100;
    if (normalized.includes('ma')) return number;
    if (normalized.includes('mr')) return number / 100;
    return number;
}

function sortResults() {
    if (!sortSelect || !resultsGrid) return;
    if (sortSelect.value === 'default') {
        resultContainers.forEach((container, index) => {
            originalCardOrders[index].forEach((card) => container.appendChild(card));
        });
        return;
    }
    const [field, direction] = sortSelect.value.split('-');
    const dataKey = `sort${field[0].toUpperCase()}${field.slice(1)}`;
    resultContainers.forEach((container) => {
        const cards = [...container.querySelectorAll('.result-card')];
        cards.sort((a, b) => {
            const aRaw = a.dataset[dataKey] || '';
            const bRaw = b.dataset[dataKey] || '';
            const aValue = field === 'name' || field === 'category'
                ? aRaw.toLocaleLowerCase('it')
                : sortableNumber(aRaw);
            const bValue = field === 'name' || field === 'category'
                ? bRaw.toLocaleLowerCase('it')
                : sortableNumber(bRaw);
            const comparison = typeof aValue === 'string' ? aValue.localeCompare(bValue, 'it') : aValue - bValue;
            return direction === 'desc' ? -comparison : comparison;
        });
        cards.forEach((card) => container.appendChild(card));
    });
}

if (searchInput) {
    searchInput.addEventListener('input', filterResults);
}
if (sortSelect) {
    sortSelect.addEventListener('change', sortResults);
    sortResults();
}
spellFilterSelects.forEach((select) => {
    select.addEventListener('change', () => select.form?.submit());
});
monsterFilterSelects.forEach((select) => {
    select.addEventListener('change', () => select.form?.submit());
});

function closeExportDialog() {
    document.querySelector('.export-dialog-backdrop')?.remove();
}

function openExportDialog(items) {
    const itemCount = items.length;
    const firstTitle = items[0].title;
    const isSpellExport = items[0].type === 'armi';
    const itemLabel = isSpellExport ? 'incantesimo' : 'oggetto';
    const itemLabelPlural = isSpellExport ? 'incantesimi' : 'oggetti';
    const archiveName = isSpellExport ? 'incantesimi.zip' : 'oggetti.zip';
    const backdrop = document.createElement('div');
    backdrop.className = 'export-dialog-backdrop';
    backdrop.innerHTML = `
        <form class="export-dialog" method="dialog">
            <button class="export-dialog-close" type="button" aria-label="Chiudi">×</button>
            <p class="eyebrow">Esportazione ${itemLabel}</p>
            <h2>Esporta ${itemCount === 1 ? escapeHtml(firstTitle) : `${itemCount} ${itemLabelPlural}`}</h2>
            <p class="export-dialog-copy">I campi seguenti sono facoltativi. Il file verrà creato come <strong>${itemCount === 1 ? `${escapeHtml(firstTitle)}.zip` : archiveName}</strong>.</p>
            <label class="export-field">
                <span>ID cartella</span>
                <input name="folderId" type="text" maxlength="80" placeholder="Lascia vuoto per omettere">
            </label>
            <label class="export-field export-color-field">
                <span class="export-checkbox-label"><input name="useCardColor" type="checkbox"> Imposta colore della card</span>
                <span class="export-color-control" hidden>
                    <input name="cardColor" type="color" value="#ef4444" disabled>
                    <input name="cardColorText" type="text" value="#ef4444" pattern="#[0-9a-fA-F]{6}" maxlength="7" disabled>
                </span>
            </label>
            <div class="export-dialog-actions">
                <button class="button-secondary" type="button" data-export-cancel>Annulla</button>
                <button class="button-primary" type="submit">Scarica ZIP</button>
            </div>
            <p class="export-error" role="alert" hidden></p>
        </form>
    `;
    document.body.appendChild(backdrop);
    const form = backdrop.querySelector('form');
    const colorPicker = form.querySelector('input[type="color"]');
    const colorText = form.querySelector('input[name="cardColorText"]');
    const useCardColor = form.querySelector('input[name="useCardColor"]');
    useCardColor.addEventListener('change', () => {
        colorPicker.disabled = !useCardColor.checked;
        colorText.disabled = !useCardColor.checked;
        form.querySelector('.export-color-control').hidden = !useCardColor.checked;
    });
    colorPicker.addEventListener('input', () => { colorText.value = colorPicker.value; });
    colorText.addEventListener('input', () => {
        if (/^#[0-9a-fA-F]{6}$/.test(colorText.value)) colorPicker.value = colorText.value;
    });
    backdrop.querySelector('[data-export-cancel]').addEventListener('click', closeExportDialog);
    backdrop.querySelector('.export-dialog-close').addEventListener('click', closeExportDialog);
    backdrop.addEventListener('click', (event) => {
        if (event.target === backdrop) closeExportDialog();
    });
    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const error = form.querySelector('.export-error');
        const folderId = form.elements.folderId.value.trim();
        const cardColor = useCardColor.checked ? colorText.value.trim() : '';
        if (folderId && !/^[A-Za-z0-9_-]+$/.test(folderId)) {
            error.textContent = 'L’ID cartella può contenere solo lettere, numeri, trattini e underscore.';
            error.hidden = false;
            return;
        }
        if (cardColor && !/^#[0-9a-fA-F]{6}$/.test(cardColor)) {
            error.textContent = 'Inserisci un colore esadecimale valido.';
            error.hidden = false;
            return;
        }
        const body = new FormData();
        body.append('items', JSON.stringify(items));
        body.append('folderId', folderId);
        body.append('cardColor', cardColor);
        const submit = form.querySelector('[type="submit"]');
        submit.disabled = true;
        submit.textContent = 'Preparazione...';
        try {
            const response = await fetch('export.php', { method: 'POST', body });
            if (!response.ok) throw new Error(await response.text());
            const blob = await response.blob();
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = `${itemCount === 1 ? firstTitle : (isSpellExport ? 'incantesimi' : 'oggetti')}.zip`;
            link.click();
            URL.revokeObjectURL(url);
            closeExportDialog();
        } catch (exportError) {
            error.textContent = exportError.message || 'Impossibile creare il file ZIP.';
            error.hidden = false;
            submit.disabled = false;
            submit.textContent = 'Scarica ZIP';
        }
    });
}

document.addEventListener('click', (event) => {
    const button = event.target.closest('.export-button');
    if (button) {
        const item = JSON.parse(button.closest('[data-export]').dataset.export);
        openExportDialog([item]);
        return;
    }
    if (event.target.closest('#export-visible-button')) {
        const exportButton = event.target.closest('#export-visible-button');
        const request = Object.fromEntries(new URLSearchParams(window.location.search));
        const basePath = new URL(document.baseURI).pathname.replace(/\/+$/, '');
        const route = window.location.pathname
            .slice(basePath.length)
            .replace(/^\/+|\/+$/g, '');
        delete request.route;
        delete request.page;
        delete request.pageSize;
        exportButton.disabled = true;
        fetch('export.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                exportAll: '1',
                route,
                request: JSON.stringify(request)
            })
        })
            .then((response) => response.ok
                ? response.json()
                : response.json().then((data) => Promise.reject(new Error(data.error || 'Impossibile preparare l’esportazione.'))))
            .then((data) => {
                if (data.items?.length > 0) {
                    openExportDialog(data.items);
                }
            })
            .catch((error) => {
                window.alert(error.message);
            })
            .finally(() => {
                exportButton.disabled = false;
            });
    }
});

function renderGlobalSearch() {
    if (!globalSearchInput || !globalSearchPreview) {
        return;
    }

    const query = globalSearchInput.value.trim().toLocaleLowerCase('it');
    if (query.length < 2) {
        globalSearchPreview.hidden = true;
        globalSearchInput.setAttribute('aria-expanded', 'false');
        globalSearchPreview.innerHTML = '';
        return;
    }

    const groups = new Map();
    globalSearchData
        .filter((item) => `${item.search || ''} ${item.title} ${item.description} ${item.section}`.toLocaleLowerCase('it').includes(query))
        .slice(0, 30)
        .forEach((item) => {
            if (!groups.has(item.section)) {
                groups.set(item.section, []);
            }
            groups.get(item.section).push(item);
        });

    if (!groups.size) {
        globalSearchPreview.innerHTML = '<div class="global-search-empty">Nessun risultato trovato</div>';
    } else {
        globalSearchPreview.innerHTML = [...groups].map(([section, items]) => `
            <section class="global-search-group">
                <h3>${escapeHtml(section)}</h3>
                ${items.slice(0, 6).map((item) => `
                    <a class="global-search-result" href="${escapeAttribute(item.url)}">
                            <span class="global-search-result-main">
                                <strong>${escapeHtml(item.title)}</strong>
                                <span class="global-search-description">${escapeHtml(item.description)}</span>
                            </span>
                            <span class="global-search-tags">${(item.tags || []).map((tag) => `<span class="global-search-tag">${escapeHtml(tag)}</span>`).join('')}</span>
            </a>
                `).join('')}
            </section>
        `).join('');
    }
    globalSearchPreview.hidden = false;
    globalSearchInput.setAttribute('aria-expanded', 'true');
}

function escapeHtml(value) {
    return String(value).replace(/[&<>"']/g, (character) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
    }[character]));
}

function escapeAttribute(value) {
    return escapeHtml(value).replace(/`/g, '&#096;');
}

if (globalSearchInput) {
    globalSearchInput.addEventListener('input', renderGlobalSearch);
    globalSearchInput.addEventListener('focus', renderGlobalSearch);
}

document.addEventListener('click', (event) => {
    if (globalSearchPreview && !event.target.closest('.top-search-wrap')) {
        globalSearchPreview.hidden = true;
        globalSearchInput?.setAttribute('aria-expanded', 'false');
    }
});

document.addEventListener('keydown', (event) => {
    if (event.key === '/' && document.activeElement !== searchInput && document.activeElement !== globalSearchInput) {
        event.preventDefault();
        (searchInput || globalSearchInput)?.focus();
    }
});

const menuButton = document.querySelector('.menu-button');
const sidebar = document.querySelector('.sidebar');

if (menuButton && sidebar) {
    menuButton.addEventListener('click', () => {
        const isOpen = sidebar.classList.toggle('is-open');
        menuButton.setAttribute('aria-expanded', String(isOpen));
    });
}

const sourceDialog = document.querySelector('#source-dialog');
const sourceDialogButton = document.querySelector('[data-source-dialog]');

if (sourceDialog && sourceDialogButton) {
    sourceDialogButton.addEventListener('click', () => sourceDialog.showModal());
    sourceDialog.addEventListener('click', (event) => {
        if (event.target === sourceDialog) sourceDialog.close();
    });
}
