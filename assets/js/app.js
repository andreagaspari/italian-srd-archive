const searchInput = document.querySelector('#object-search-input');
let resultCards = [...document.querySelectorAll('[data-search]')];
let resultCount = document.querySelector('#result-count');
let emptyState = document.querySelector('#empty-state');
const sortSelect = document.querySelector('#object-sort-select');
let resultsGrid = document.querySelector('#results-grid');
const globalSearchInput = document.querySelector('#search-input');
const globalSearchPreview = document.querySelector('#global-search-preview');
let globalSearchData = [];
const spellFilterSelects = [...document.querySelectorAll('.spell-filter-select')];
const monsterFilterSelects = [...document.querySelectorAll('.monster-filter-select')];
let resultContainers = resultsGrid?.classList.contains('monster-species-groups')
    ? [...resultsGrid.querySelectorAll('.monster-results-grid')]
    : (resultsGrid ? [resultsGrid] : []);
let originalCardOrders = resultContainers.map((container) => [...container.querySelectorAll('.result-card')]);

/**
 * Stores browser-local favorite references and keeps their format versioned for future migrations.
 */
class LocalStorageFavoriteStore {
    /**
     * @param {string} storageKey Key used in localStorage.
     */
    constructor(storageKey = 'isa:favorites:v1') {
        this.storageKey = storageKey;
    }

    /**
     * Returns all saved favorite references.
     *
     * @returns {Array<{key: string, dataset: string, id: string, addedAt: string}>} Favorite references.
     */
    getAll() {
        const raw = window.localStorage.getItem(this.storageKey);
        if (!raw) return [];
        const parsed = JSON.parse(raw);
        return Array.isArray(parsed?.favorites) ? parsed.favorites : [];
    }

    /**
     * Checks whether an item is saved.
     *
     * @param {string} dataset Dataset name.
     * @param {string} id Stable item identifier.
     * @returns {boolean} Whether the item is saved.
     */
    has(dataset, id) {
        return this.getAll().some((favorite) => favorite.key === `${dataset}:${id}`);
    }

    /**
     * Adds an item unless it is already saved.
     *
     * @param {string} dataset Dataset name.
     * @param {string} id Stable item identifier.
     * @returns {void}
     */
    add(dataset, id) {
        const favorites = this.getAll();
        const key = `${dataset}:${id}`;
        if (favorites.some((favorite) => favorite.key === key)) return;
        favorites.push({ key, dataset, id, addedAt: new Date().toISOString() });
        this.save(favorites);
    }

    /**
     * Removes an item.
     *
     * @param {string} dataset Dataset name.
     * @param {string} id Stable item identifier.
     * @returns {void}
     */
    remove(dataset, id) {
        const key = `${dataset}:${id}`;
        this.save(this.getAll().filter((favorite) => favorite.key !== key));
    }

    /**
     * Replaces all saved references.
     *
     * @param {Array<{key: string, dataset: string, id: string, addedAt: string}>} favorites Favorite references.
     * @returns {void}
     */
    save(favorites) {
        window.localStorage.setItem(this.storageKey, JSON.stringify({ version: 1, favorites }));
        window.dispatchEvent(new CustomEvent('favoriteschange'));
    }
}

const favoriteStore = new LocalStorageFavoriteStore();

/**
 * Updates the favorite counter in the global header.
 *
 * @returns {void}
 */
function updateFavoriteCount() {
    const counter = document.querySelector('#favorites-count');
    const icon = document.querySelector('.favorites-icon');
    const count = favoriteStore.getAll().length;
    if (counter) counter.textContent = String(count);
    if (icon) {
        icon.textContent = count > 0 ? '❤️' : '🤍';
        icon.classList.toggle('is-active', count > 0);
    }
}

/**
 * Filters the original cards currently rendered in the favorites groups.
 *
 * @returns {void}
 */
