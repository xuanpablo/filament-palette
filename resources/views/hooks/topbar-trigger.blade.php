@php
    $platform = \Filament\Support\Enums\Platform::detect();
    $shortcutHint = $platform === \Filament\Support\Enums\Platform::Mac ? '⌘K' : 'Ctrl+K';
    $showButton = ! filament()->isGlobalSearchEnabled() || config('filament-palette.show_topbar_button_when_global_search_enabled', false);
@endphp
@if ($showButton)
    <style>
        .fp-trigger {
            display: flex; align-items: center; gap: 0.75rem;
            width: 100%; max-width: 20rem; min-height: 2.5rem;
            padding: 0.5rem 0.75rem 0.5rem 1rem;
            border-radius: 0.5rem;
            border: 1px solid var(--gray-200, #e5e7eb);
            background: var(--gray-50, #f9fafb);
            color: var(--gray-500, #6b7280);
            font-size: 0.875rem; line-height: 1.25rem; text-align: left;
            cursor: pointer; outline: none;
            transition: border-color 0.15s, background-color 0.15s;
        }
        .fp-trigger:hover { border-color: var(--gray-300, #d1d5db); background: var(--gray-100, #f3f4f6); }
        .dark .fp-trigger { border-color: rgba(255, 255, 255, 0.1); background: rgba(255, 255, 255, 0.05); color: var(--gray-400, #9ca3af); }
        .dark .fp-trigger:hover { border-color: rgba(255, 255, 255, 0.2); background: rgba(255, 255, 255, 0.1); }

        .fp-trigger-icon { display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; width: 1rem; height: 1rem; }
        .fp-trigger-icon svg { width: 1rem; height: 1rem; }
        .fp-trigger-label { flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

        .fp-trigger kbd {
            flex-shrink: 0; padding: 0.125rem 0.375rem;
            font-size: 0.75rem; border-radius: 0.25rem;
            border: 1px solid var(--gray-200, #e5e7eb); background: white; color: var(--gray-500, #6b7280);
        }
        .dark .fp-trigger kbd { border-color: rgba(255, 255, 255, 0.1); background: var(--gray-900, #111827); color: var(--gray-400, #9ca3af); }
    </style>

    <button
        type="button"
        class="fp-trigger"
        x-data="{}"
        x-on:click="$dispatch('open-command-palette')"
    >
        <span class="fp-trigger-icon">
            <x-filament::icon :icon="\Filament\Support\Icons\Heroicon::MagnifyingGlass" />
        </span>
        <span class="fp-trigger-label">{{ __('filament-palette::filament-palette.placeholder') }}</span>
        <kbd>{{ $shortcutHint }}</kbd>
    </button>
@endif
