<?php

namespace Xuanpablo\CommandPalette;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Xuanpablo\CommandPalette\Commands\CommandPalettePublishViewsCommand;

class CommandPaletteServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-palette')
            ->hasConfigFile()
            ->hasViews()
            ->hasTranslations()
            ->hasCommand(CommandPalettePublishViewsCommand::class);
    }
}
