<?php

namespace Xuanpablo\FilamentPalette;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Xuanpablo\FilamentPalette\Commands\CommandPalettePublishViewsCommand;

class FilamentPaletteServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-palette')
            ->hasConfigFile()
            ->hasViews()
            ->hasCommand(CommandPalettePublishViewsCommand::class);
    }
}
