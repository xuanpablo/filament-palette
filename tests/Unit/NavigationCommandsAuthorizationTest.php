<?php

namespace Xuanpablo\FilamentPalette\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Xuanpablo\FilamentPalette\Support\Commands\NavigationCommands;

class FpAccessibleStub
{
    public static function canAccess(): bool
    {
        return true;
    }

    public static function canCreate(): bool
    {
        return true;
    }
}

class FpInaccessibleStub
{
    public static function canAccess(): bool
    {
        return false;
    }

    public static function canCreate(): bool
    {
        return false;
    }
}

class FpThrowingStub
{
    public static function canAccess(): bool
    {
        throw new \RuntimeException('boom');
    }

    public static function canCreate(): bool
    {
        throw new \RuntimeException('boom');
    }
}

class FpUngatedStub {}

class NavigationCommandsAuthorizationTest extends TestCase
{
    public function test_accessible_page_is_included(): void
    {
        $this->assertTrue(NavigationCommands::pageIsAccessible(FpAccessibleStub::class));
    }

    public function test_inaccessible_page_is_excluded(): void
    {
        $this->assertFalse(NavigationCommands::pageIsAccessible(FpInaccessibleStub::class));
    }

    public function test_page_without_can_access_gate_is_included(): void
    {
        $this->assertTrue(NavigationCommands::pageIsAccessible(FpUngatedStub::class));
    }

    public function test_throwing_page_gate_fails_closed(): void
    {
        $this->assertFalse(NavigationCommands::pageIsAccessible(FpThrowingStub::class));
    }

    public function test_accessible_resource_is_included(): void
    {
        $this->assertTrue(NavigationCommands::resourceIsAccessible(FpAccessibleStub::class));
    }

    public function test_inaccessible_resource_is_excluded(): void
    {
        $this->assertFalse(NavigationCommands::resourceIsAccessible(FpInaccessibleStub::class));
    }

    public function test_throwing_resource_gate_fails_closed(): void
    {
        $this->assertFalse(NavigationCommands::resourceIsAccessible(FpThrowingStub::class));
    }

    public function test_resource_can_create_respects_the_gate(): void
    {
        $this->assertTrue(NavigationCommands::resourceCanCreate(FpAccessibleStub::class));
        $this->assertFalse(NavigationCommands::resourceCanCreate(FpInaccessibleStub::class));
        $this->assertTrue(NavigationCommands::resourceCanCreate(FpUngatedStub::class));
        $this->assertFalse(NavigationCommands::resourceCanCreate(FpThrowingStub::class));
    }
}
