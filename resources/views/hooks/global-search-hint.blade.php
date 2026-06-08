@php
    // Rendered inside Filament's global-search field (GLOBAL_SEARCH_END), which
    // only outputs when global search is enabled. Show the compact hint unless
    // the full standalone button is configured to appear alongside it.
    $showHint = config('filament-palette.show_topbar_button', true)
        && ! config('filament-palette.show_topbar_button_when_global_search_enabled', false);

    $platform = \Filament\Support\Enums\Platform::detect();
    $shortcutHint = $platform === \Filament\Support\Enums\Platform::Mac ? '⌘K' : 'Ctrl+K';
    $label = __('filament-palette::filament-palette.placeholder');
@endphp

@if ($showHint)
    <style>
        .fi-global-search-ctn { position: relative; }
        .fi-global-search-ctn .fi-global-search-field .fi-input { padding-inline-end: 3.5rem; }

        .fp-gs-hint {
            position: absolute;
            inset-block: 0;
            inset-inline-end: 0.625rem;
            display: inline-flex;
            align-items: center;
            pointer-events: none;
            z-index: 10;
        }
        .fp-gs-hint button {
            pointer-events: auto;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            border: 0;
            background: transparent;
            padding: 0;
            outline: none;
        }
        .fp-gs-hint kbd {
            display: inline-flex;
            align-items: center;
            padding: 0.125rem 0.4rem;
            font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
            font-size: 0.6875rem;
            line-height: 1;
            border-radius: 0.3125rem;
            border: 1px solid var(--gray-200, #e5e7eb);
            background: white;
            color: var(--gray-500, #6b7280);
            transition: color 0.15s, border-color 0.15s;
        }
        .fp-gs-hint button:hover kbd { color: var(--primary-600, #2563eb); border-color: var(--primary-400, #60a5fa); }
        .dark .fp-gs-hint kbd { border-color: rgba(255, 255, 255, 0.1); background: rgba(255, 255, 255, 0.05); color: var(--gray-300, #d1d5db); }
        .dark .fp-gs-hint button:hover kbd { color: var(--primary-400, #60a5fa); }
    </style>

    <div class="fp-gs-hint" x-data="{}">
        <button
            type="button"
            x-on:click="$dispatch('open-command-palette')"
            aria-label="{{ $label }}"
            title="{{ $label }}"
        >
            <kbd>{{ $shortcutHint }}</kbd>
        </button>
    </div>
@endif