function filterFavoriteCards() {
    const query = document.querySelector('#favorites-search')?.value.trim().toLocaleLowerCase('it') ?? '';
    const dataset = document.querySelector('#favorites-type-filter')?.value ?? '';
    let visibleCards = 0;

    document.querySelectorAll('.favorite-group').forEach((group) => {
        const matchesDataset = !dataset || group.dataset.favoriteGroup === dataset;
        let visibleInGroup = 0;
        group.querySelectorAll('.result-card').forEach((card) => {
            const matchesQuery = !query || card.textContent.toLocaleLowerCase('it').includes(query);
            const visible = matchesDataset && matchesQuery;
            card.hidden = !visible;
            if (visible) visibleInGroup += 1;
        });
        group.hidden = visibleInGroup === 0;
        visibleCards += visibleInGroup;
    });

    const emptyState = document.querySelector('#favorites-filter-empty');
    if (emptyState) emptyState.hidden = visibleCards > 0 || (!query && !dataset);
    const exportButton = document.querySelector('#export-favorites-button');
    if (exportButton) {
        exportButton.disabled = visibleCards === 0;
        exportButton.textContent = `⇩ Esporta preferiti (${visibleCards})`;
    }
}

/**
 * Adds or updates the favorite action on a catalog card.
 *
 * @param {HTMLElement} card Catalog result card.
 * @returns {void}
 */
function enhanceFavoriteCard(card) {
    const dataset = card.dataset.favoriteDataset;
    const id = card.dataset.favoriteId;
    if (!dataset || !id || card.querySelector('.favorite-button')) return;

    const button = document.createElement('button');
    button.className = 'favorite-button';
    button.type = 'button';
    button.addEventListener('click', () => {
        if (favoriteStore.has(dataset, id)) {
            favoriteStore.remove(dataset, id);
        } else {
            favoriteStore.add(dataset, id);
        }
        updateFavoriteButton(button, dataset, id);
    });
    card.insertBefore(button, card.firstChild);
    updateFavoriteButton(button, dataset, id);
}

/**
 * Reflects the saved state in a favorite button.
 *
 * @param {HTMLButtonElement} button Favorite action.
 * @param {string} dataset Dataset name.
 * @param {string} id Stable item identifier.
 * @returns {void}
 */
function updateFavoriteButton(button, dataset, id) {
    const isFavorite = favoriteStore.has(dataset, id);
    button.textContent = isFavorite ? '❤️' : '🤍';
    button.classList.toggle('is-favorite', isFavorite);
    button.setAttribute('aria-pressed', String(isFavorite));
    button.setAttribute('aria-label', isFavorite ? 'Rimuovi dai preferiti' : 'Aggiungi ai preferiti');
    button.title = isFavorite ? 'Rimuovi dai preferiti' : 'Aggiungi ai preferiti';
}

/**
 * Loads and renders the client-side favorites page using the original catalog cards.
 *
 * @returns {Promise<void>} Resolves after rendering the page.
 */
