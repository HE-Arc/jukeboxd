document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('search-input');
    const popover = document.getElementById('search-popover');
    const results = document.getElementById('search-results');
    const form = document.getElementById('search-form');

    if (!input || !popover || !results || !form) return;

    let debounceTimer = null;
    let abortController = null;

    const escapeHtml = (str) => {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    };

    const search = async (query) => {
        const trimmed = query.trim();
        if (trimmed.length < 2) {
            popover.classList.add('hidden');
            results.innerHTML = '';
            return;
        }

        // Cancel previous request if still flying
        if (abortController) {
            abortController.abort();
        }
        abortController = new AbortController();

        try {
            const res = await fetch(`/search/live?q=${encodeURIComponent(trimmed)}`, {
                signal: abortController.signal,
                headers: { 'Accept': 'application/json' }
            });

            if (!res.ok) throw new Error('Search failed');

            const albums = await res.json();

            if (!albums || albums.length === 0) {
                results.innerHTML = `
                    <div class="p-4 text-center text-xs text-neutral-500">
                        Aucun résultat
                    </div>
                `;
                popover.classList.remove('hidden');
                return;
            }

            results.innerHTML = albums.slice(0, 5).map(album => {
                const cover = album.cover_url
                    ? `<img src="${escapeHtml(album.cover_url)}" alt="${escapeHtml(album.title)}" class="w-10 h-10 rounded-md object-cover bg-neutral-100 shrink-0" />`
                    : `<div class="w-10 h-10 rounded-md bg-neutral-200 flex items-center justify-center text-[10px] text-neutral-500 font-medium shrink-0">♫</div>`;

                const meta = [album.artist, album.year].filter(Boolean).join(' • ');

                return `
                    <div class="flex items-center gap-3 p-2.5 text-left select-none">
                        ${cover}
                        <div class="min-w-0 flex-1">
                            <div class="text-xs font-semibold text-neutral-900 truncate">
                                ${escapeHtml(album.title)}
                            </div>
                            <div class="text-[11px] text-neutral-500 truncate mt-0.5">
                                ${escapeHtml(meta)}
                            </div>
                        </div>
                    </div>
                `;
            }).join('');

            popover.classList.remove('hidden');

        } catch (err) {
            if (err.name !== 'AbortError') {
                popover.classList.add('hidden');
            }
        }
    };

    // Live typing with debounce
    input.addEventListener('input', (e) => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            search(e.target.value);
        }, 250);
    });

    // Re-open on focus if query >= 2 chars
    input.addEventListener('focus', () => {
        if (input.value.trim().length >= 2 && results.innerHTML.trim() !== '') {
            popover.classList.remove('hidden');
        }
    });

    // Close on click outside
    document.addEventListener('click', (e) => {
        if (!form.contains(e.target)) {
            popover.classList.add('hidden');
        }
    });
});
