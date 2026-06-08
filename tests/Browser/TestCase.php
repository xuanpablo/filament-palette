<?php

namespace Xuanpablo\CommandPalette\Tests\Browser;

use Filament\Actions\ActionsServiceProvider;
use Filament\FilamentServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\Infolists\InfolistsServiceProvider;
use Filament\Notifications\NotificationsServiceProvider;
use Filament\Schemas\SchemasServiceProvider;
use Filament\Support\SupportServiceProvider;
use Filament\Tables\TablesServiceProvider;
use Filament\Widgets\WidgetsServiceProvider;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Xuanpablo\CommandPalette\CommandPaletteServiceProvider;
use Xuanpablo\CommandPalette\Tests\Fixtures\AdminPanelProvider;

/**
 * Boots a Testbench app with Livewire, Filament, and a Filament panel that
 * registers this plugin, so the browser smoke test can open the palette on a
 * real page.
 *
 * NOTE (unverified): this provider list is best-effort and has not been run in
 * this environment. The first CI runs may need adjustment — e.g. adding
 * blade-icons / blade-heroicons providers, enabling Testbench package discovery,
 * or wiring auth — before the Filament panel boots cleanly. Track this on the
 * test/pest-browser branch until CI is green.
 */
abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            LivewireServiceProvider::class,
            SupportServiceProvider::class,
            ActionsServiceProvider::class,
            SchemasServiceProvider::class,
            FormsServiceProvider::class,
            TablesServiceProvider::class,
            NotificationsServiceProvider::class,
            InfolistsServiceProvider::class,
            WidgetsServiceProvider::class,
            FilamentServiceProvider::class,
            CommandPaletteServiceProvider::class,
            AdminPanelProvider::class,
        ];
    }
}
