# Changelog

All notable changes to `xuanpablo/filament-palette` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

- Action commands. `CommandItem::action($label, $event, $data)` (and `->dispatch()`
  on any item) create a command that fires a browser `CustomEvent` instead of
  navigating — the payload arrives as `event.detail`. Listen via
  `window.addEventListener(...)` or a Livewire `#[On(...)]` handler. Useful for
  toggling theme, logging out, opening modals, or any custom action.

## [2.1.1] - 2026-06-09

### Fixed

- Dark mode now applies to the topbar trigger button. Its colours were set via
  inline `style="…"` while dark mode used Tailwind `dark:` utility *classes* —
  and inline styles always win, so the `dark:` rules never took effect. Styling
  moved to a scoped `<style>` block with `.dark` selectors (theme-aware
  `var(--gray-*)`), matching the rest of the palette. Also dropped non-existent
  `fi-text-gray-*` icon utilities and made the button label translatable.

## [2.1.0] - 2026-06-09

### Changed

- Hardened the palette internals: the Alpine state and logic are now a registered
  `Alpine.data('commandPalette', …)` component (injected once per page via
  Livewire `@assets`) instead of a large inline `x-data` object, removing the
  HTML-attribute escaping fragility behind the v1.1.2 bug. Theme-aware colours
  and behaviour are unchanged; no public-API changes.

## [2.0.2] - 2026-06-09

### Fixed

- Vertically centre the `⌘K` hint within Filament's global-search field. It used
  a fixed `height: 2.5rem` pinned to the top, which overflowed Filament v5's
  ~36px input and sat slightly low. It now spans the field height
  (`inset-block: 0`) and flex-centres, independent of the input's height.

## [2.0.1] - 2026-06-09

### Fixed

- The palette is now fully theme-aware. Neutral colours use Filament's `--gray-*`
  variables and the accent uses `--primary-*` directly (Filament v5 exposes these
  as full colours, consumed as `var(--…)`). Previously the accent used the v3/v4
  `rgb(var(--primary-600))` channel syntax, which is invalid in v5 and silently
  fell back to a hardcoded blue. The palette now matches the panel's primary and
  gray theme in both light and dark mode. Neutral translucent overlays
  (backdrop, subtle hovers) remain untinted by design.

## [2.0.0] - 2026-06-09

### Changed

- **BREAKING:** Renamed the PHP namespace `Xuanpablo\FilamentPalette` →
  `Xuanpablo\CommandPalette`, and the entry-point classes `FilamentPalettePlugin`
  / `FilamentPaletteServiceProvider` → `CommandPalettePlugin` /
  `CommandPaletteServiceProvider`. Update your imports and
  `->plugins([CommandPalettePlugin::make()])`. The Composer package name
  (`xuanpablo/filament-palette`), config file/key, view namespace, publish tags,
  and translations are unchanged.

## [1.1.2] - 2026-06-09

### Fixed

- Fixed a raw `"` inside the `escapeChar` method of the palette's double-quoted
  `x-data` attribute, which closed the attribute early — leaking raw JavaScript
  as page text and throwing `SyntaxError` / `ReferenceError` (`isOpen`, `search`,
  `results`, …) on every panel page. The comparison now uses
  `String.fromCharCode(34)`. Added a regression test that asserts the `x-data`
  attribute contains no attribute-breaking double-quote.

## [1.1.1] - 2026-06-09

### Fixed

- The topbar trigger no longer disappears when Filament global search is enabled.
  A compact `⌘K` hint is now embedded in the global-search field (via the
  `GLOBAL_SEARCH_END` render hook); set
  `show_topbar_button_when_global_search_enabled` to `true` to show the full
  standalone button instead.

## [1.1.0] - 2026-06-09

### Added

- Bundled translations for Chinese Simplified (`zh_CN`), Hindi (`hi`), Spanish
  (`es`), Portuguese – Brazil (`pt_BR`), Japanese (`ja`), Russian (`ru`), German
  (`de`), French (`fr`), and Korean (`ko`).

## [1.0.0] - 2026-06-09

First release of the `xuanpablo/filament-palette` fork (formerly
`usamamuneerchaudhary/filament-command-palette`).

### Added

- Fuzzy search with scoring, ranking, and matched-character highlighting.
- Recent commands, remembered per panel in the browser and shown first.
- Keyboard navigation: arrow keys with wrap-around, `Home`/`End`, `Enter`, `Esc`,
  and an on-screen keyboard-hint footer.
- `CommandItem::description()` and `CommandItem::keywords()` for richer,
  more discoverable results.
- Authorization filtering: auto-discovered pages and resources respect
  `canAccess()` / `canCreate()` and fail closed.
- SPA navigation via Filament's `wire:navigate`.
- Accessibility: focus trap, scroll lock, focus restoration, and
  `prefers-reduced-motion` support.
- Publishable translations (`filament-palette-translations`) for all UI strings.
- Config options: `placeholder`, `show_footer`, `show_recent`, `recent_limit`.
- Lazy loading: commands are fetched when the palette first opens.
- Tooling: GitHub Actions test matrix, Pint, and PHPStan (level 5).

### Changed

- Rebranded to `xuanpablo/filament-palette` under the `Xuanpablo\FilamentPalette`
  namespace.
- Theme-aware UI driven by the panel's primary colour, with a refined light and
  dark appearance.
- Requires PHP 8.4+, Filament v5, and Laravel 13.

### Fixed

- Page title fallback previously called the instance method `getTitle()`
  statically, which errored and was silently swallowed.

[Unreleased]: https://github.com/xuanpablo/filament-palette/compare/v2.1.1...HEAD
[2.1.1]: https://github.com/xuanpablo/filament-palette/compare/v2.1.0...v2.1.1
[2.1.0]: https://github.com/xuanpablo/filament-palette/compare/v2.0.2...v2.1.0
[2.0.2]: https://github.com/xuanpablo/filament-palette/compare/v2.0.1...v2.0.2
[2.0.1]: https://github.com/xuanpablo/filament-palette/compare/v2.0.0...v2.0.1
[2.0.0]: https://github.com/xuanpablo/filament-palette/compare/v1.1.2...v2.0.0
[1.1.2]: https://github.com/xuanpablo/filament-palette/compare/v1.1.1...v1.1.2
[1.1.1]: https://github.com/xuanpablo/filament-palette/compare/v1.1.0...v1.1.1
[1.1.0]: https://github.com/xuanpablo/filament-palette/compare/v1.0.0...v1.1.0
[1.0.0]: https://github.com/xuanpablo/filament-palette/releases/tag/v1.0.0
