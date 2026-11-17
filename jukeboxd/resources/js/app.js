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
                const isListened = Boolean(album.is_listened);
                const stateClasses = isListened ? 'text-brand-green bg-emerald-50' : 'text-neutral-300';

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
                        <button type="button" 
                                title="Marquer comme écouté"
                                data-album-id="${escapeHtml(album.id)}"
                                data-listened="${isListened ? 'true' : 'false'}"
                                class="listened-btn p-1.5 rounded-full ${stateClasses} hover:text-neutral-600 hover:bg-neutral-100 transition-all shrink-0 cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 pointer-events-none" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                            </svg>
                        </button>
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

    const applyListenedState = (albumId, listened) => {
        document.querySelectorAll(`.listened-btn[data-album-id="${CSS.escape(albumId)}"]`).forEach((btn) => {
            btn.dataset.listened = listened ? 'true' : 'false';
            btn.classList.toggle('text-neutral-300', !listened);
            btn.classList.toggle('text-brand-green', listened);
            btn.classList.toggle('bg-emerald-50', listened);
        });
    };

    const refreshProfileSection = async () => {
        const section = document.getElementById('profile-listened-section');
        if (!section) return;

        try {
            const res = await fetch(window.location.href, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });
            if (!res.ok) return;

            const html = await res.text();
            const doc = new DOMParser().parseFromString(html, 'text/html');
            const updatedSection = doc.getElementById('profile-listened-section');
            if (updatedSection) {
                section.innerHTML = updatedSection.innerHTML;
            }
        } catch {
            // Ignore background refresh error
        }
    };

    // Global click listener: handles listened button toggle and popover click-outside
    document.addEventListener('click', async (e) => {
        const listenedBtn = e.target.closest('.listened-btn');
        if (listenedBtn) {
            e.preventDefault();
            e.stopPropagation();

            const albumId = listenedBtn.dataset.albumId;
            if (!albumId || listenedBtn.dataset.loading === 'true') return;

            const previousState = listenedBtn.dataset.listened === 'true';
            const nextState = !previousState;

            // Optimistic UI update
            listenedBtn.dataset.loading = 'true';
            applyListenedState(albumId, nextState);

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const res = await fetch(`/albums/${encodeURIComponent(albumId)}/listened`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                });

                if (res.status === 401) {
                    window.location.href = '/login';
                    return;
                }

                if (!res.ok) throw new Error('Failed to toggle listened');

                const data = await res.json();
                applyListenedState(albumId, Boolean(data.listened));
                await refreshProfileSection();
            } catch {
                // Rollback on failure
                applyListenedState(albumId, previousState);
            } finally {
                delete listenedBtn.dataset.loading;
            }
            return;
        }

        if (!form.contains(e.target)) {
            popover.classList.add('hidden');
        }
    });
});
