<?php

namespace Xuanpablo\FilamentPalette;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\View\PanelsRenderHook;
use Xuanpablo\FilamentPalette\Filament\Pages\PublishCommandPaletteViewsPage;
use Xuanpablo\FilamentPalette\Livewire\CommandPalette;

class FilamentPalettePlugin implements Plugin
{
    public function getId(): string
    {
        return 'filament-palette';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->livewireComponents([
                CommandPalette::class,
            ])
            ->renderHook(PanelsRenderHook::BODY_END, function () use ($panel): string {
                return view('filament-palette::hooks.body-end', ['panel' => $panel])->render();
            });

        if (config('filament-palette.include_publish_views_command', true)) {
            $panel->pages([
                PublishCommandPaletteViewsPage::class,
            ]);
        }

        if (config('filament-palette.show_topbar_button', true)) {
            $panel->renderHook(PanelsRenderHook::GLOBAL_SEARCH_BEFORE, function (): string {
                return view('filament-palette::hooks.topbar-trigger')->render();
            });
        }
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return new static;
    }
}
