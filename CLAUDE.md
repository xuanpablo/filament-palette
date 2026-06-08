# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

A distributable Filament v5 plugin (`xuanpablo/filament-palette`) that adds a
Spotlight / ⌘K command palette to Filament panels. It is a **library**, not an
app: there is no host application here, no build step, and `composer.lock` is not
tracked.

Requirements: PHP 8.4+, Filament v5, Laravel 13.

## Commands

```bash
composer install                      # deps (Laravel 13 / Filament 5 / testbench 11)
composer test                         # PHPUnit (NOT Pest)
vendor/bin/phpunit --filter test_name # single test (or --filter CommandItemTest)
composer lint                         # Pint (laravel preset) — write
composer lint:test                    # Pint — check only (CI)
composer analyse                      # PHPStan level 5 (larastan), src only
```

CI (`.github/workflows/tests.yml`) runs the suite on PHP 8.4/8.5 × Laravel 13,
plus Pint and PHPStan.

## Naming: read before renaming anything

Two naming systems coexist **on purpose** — do not "unify" them:

- **PHP namespace + classes:** `Xuanpablo\CommandPalette`, `CommandPalettePlugin`,
  `CommandPaletteServiceProvider`.
- **Composer package name + all runtime string identifiers:** `filament-palette`.
  This covers the config file/key (`config('filament-palette.*')`), the view
  namespace (`filament-palette::`), publish tags (`filament-palette-config`,
  `-views`, `-translations`), the artisan signature (`filament-palette:publish-views`),
  the spatie `->name('filament-palette')`, the plugin `getId()`, and the
  translations file (`lang/<locale>/filament-palette.php`, keys referenced as
  `filament-palette::filament-palette.*`).

Changing anything in the second group is a **breaking change** for consumers.

## Architecture

**Render path (every panel page):** `CommandPaletteServiceProvider` boots via
spatie/laravel-package-tools. `CommandPalettePlugin::register()` wires three
Filament render hooks:
- `BODY_END` → `hooks/body-end.blade.php` → mounts the `Livewire\CommandPalette`
  component (the palette UI + modal).
- `GLOBAL_SEARCH_BEFORE` → `hooks/topbar-trigger.blade.php` → the standalone ⌘K
  button, shown only when global search is **disabled**.
- `GLOBAL_SEARCH_END` → `hooks/global-search-hint.blade.php` → a compact ⌘K hint
  embedded in Filament's search field, shown only when global search is **enabled**
  (this hook only renders with the field). These two are mutually exclusive; the
  `show_topbar_button_when_global_search_enabled` config flips to a side-by-side
  button instead.

**Commands are loaded lazily.** `Livewire\CommandPalette::render()` ships only the
shell + config; it does NOT build the command list. On first open, Alpine calls
the Livewire action `loadCommands()`, which serialises commands (incl.
server-rendered icon HTML) and returns them to the client, cached thereafter.

**Command discovery** lives in `Support/Commands/NavigationCommands::get()`:
navigation items, pages, and resources of the panel. It is **authorization-aware
and fails closed** — `pageIsAccessible()`/`resourceIsAccessible()`/`resourceCanCreate()`
gate on Filament `canAccess()`/`canCreate()` and exclude on any thrown exception.
`Support/CommandRegistry` merges discovery with `config('filament-palette.custom_commands')`,
dedupes by URL, and applies `max_results`. `Support/CommandItem` is the value
object (`make()` + fluent `group/icon/description/keywords/openInNewTab`).

**All search/ranking/highlighting/recent-commands logic is client-side** in the
Alpine component inside `resources/views/livewire/command-palette.blade.php`:
fuzzy scoring across label/keywords/group/description, match highlighting,
recents in `localStorage` keyed per panel, keyboard nav, `x-trap.noscroll` for
focus trap + scroll lock.

## Front-end constraints (the source of most bugs here)

- **No build step.** Every line of CSS/JS ships inline in the Blade views. You
  cannot rely on the host app compiling the plugin's Tailwind. Use the standard
  Tailwind utilities Filament already compiles, plus scoped `<style>` blocks.
  Never invent `fi-*` utility classes (e.g. `fi-bg-gray-50` does not exist).
- **Theme colours use Filament v5 variables directly:** `var(--gray-500)`,
  `var(--primary-600)`, etc. (with hex fallbacks). Filament v5 stores these as
  full colours, so the v3/v4 channel syntax `rgb(var(--primary-600))` is invalid
  and silently falls back — do not use it. Dark mode picks lighter shades of the
  same fixed scale via `.dark` selectors (e.g. `var(--gray-900)` surfaces);
  neutral translucent overlays stay plain `rgba()`.
- **The Alpine state is fragile inside an HTML attribute.** On `main` it is a
  large inline `x-data="{ … }"`; a single raw `"` anywhere inside it (code OR
  comments) closes the attribute and breaks the whole component. Use
  `String.fromCharCode(34)` / `"` and single-quoted JS strings. This is
  guarded by `tests/Unit/CommandPaletteViewTest.php`. (An in-progress branch,
  `refactor/alpine-data-component`, moves the state into a registered
  `Alpine.data('commandPalette', …)` component via Livewire `@assets` to remove
  this whole class of bug.)

## Verifying changes (this environment can't run a browser)

- PHP logic: PHPUnit + PHPStan + Pint.
- Blade JS: extract the `<script>`/`x-data` and run `node --check` (or
  `new Function(...)`) — catches the `SyntaxError` class directly.
- Blade compiles: Laravel's `BladeCompiler::compileString()` works for the
  palette view, but **chokes on `<x-filament::icon>`** (topbar-trigger) and on
  Livewire's `@assets`/`@script` directives — those failures are harness
  limitations, not real errors.
- The Livewire/Alpine **runtime** (e.g. `$wire.loadCommands()`, `@assets`
  injection, render-hook output) cannot be exercised here — flag UI/integration
  changes for a manual smoke test in a real panel.

## Releasing

SemVer against the public API above. Tag + GitHub release per version; keep
`CHANGELOG.md` (Keep a Changelog) in step. `.gitattributes` `export-ignore`s
tests/CI/screenshots from the dist. The earlier history was rewritten/squashed to
detach the fork — current `main` is a clean standalone repo.
