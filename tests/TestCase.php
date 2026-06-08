<?php

namespace Xuanpablo\CommandPalette\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Xuanpablo\CommandPalette\CommandPaletteServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            CommandPaletteServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        config()->set('filament-palette.custom_commands', []);
        config()->set('filament-palette.max_results', 10);
    }
}