async function renderFavoritesPage() {
    const groupsContainer = document.querySelector('#favorites-groups');
    if (!groupsContainer) return;

    const groups = [
        { dataset: 'spells', label: 'Incantesimi', route: 'incantesimi' },
        { dataset: 'monsters', label: 'Mostri', route: 'mostri' },
        { dataset: 'weapons', label: 'Oggetti · Armi', route: 'oggetti/armi' },
        { dataset: 'armor', label: 'Oggetti · Armature', route: 'oggetti/armature' },
        { dataset: 'ammunition', label: 'Oggetti · Munizioni', route: 'oggetti/munizioni' },
        { dataset: 'tools', label: 'Oggetti · Strumenti', route: 'oggetti/strumenti' },
        { dataset: 'adventuring-gear', label: 'Oggetti · Equipaggiamento', route: 'oggetti/avventura' },
        { dataset: 'rules', label: 'Glossario delle regole', route: 'regole' }
    ];
    const favorites = favoriteStore.getAll();
    const foundKeys = new Set();
    const renderedGroups = [];

    for (const group of groups) {
        const groupFavorites = favorites.filter((favorite) => favorite.dataset === group.dataset);
        if (groupFavorites.length === 0) continue;

        const pendingKeys = new Set(groupFavorites.map((favorite) => favorite.key));
        const cards = new Map();
        for (let page = 1; page <= 10 && pendingKeys.size > 0; page += 1) {
            const response = await fetch(`${group.route}?pageSize=100&page=${page}`);
            if (!response.ok) throw new Error(`Impossibile caricare la sezione ${group.label}.`);
            const document = new DOMParser().parseFromString(await response.text(), 'text/html');
            const pageCards = [...document.querySelectorAll('[data-favorite-dataset][data-favorite-id]')];
            if (pageCards.length === 0) break;
            pageCards.forEach((card) => {
                const key = `${card.dataset.favoriteDataset}:${card.dataset.favoriteId}`;
                if (pendingKeys.has(key)) {
                    cards.set(key, card);
                    pendingKeys.delete(key);
                    foundKeys.add(key);
                }
            });
        }

        if (cards.size > 0) {
            renderedGroups.push({
                ...group,
                cards: groupFavorites.filter((favorite) => cards.has(favorite.key)).map((favorite) => cards.get(favorite.key))
            });
        }
    }

    groupsContainer.innerHTML = renderedGroups.map((group) => `
        <section class="favorite-group" data-favorite-group="${group.dataset}" aria-labelledby="favorites-${group.dataset}">
            <div class="section-heading">
                <div><p class="eyebrow">Categoria</p><h2 id="favorites-${group.dataset}">${group.label}</h2></div>
            </div>
            <div class="results-grid"></div>
        </section>
    `).join('');
    renderedGroups.forEach((group, index) => {
        const grid = groupsContainer.querySelectorAll('.favorite-group .results-grid')[index];
        group.cards.forEach((card) => {
            const clone = card.cloneNode(true);
            grid.appendChild(clone);
            enhanceFavoriteCard(clone);
        });
    });
    filterFavoriteCards();

    const missing = favorites.filter((favorite) => !foundKeys.has(favorite.key));
    document.querySelector('#favorites-empty').hidden = favorites.length > 0;
    document.querySelector('#favorites-missing').hidden = missing.length === 0;
    document.querySelector('#remove-missing-favorites').onclick = () => {
        favoriteStore.save(favorites.filter((favorite) => foundKeys.has(favorite.key)));
        renderFavoritesPage();
    };
}

document.querySelectorAll('[data-favorite-dataset]').forEach(enhanceFavoriteCard);
updateFavoriteCount();
window.addEventListener('favoriteschange', updateFavoriteCount);
document.querySelector('#favorites-search')?.addEventListener('input', filterFavoriteCards);
document.querySelector('#favorites-type-filter')?.addEventListener('change', filterFavoriteCards);
renderFavoritesPage().catch((error) => {
    const missingMessage = document.querySelector('#favorites-missing');
    if (missingMessage) {
        missingMessage.hidden = false;
        missingMessage.textContent = 'Non è stato possibile caricare i preferiti. Riprova più tardi.';
        console.error(error);
    }
});

/**
 * Persists the section search term in the browser URL without reloading the page.
 *
 * @param {string} value Current section search term.
 * @returns {void}
 */
function persistSectionSearch(value) {
    const url = new URL(window.location.href);
    const normalizedValue = value.trim();
    if (normalizedValue) {
        url.searchParams.set('q', normalizedValue);
    } else {
        url.searchParams.delete('q');
    }
    url.searchParams.delete('page');
    window.history.replaceState({}, '', url);
}

/**
 * Keeps the current section search term when navigating through filters.
 *
 * @returns {void}
 */
function preserveSectionSearchOnNavigation() {
    const query = new URLSearchParams(window.location.search).get('q');
    const resultsView = document.querySelector('.results-view');
    if (!query || !resultsView) return;

    resultsView.querySelectorAll('a[href]').forEach((link) => {
        const url = new URL(link.href, window.location.href);
        if (url.origin === window.location.origin) {
            url.searchParams.set('q', query);
            link.href = url.toString();
        }
    });
    resultsView.querySelectorAll('form[method="get"]').forEach((form) => {
        if (!form.querySelector('input[name="q"]')) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'q';
            input.value = query;
            form.appendChild(input);
        }
    });
}

