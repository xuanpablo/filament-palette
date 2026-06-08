<?php

namespace Xuanpablo\FilamentPalette\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Xuanpablo\FilamentPalette\FilamentPaletteServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            FilamentPaletteServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        config()->set('filament-palette.custom_commands', []);
        config()->set('filament-palette.max_results', 10);
    }
}
