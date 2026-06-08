@php
    $keyBindings = config('filament-palette.key_bindings', ['mod+k']);
    $mousetrapBindings = collect($keyBindings)->map(fn (string $key): string => str_replace('+', '-', $key))->implode('.');
@endphp

<style>
    .fp-palette [x-cloak] { display: none !important; }

    @media (prefers-reduced-motion: reduce) {
        .fp-palette *, .fp-palette *::before, .fp-palette *::after { transition-duration: 0s !important; animation-duration: 0s !important; }
    }

    .fp-overlay { position: fixed; inset: 0; z-index: 9999; display: flex; align-items: flex-start; justify-content: center; padding: 1rem; }
    @media (min-width: 640px) { .fp-overlay { padding-top: 12vh; } }

    .fp-backdrop { position: fixed; inset: 0; background: rgba(17, 24, 39, 0.45); }
    .dark .fp-backdrop { background: rgba(0, 0, 0, 0.6); }

    .fp-panel {
        position: relative;
        width: 100%;
        max-width: 40rem;
        max-height: min(32rem, 80vh);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        border-radius: 0.875rem;
        background: rgb(255 255 255);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35), 0 0 0 1px rgba(17, 24, 39, 0.05);
    }
    .dark .fp-panel {
        background: rgb(17 24 39);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6), 0 0 0 1px rgba(255, 255, 255, 0.08);
    }

    .fp-search-row { display: flex; align-items: center; gap: 0.625rem; padding: 0.875rem 1rem; border-bottom: 1px solid rgb(243 244 246); }
    .dark .fp-search-row { border-color: rgba(255, 255, 255, 0.08); }
    .fp-search-icon { flex-shrink: 0; width: 1.125rem; height: 1.125rem; color: rgb(156 163 175); }
    .fp-search-input {
        flex: 1; min-width: 0; border: 0; background: transparent; outline: none;
        font-size: 0.975rem; line-height: 1.5rem; color: rgb(17 24 39);
        padding: 0; appearance: none;
    }
    .fp-search-input::placeholder { color: rgb(156 163 175); }
    .dark .fp-search-input { color: rgb(243 244 246); }
    .fp-search-input::-webkit-search-cancel-button { -webkit-appearance: none; }

    .fp-results { flex: 1; min-height: 0; overflow-y: auto; padding: 0.375rem; scroll-padding-block: 0.375rem; }
    .fp-results::-webkit-scrollbar { width: 8px; }
    .fp-results::-webkit-scrollbar-track { background: transparent; }
    .fp-results::-webkit-scrollbar-thumb { background: rgb(203 213 225); border-radius: 4px; border: 2px solid transparent; background-clip: content-box; }
    .fp-results::-webkit-scrollbar-thumb:hover { background: rgb(148 163 184); background-clip: content-box; }
    .dark .fp-results::-webkit-scrollbar-thumb { background: rgb(71 85 105); background-clip: content-box; }
    .dark .fp-results::-webkit-scrollbar-thumb:hover { background: rgb(100 116 139); background-clip: content-box; }
    .fp-results { scrollbar-width: thin; scrollbar-color: rgb(203 213 225) transparent; }
    .dark .fp-results { scrollbar-color: rgb(71 85 105) transparent; }

    .fp-group { padding: 0.625rem 0.625rem 0.25rem; font-size: 0.6875rem; font-weight: 600; letter-spacing: 0.06em; text-transform: uppercase; color: rgb(107 114 128); }
    .dark .fp-group { color: rgb(148 163 184); }

    .fp-option {
        display: flex; align-items: center; gap: 0.75rem;
        padding: 0.5rem 0.625rem; border-radius: 0.5rem; cursor: pointer;
        color: rgb(17 24 39); text-decoration: none;
        scroll-margin: 0.375rem;
    }
    .dark .fp-option { color: rgb(243 244 246); }
    .fp-option.fp-active { background: rgb(243 244 246); }
    .dark .fp-option.fp-active { background: rgba(255, 255, 255, 0.06); }

    .fp-option-icon { display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; width: 1.125rem; height: 1.125rem; color: rgb(107 114 128); }
    .dark .fp-option-icon { color: rgb(148 163 184); }
    .fp-active .fp-option-icon { color: rgb(var(--primary-600, 37 99 235)); }

    .fp-option-text { min-width: 0; flex: 1; display: flex; flex-direction: column; gap: 0.0625rem; }
    .fp-option-label { font-size: 0.875rem; font-weight: 500; line-height: 1.25rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .fp-option-desc { font-size: 0.75rem; line-height: 1rem; color: rgb(107 114 128); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .dark .fp-option-desc { color: rgb(148 163 184); }

    .fp-match { font-weight: 700; color: rgb(var(--primary-600, 37 99 235)); }
    .dark .fp-match { color: rgb(var(--primary-400, 96 165 250)); }

    .fp-enter-hint { flex-shrink: 0; opacity: 0; transition: opacity 0.1s; }
    .fp-active .fp-enter-hint { opacity: 1; }

    .fp-empty { display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 0.625rem; padding: 2.5rem 1rem; text-align: center; }
    .fp-empty-icon { width: 1.75rem; height: 1.75rem; color: rgb(209 213 219); }
    .fp-spin { animation: fp-spin 0.7s linear infinite; color: rgb(var(--primary-500, 99 102 241)); }
    @keyframes fp-spin { to { transform: rotate(360deg); } }
    .dark .fp-empty-icon { color: rgb(75 85 99); }
    .fp-empty-text { font-size: 0.875rem; color: rgb(107 114 128); }
    .dark .fp-empty-text { color: rgb(148 163 184); }

    .fp-footer { display: flex; align-items: center; gap: 1rem; padding: 0.5rem 0.875rem; border-top: 1px solid rgb(243 244 246); font-size: 0.75rem; color: rgb(107 114 128); flex-shrink: 0; }
    .dark .fp-footer { border-color: rgba(255, 255, 255, 0.08); color: rgb(148 163 184); }
    .fp-footer-hint { display: inline-flex; align-items: center; gap: 0.375rem; }

    kbd.fp-kbd {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 1.25rem; height: 1.25rem; padding: 0 0.3125rem;
        border-radius: 0.3125rem; border: 1px solid rgb(229 231 235);
        background: rgb(249 250 251); color: rgb(75 85 99);
        font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: 0.6875rem; line-height: 1;
    }
    .dark kbd.fp-kbd { border-color: rgba(255, 255, 255, 0.1); background: rgba(255, 255, 255, 0.05); color: rgb(209 213 219); }
</style>

<div
    class="fp-palette"
    x-data="{
        commands: [],
        recentIds: [],
        search: '',
        maxResults: {{ $maxResults }},
        recentLimit: {{ $recentLimit }},
        showRecent: {{ $showRecent ? 'true' : 'false' }},
        recentLabel: @js($recentLabel),
        paletteKey: @js($paletteKey),
        selectedIndex: 0,
        isOpen: false,
        loaded: false,
        loading: false,

        init() {
            this.loadRecent();
            this.$watch('search', () => { this.selectedIndex = 0; this.$nextTick(() => this.scrollActiveIntoView()); });
        },

        open() {
            this.isOpen = true;
            this.search = '';
            this.selectedIndex = 0;
            this.loadRecent();
            this.fetchCommands();
            this.$nextTick(() => this.$refs.searchInput?.focus());
        },

        fetchCommands() {
            // Commands are loaded lazily on first open, then cached client-side.
            if (this.loaded || this.loading) {
                return;
            }
            this.loading = true;
            Promise.resolve(this.$wire.loadCommands())
                .then(commands => { this.commands = Array.isArray(commands) ? commands : []; this.loaded = true; })
                .catch(() => { this.commands = []; })
                .finally(() => { this.loading = false; });
        },

        close() { this.isOpen = false; },

        storageKey() { return 'filament-palette:recent:' + this.paletteKey; },

        loadRecent() {
            try { this.recentIds = JSON.parse(localStorage.getItem(this.storageKey()) || '[]'); }
            catch (e) { this.recentIds = []; }
        },

        recentCommands() {
            const byId = new Map(this.commands.map(c => [c.id, c]));
            return this.recentIds.map(id => byId.get(id)).filter(Boolean).slice(0, this.recentLimit);
        },

        recordRecent(id) {
            this.recentIds = [id, ...this.recentIds.filter(x => x !== id)].slice(0, this.recentLimit);
            try { localStorage.setItem(this.storageKey(), JSON.stringify(this.recentIds)); } catch (e) {}
        },

        escapeChar(ch) {
            // Compare via char code (34) so no raw double-quote character ever
            // appears inside this double-quoted x-data attribute, which would
            // otherwise close the attribute early and break the component.
            return ch === '&' ? '&amp;' : ch === '<' ? '&lt;' : ch === '>' ? '&gt;' : ch === String.fromCharCode(34) ? '&quot;' : ch;
        },

        escape(str) {
            let out = '';
            for (const ch of String(str)) out += this.escapeChar(ch);
            return out;
        },

        highlight(label, indices) {
            if (!indices || !indices.length) return this.escape(label);
            const set = new Set(indices);
            let out = '';
            for (let i = 0; i < label.length; i++) {
                const ch = this.escapeChar(label[i]);
                out += set.has(i) ? '<span class=\'fp-match\'>' + ch + '</span>' : ch;
            }
            return out;
        },

        fuzzy(query, text) {
            if (!query) return { score: 0, indices: [] };
            const q = query.toLowerCase();
            const t = String(text || '').toLowerCase();
            const indices = [];
            let qi = 0, score = 0, run = 0, last = -2;
            for (let ti = 0; ti < t.length && qi < q.length; ti++) {
                if (t[ti] === q[qi]) {
                    indices.push(ti);
                    let bonus = 1;
                    if (ti === last + 1) { run++; bonus += run * 4; } else { run = 0; }
                    const prev = ti > 0 ? t[ti - 1] : ' ';
                    if (/[\s\-_/.]/.test(prev)) bonus += 10;
                    if (ti === 0) bonus += 15;
                    score += bonus;
                    last = ti;
                    qi++;
                }
            }
            if (qi < q.length) return null;
            score -= (t.length - indices.length) * 0.1;
            return { score, indices };
        },

        scoreItem(item, q) {
            let best = null, labelIndices = [];
            const lab = this.fuzzy(q, item.label);
            if (lab) { best = lab.score * 2; labelIndices = lab.indices; }
            for (const kw of (item.keywords || [])) {
                const m = this.fuzzy(q, kw);
                if (m) best = Math.max(best ?? -Infinity, m.score * 1.2);
            }
            const g = this.fuzzy(q, item.group);
            if (g) best = Math.max(best ?? -Infinity, g.score * 0.8);
            if (item.description) {
                const d = this.fuzzy(q, item.description);
                if (d) best = Math.max(best ?? -Infinity, d.score * 0.6);
            }
            if (best === null) return null;
            return { score: best, indices: labelIndices };
        },

        get results() {
            const q = this.search.trim();
            const out = [];
            let cmdIndex = 0;
            const push = (item, indices) => out.push({ type: 'command', cmdIndex: cmdIndex++, item, hl: this.highlight(item.label, indices) });

            if (q) {
                const scored = [];
                for (const c of this.commands) {
                    const s = this.scoreItem(c, q);
                    if (s) scored.push({ c, score: s.score, indices: s.indices });
                }
                scored.sort((a, b) => b.score - a.score || a.c.label.localeCompare(b.c.label));
                const capped = scored.slice(0, this.maxResults);
                const grouped = {};
                for (const e of capped) (grouped[e.c.group] ??= []).push(e);
                for (const g of Object.keys(grouped)) {
                    out.push({ type: 'header', group: g });
                    for (const e of grouped[g]) push(e.c, e.indices);
                }
                return out;
            }

            const recents = this.showRecent ? this.recentCommands() : [];
            const recentSet = new Set(recents.map(c => c.id));
            if (recents.length) {
                out.push({ type: 'header', group: this.recentLabel });
                for (const c of recents) push(c, []);
            }
            const grouped = {};
            for (const c of this.commands) {
                if (recentSet.has(c.id)) continue;
                (grouped[c.group] ??= []).push(c);
            }
            for (const g of Object.keys(grouped)) {
                out.push({ type: 'header', group: g });
                for (const c of grouped[g]) push(c, []);
            }
            return out;
        },

        get commandCount() {
            return this.results.filter(e => e.type === 'command').length;
        },

        move(delta) {
            const count = this.commandCount;
            if (count === 0) return;
            this.selectedIndex = (this.selectedIndex + delta + count) % count;
            this.scrollActiveIntoView();
        },

        toEdge(index) {
            const count = this.commandCount;
            if (count === 0) return;
            this.selectedIndex = index < 0 ? count - 1 : 0;
            this.scrollActiveIntoView();
        },

        scrollActiveIntoView() {
            this.$nextTick(() => {
                this.$refs.results?.querySelector('[data-index=\'' + this.selectedIndex + '\']')?.scrollIntoView({ block: 'nearest' });
            });
        },

        choose() {
            const el = this.$refs.results?.querySelector('[data-index=\'' + this.selectedIndex + '\']');
            if (el) el.click();
        },
    }"
    x-mousetrap.global.{{ $mousetrapBindings }}="open()"
    x-on:open-command-palette.window="open()"
    x-on:keydown.escape.window="close()"
>
    <div
        x-show="isOpen"
        x-cloak
        class="fp-overlay"
        role="dialog"
        aria-modal="true"
        aria-label="Command palette"
        x-on:click.self="close()"
    >
        <div
            class="fp-backdrop"
            aria-hidden="true"
            x-show="isOpen"
            x-transition.opacity.duration.200ms
            x-on:click="close()"
        ></div>

        <div
            class="fp-panel"
            x-show="isOpen"
            x-trap.noscroll="isOpen"
            x-transition.opacity.scale.95.duration.150ms
        >
            <div class="fp-search-row">
                <svg class="fp-search-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
                <input
                    type="search"
                    x-model="search"
                    x-ref="searchInput"
                    class="fp-search-input"
                    placeholder="{{ $placeholder }}"
                    autocomplete="off"
                    autocorrect="off"
                    autocapitalize="off"
                    spellcheck="false"
                    role="combobox"
                    aria-expanded="true"
                    aria-controls="fp-listbox"
                    x-bind:aria-activedescendant="commandCount ? 'fp-opt-' + selectedIndex : null"
                    x-on:keydown.down.prevent="move(1)"
                    x-on:keydown.up.prevent="move(-1)"
                    x-on:keydown.home.prevent="toEdge(0)"
                    x-on:keydown.end.prevent="toEdge(-1)"
                    x-on:keydown.enter.prevent="choose()"
                />
            </div>

            <div x-ref="results" class="fp-results">
                <template x-if="loading && commandCount === 0">
                    <div class="fp-empty">
                        <svg class="fp-empty-icon fp-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3a9 9 0 1 0 9 9" />
                        </svg>
                        <p class="fp-empty-text">{{ __('filament-palette::filament-palette.loading') }}</p>
                    </div>
                </template>

                <template x-if="! loading && commandCount === 0">
                    <div class="fp-empty">
                        <svg class="fp-empty-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                        <p class="fp-empty-text">{{ __('filament-palette::filament-palette.empty') }}</p>
                    </div>
                </template>

                <ul x-show="commandCount > 0" id="fp-listbox" role="listbox" style="list-style: none; margin: 0; padding: 0;">
                    <template x-for="entry in results" :key="entry.type === 'header' ? 'h:' + entry.group : 'c:' + entry.item.id">
                        <li role="presentation">
                            <div x-show="entry.type === 'header'" class="fp-group" x-text="entry.group"></div>

                            <a
                                x-show="entry.type === 'command'"
                                class="fp-option"
                                :id="entry.type === 'command' ? 'fp-opt-' + entry.cmdIndex : null"
                                :data-index="entry.cmdIndex"
                                :class="{ 'fp-active': entry.cmdIndex === selectedIndex }"
                                :href="entry.item?.url"
                                :target="entry.item?.openInNewTab ? '_blank' : null"
                                :rel="entry.item?.openInNewTab ? 'noopener noreferrer' : null"
                                x-bind="{ 'wire:navigate': entry.type === 'command' && !entry.item?.openInNewTab }"
                                role="option"
                                :aria-selected="entry.cmdIndex === selectedIndex"
                                x-on:mouseenter="selectedIndex = entry.cmdIndex"
                                x-on:click="recordRecent(entry.item.id); close()"
                            >
                                <span class="fp-option-icon" x-html="entry.item?.iconHtml"></span>
                                <span class="fp-option-text">
                                    <span class="fp-option-label" x-html="entry.hl"></span>
                                    <template x-if="entry.item?.description">
                                        <span class="fp-option-desc" x-text="entry.item.description"></span>
                                    </template>
                                </span>
                                <kbd class="fp-kbd fp-enter-hint" aria-hidden="true">&crarr;</kbd>
                            </a>
                        </li>
                    </template>
                </ul>
            </div>

            @if ($showFooter)
                <div class="fp-footer">
                    <span class="fp-footer-hint"><kbd class="fp-kbd">&uarr;</kbd><kbd class="fp-kbd">&darr;</kbd> {{ __('filament-palette::filament-palette.hints.navigate') }}</span>
                    <span class="fp-footer-hint"><kbd class="fp-kbd">&crarr;</kbd> {{ __('filament-palette::filament-palette.hints.open') }}</span>
                    <span class="fp-footer-hint"><kbd class="fp-kbd">esc</kbd> {{ __('filament-palette::filament-palette.hints.close') }}</span>
                </div>
            @endif
        </div>
    </div>
</div>