function filterResults() {
    if (!searchInput) return;
    const query = searchInput.value.trim().toLocaleLowerCase('it');
    let visibleCount = 0;

    resultCards.forEach((card) => {
        const matches = card.dataset.search.toLocaleLowerCase('it').includes(query);
        card.hidden = !matches;
        if (matches) {
            visibleCount += 1;
        }
    });

    document.querySelectorAll('[id^="result-count-"], #result-count').forEach((counter) => {
        counter.textContent = String(visibleCount);
    });

    if (emptyState) {
        emptyState.hidden = visibleCount > 0;
    }
}

/**
 * Refreshes references to section result elements after an asynchronous render.
 *
 * @returns {void}
 */
function refreshSectionResultReferences() {
    resultCards = [...document.querySelectorAll('[data-search]')];
    resultCount = document.querySelector('#result-count');
    emptyState = document.querySelector('#empty-state');
    resultsGrid = document.querySelector('#results-grid');
    resultContainers = resultsGrid?.classList.contains('monster-species-groups')
        ? [...resultsGrid.querySelectorAll('.monster-results-grid')]
        : (resultsGrid ? [resultsGrid] : []);
    originalCardOrders = resultContainers.map((container) => [...container.querySelectorAll('.result-card')]);
    resultCards.forEach(enhanceFavoriteCard);
}

/**
 * Moves the result selected from global search to the top of its result grid.
 *
 * @returns {void}
 */
function prioritizeFocusedResult() {
    const focusId = new URLSearchParams(window.location.search).get('focus');
    if (!focusId) return;

    document.querySelectorAll('.result-card.is-focused-result').forEach((card) => {
        card.classList.remove('is-focused-result');
    });
    const focusedCard = [...document.querySelectorAll('.result-card')]
        .find((card) => card.dataset.favoriteId === focusId);
    if (!focusedCard) return;

    const rulesList = focusedCard.closest('.rules-list');
    if (rulesList) {
        const columns = [...rulesList.querySelectorAll(':scope > .rules-column')];
        const cards = [...rulesList.querySelectorAll('.rule-card')]
            .sort((left, right) => Number(left.dataset.ruleOrder) - Number(right.dataset.ruleOrder))
            .filter((card) => card !== focusedCard);
        const orderedCards = [focusedCard, ...cards];
        columns.forEach((column) => {
            column.replaceChildren();
        });
        orderedCards.forEach((card, index) => {
            columns[index % columns.length]?.append(card);
        });
    } else {
        focusedCard.parentElement?.prepend(focusedCard);
    }
    focusedCard.classList.add('is-focused-result');
}

/**
 * Loads section search results from the server without replacing the page.
 *
 * @param {string} value Current search term.
 * @returns {Promise<void>} Resolves after the result fragments are replaced.
 * @sideeffect Updates the URL and clears any focused result before loading new results.
 */
