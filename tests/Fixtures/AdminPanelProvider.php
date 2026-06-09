<?php

namespace Xuanpablo\CommandPalette\Tests\Fixtures;

use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Xuanpablo\CommandPalette\CommandPalettePlugin;

/**
 * Minimal Filament panel for the browser smoke test. Auth is left off so the
 * Dashboard is reachable without a user model/migration.
 */
class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->plugin(CommandPalettePlugin::make())
            ->pages([Dashboard::class]);
    }
}
