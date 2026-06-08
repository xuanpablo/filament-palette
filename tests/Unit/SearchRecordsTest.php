<?php

namespace Xuanpablo\CommandPalette\Tests\Unit;

use Xuanpablo\CommandPalette\Livewire\CommandPalette;
use Xuanpablo\CommandPalette\Tests\TestCase;

class SearchRecordsTest extends TestCase
{
    public function test_returns_empty_when_the_feature_is_disabled(): void
    {
        config()->set('filament-palette.include_global_search_results', false);

        $this->assertSame([], (new CommandPalette)->searchRecords('dashboard'));
    }

    public function test_returns_empty_for_a_query_shorter_than_two_characters(): void
    {
        config()->set('filament-palette.include_global_search_results', true);

        $this->assertSame([], (new CommandPalette)->searchRecords('a'));
        $this->assertSame([], (new CommandPalette)->searchRecords(' '));
    }

    public function test_returns_empty_and_fails_closed_when_no_provider_is_available(): void
    {
        config()->set('filament-palette.include_global_search_results', true);

        // No Filament panel is registered in the test app, so there is no global
        // search provider; the method must fail closed rather than throw.
        $this->assertSame([], (new CommandPalette)->searchRecords('dashboard'));
    }
}