async function fetchSectionResults(value) {
    const url = new URL(window.location.href);
    const normalizedValue = value.trim();
    if (normalizedValue) {
        url.searchParams.set('q', normalizedValue);
    } else {
        url.searchParams.delete('q');
    }
    url.searchParams.delete('focus');
    document.querySelectorAll('.result-card.is-focused-result').forEach((card) => {
        card.classList.remove('is-focused-result');
    });
    url.searchParams.delete('page');
    window.history.replaceState({}, '', url);

    const response = await fetch(url, { headers: { Accept: 'text/html' }, cache: 'no-store' });
    if (!response.ok) {
        throw new Error(`Impossibile aggiornare i risultati (${response.status}).`);
    }

    const documentFragment = new DOMParser().parseFromString(await response.text(), 'text/html');
    const currentResults = document.querySelector('#results-grid') || document.querySelector('.rules-list');
    const nextResults = documentFragment.querySelector('#results-grid') || documentFragment.querySelector('.rules-list');
    if (currentResults && nextResults) currentResults.replaceWith(nextResults);

    const replaceElements = (selector) => {
        const currentElements = [...document.querySelectorAll(selector)];
        const nextElements = [...documentFragment.querySelectorAll(selector)];
        currentElements.forEach((element, index) => {
            const nextElement = nextElements[index];
            if (nextElement) {
                element.replaceWith(nextElement);
            } else {
                element.remove();
            }
        });
        if (nextElements.length > currentElements.length) {
            const anchor = currentElements.at(-1);
            nextElements.slice(currentElements.length).forEach((element) => {
                (anchor?.parentElement || document.querySelector('.results-view'))?.append(element);
            });
        }
    };

    const currentEmptyState = document.querySelector('#empty-state');
    const nextEmptyState = documentFragment.querySelector('#empty-state');
    if (currentEmptyState && nextEmptyState) currentEmptyState.replaceWith(nextEmptyState);

    replaceElements('.pagination-summary');
    replaceElements('.pagination');
    refreshSectionResultReferences();
    sortResults();
    prioritizeFocusedResult();
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

/**
 * Sorts glossary cards globally and redistributes them across masonry columns.
 *
 * @param {HTMLElement} rulesList Glossary masonry container.
 * @returns {void}
 */
function sortRules(rulesList) {
    const columns = [...rulesList.querySelectorAll(':scope > .rules-column')];
    if (columns.length === 0) return;

    const cards = [...rulesList.querySelectorAll('.rule-card')];
    const focusedCard = cards.find((card) => card.classList.contains('is-focused-result'));
    const cardsToSort = cards.filter((card) => card !== focusedCard);
    const sortValue = sortSelect?.value || 'default';
    const [field, direction] = sortValue.split('-');
    const dataKey = field ? `sort${field[0].toUpperCase()}${field.slice(1)}` : '';

    cardsToSort.sort((left, right) => {
        if (sortValue === 'default') {
            return Number(left.dataset.ruleOrder) - Number(right.dataset.ruleOrder);
        }

        const leftRaw = left.dataset[dataKey] || '';
        const rightRaw = right.dataset[dataKey] || '';
        const leftValue = field === 'name' || field === 'category'
            ? leftRaw.toLocaleLowerCase('it')
            : sortableNumber(leftRaw);
        const rightValue = field === 'name' || field === 'category'
            ? rightRaw.toLocaleLowerCase('it')
            : sortableNumber(rightRaw);
        const comparison = typeof leftValue === 'string'
            ? leftValue.localeCompare(rightValue, 'it')
            : leftValue - rightValue;

        return direction === 'desc' ? -comparison : comparison;
    });

    const orderedCards = focusedCard ? [focusedCard, ...cardsToSort] : cardsToSort;
    columns.forEach((column) => column.replaceChildren());
    orderedCards.forEach((card, index) => {
        columns[index % columns.length]?.append(card);
    });
}

function sortResults() {
    if (!sortSelect) return;
    const rulesList = document.querySelector('.rules-list');
    if (rulesList) {
        sortRules(rulesList);
        return;
    }
    if (!resultsGrid) return;
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
    let searchRequestTimer;
    searchInput.addEventListener('input', () => {
        window.clearTimeout(searchRequestTimer);
        searchRequestTimer = window.setTimeout(() => {
            fetchSectionResults(searchInput.value).catch((error) => console.error(error));
        }, 250);
    });
    searchInput.form?.addEventListener('submit', (event) => event.preventDefault());
}
preserveSectionSearchOnNavigation();
if (sortSelect) {
    sortSelect.addEventListener('change', sortResults);
    sortResults();
}
prioritizeFocusedResult();
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
    const exportTypes = new Set(items.map((item) => item.type));
    const isSpellExport = exportTypes.size === 1 && items[0].type === 'armi';
    const isMixedExport = exportTypes.size > 1;
    const itemLabel = isMixedExport ? 'preferito' : (isSpellExport ? 'incantesimo' : 'oggetto');
    const itemLabelPlural = isMixedExport ? 'preferiti' : (isSpellExport ? 'incantesimi' : 'oggetti');
    const archiveName = isMixedExport ? 'preferiti.zip' : (isSpellExport ? 'incantesimi.zip' : 'oggetti.zip');
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
            <label class="export-field">
                <span class="export-checkbox-label"><input name="separateZips" type="checkbox" ${itemCount > 1 ? '' : 'disabled'}> Crea uno ZIP separato per ogni elemento</span>
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
        const separateZips = form.elements.separateZips.checked;
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
        body.append('separateZips', separateZips ? '1' : '0');
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
    if (event.target.closest('#export-favorites-button')) {
        const exportButton = event.target.closest('#export-favorites-button');
        const items = [...document.querySelectorAll('#favorites-groups .result-card:not([hidden])')]
            .filter((card) => card.dataset.export)
            .map((card) => JSON.parse(card.dataset.export));
        if (items.length > 0) {
            openExportDialog(items);
        }
        exportButton.blur();
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
                            ${globalSearchTypeIcon(item.type)}
                            <span class="global-search-result-main">
                                <strong>${escapeHtml(item.title)}</strong>
                                <span class="global-search-description">${escapeHtml(item.description)}</span>
                            </span>
                            <span class="global-search-tags">${(item.tags || []).map((tag, index) => `<span class="global-search-tag" title="${escapeAttribute(item.tagTitles?.[index] || 'Informazione aggiuntiva')}">${escapeHtml(tag)}</span>`).join('')}</span>
            </a>
                `).join('')}
            </section>
        `).join('');
    }
    globalSearchPreview.hidden = false;
    globalSearchInput.setAttribute('aria-expanded', 'true');
}

