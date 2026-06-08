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
            inset-block-start: 0;
            inset-inline-end: 0.625rem;
            height: 2.5rem;
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
            border: 1px solid rgb(229 231 235);
            background: rgb(255 255 255);
            color: rgb(107 114 128);
            transition: color 0.15s, border-color 0.15s;
        }
        .fp-gs-hint button:hover kbd { color: rgb(var(--primary-600, 37 99 235)); border-color: rgb(var(--primary-400, 96 165 250)); }
        .dark .fp-gs-hint kbd { border-color: rgba(255, 255, 255, 0.1); background: rgba(255, 255, 255, 0.05); color: rgb(209 213 219); }
        .dark .fp-gs-hint button:hover kbd { color: rgb(var(--primary-400, 96 165 250)); }
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
