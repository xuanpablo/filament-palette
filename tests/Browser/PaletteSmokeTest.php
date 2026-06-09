<?php

/**
 * Browser smoke test for the command palette — the safety net for the class of
 * UI-runtime bugs that previously shipped undetected (x-data attribute
 * truncation, dark mode, Alpine wiring). It opens the palette on a real Filament
 * page and asserts there are no JavaScript console errors and the palette renders.
 *
 * Grouped 'browser' so it is EXCLUDED from the default `composer test` run and
 * only runs via `composer test:browser` (needs Playwright). UNVERIFIED in this
 * environment — expect to iterate against CI (panel boot, asset loading, the
 * exact pest-plugin-browser API) before it is green. See tests/Browser/TestCase.php.
 */

it('opens the palette without JavaScript errors', function () {
    $page = $this->visit('/admin');

    // Open via the keyboard event the palette listens for, then confirm it renders.
    $page->script("window.dispatchEvent(new CustomEvent('open-command-palette'))");

    $page
        ->assertSee('Type a command or search...')
        ->assertNoJavaScriptErrors()
        ->assertNoConsoleLogs();
})->group('browser');