/**
 * Renders the type icon for one global search result.
 *
 * @param {string} type Result type label.
 * @returns {string} Accessible icon markup.
 */
function globalSearchTypeIcon(type) {
    const icons = {
        Arma: '⚔️',
        Armatura: '🛡️',
        Munizione: '🎯',
        Strumento: '🛠️',
        Equipaggiamento: '🎒',
        Incantesimo: '✨',
        Mostro: '👹',
        Regola: '📖',
        Categoria: '📚'
    };
    const label = type || 'Risultato';
    const icon = icons[label] || '•';

    return `<span class="global-search-type-icon" role="img" aria-label="${escapeAttribute(label)}" title="${escapeAttribute(label)}">${icon}</span>`;
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
    globalSearchInput.addEventListener('input', async () => {
        const query = globalSearchInput.value.trim();
        if (query.length < 2) {
            globalSearchData = [];
            renderGlobalSearch();
            return;
        }

        const response = await fetch(`api/search?q=${encodeURIComponent(query)}`);
        if (!response.ok) {
            globalSearchData = [];
            renderGlobalSearch();
            return;
        }
        const data = await response.json();
        globalSearchData = data.items || [];
        renderGlobalSearch();
    });
    globalSearchInput.addEventListener('focus', renderGlobalSearch);
}

document.addEventListener('click', (event) => {
    if (globalSearchPreview && !event.target.closest('.top-search-wrap')) {
        globalSearchPreview.hidden = true;
        globalSearchInput?.setAttribute('aria-expanded', 'false');
        topSearchWrap?.classList.remove('is-open');
        mobileSearchButton?.setAttribute('aria-expanded', 'false');
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
const mobileSearchButton = document.querySelector('.mobile-search-button');
const topSearchWrap = document.querySelector('.top-search-wrap');
const themeToggle = document.querySelector('.theme-toggle');

/**
 * Updates the theme toggle state and persists the selected color scheme.
 *
 * @param {'dark'|'light'} theme Theme to apply to the document.
 * @returns {void}
 */
function setTheme(theme) {
    document.documentElement.dataset.theme = theme;
    window.localStorage.setItem('isa:theme', theme);
    if (!themeToggle) return;
    const isDark = theme === 'dark';
    themeToggle.setAttribute('aria-label', isDark ? 'Attiva il tema chiaro' : 'Attiva il tema scuro');
    themeToggle.setAttribute('aria-pressed', String(isDark));
    themeToggle.querySelector('.theme-toggle-icon').textContent = isDark ? '☀️' : '🌙';
}

if (themeToggle) {
    setTheme(document.documentElement.dataset.theme === 'dark' ? 'dark' : 'light');
    themeToggle.addEventListener('click', () => {
        setTheme(document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark');
    });
}

if (mobileSearchButton && topSearchWrap && globalSearchInput) {
    mobileSearchButton.addEventListener('click', () => {
        const isOpen = topSearchWrap.classList.toggle('is-open');
        mobileSearchButton.setAttribute('aria-expanded', String(isOpen));
        if (isOpen) {
            globalSearchInput.focus();
        } else {
            globalSearchPreview.hidden = true;
            globalSearchInput.setAttribute('aria-expanded', 'false');
        }
    });
}

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
