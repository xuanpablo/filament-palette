<?php

use Xuanpablo\CommandPalette\Support\CommandItem;

return [
    /*
    |--------------------------------------------------------------------------
    | Key Bindings
    |--------------------------------------------------------------------------
    | Keyboard shortcut to open the command palette.
    | Use 'mod' for CMD on Mac and Ctrl on Windows/Linux.
    | When using Filament global search with a shortcut too, use different
    | keys to avoid conflict (e.g. panel->globalSearchKeyBindings(['mod+shift+k'])).
    */
    'key_bindings' => [
        'mod+k',
    ],

    /*
    |--------------------------------------------------------------------------
    | Show Topbar Button
    |--------------------------------------------------------------------------
    | Whether to show an optional trigger button in the topbar.
    */
    'show_topbar_button' => true,

    /*
    |--------------------------------------------------------------------------
    | Show Topbar Button When Global Search Enabled
    |--------------------------------------------------------------------------
    | When Filament global search is enabled, the command palette topbar
    | button is hidden by default to avoid two search-style controls. Set to
    | true if you want both visible (consider using different key bindings
    | for each, e.g. globalSearchKeyBindings(['mod+shift+k']) on the panel).
    */
    'show_topbar_button_when_global_search_enabled' => false,

    /*
    |--------------------------------------------------------------------------
    | Max Results
    |--------------------------------------------------------------------------
    | Maximum number of results to display in the command palette.
    */
    'max_results' => 10,

    /*
    |--------------------------------------------------------------------------
    | Search Placeholder
    |--------------------------------------------------------------------------
    | The placeholder text shown in the palette's search input. Leave null to
    | use the (translatable) package default.
    */
    'placeholder' => null,

    /*
    |--------------------------------------------------------------------------
    | Show Footer
    |--------------------------------------------------------------------------
    | Whether to show the footer with keyboard navigation hints
    | (arrows to navigate, enter to open, escape to close).
    */
    'show_footer' => true,

    /*
    |--------------------------------------------------------------------------
    | Global Search Results
    |--------------------------------------------------------------------------
    | When enabled (and the panel has a global search provider), matching
    | records are fetched as you type and shown alongside commands. This adds a
    | debounced server round-trip per search, so it is off by default.
    */
    'include_global_search_results' => false,

    'global_search_results_limit' => 10,

    'global_search_debounce_ms' => 300,

    /*
    |--------------------------------------------------------------------------
    | Recent Commands
    |--------------------------------------------------------------------------
    | When enabled, recently used commands are remembered per panel (in the
    | browser) and shown first while the search is empty. 'recent_limit' caps
    | how many are kept.
    */
    'show_recent' => true,

    'recent_limit' => 5,

    /*
    |--------------------------------------------------------------------------
    | Include Publish Views Command
    |--------------------------------------------------------------------------
    | When true, adds a "Publish views" option to the command palette. Selecting
    | it opens a page where you can publish the Blade views for customization.
    */
    'include_publish_views_command' => true,

    /*
    |--------------------------------------------------------------------------
    | Custom Commands
    |--------------------------------------------------------------------------
    | Additional commands to include. Each closure should return an array of
    | Xuanpablo\CommandPalette\Support\CommandItem instances.
    |
    | Example:
    | 'custom_commands' => [
    | fn () => [
    |        CommandItem::make('My Action', '/some-url')->group('Custom'),
    |        CommandItem::make('Other', '/other-url', 'Custom'), // 3rd param also works
    |    ],
    | ],
    */
    'custom_commands' => [
        //
    ],
];
