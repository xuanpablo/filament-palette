<?php

namespace Xuanpablo\CommandPalette\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Xuanpablo\CommandPalette\Support\CommandItem;

class CommandItemTest extends TestCase
{
    public function test_make_creates_item_with_required_label_and_url(): void
    {
        $item = CommandItem::make('Dashboard', '/dashboard');

        $this->assertInstanceOf(CommandItem::class, $item);
        $this->assertSame('Dashboard', $item->label);
        $this->assertSame('/dashboard', $item->url);
        $this->assertSame('Navigation', $item->group);
        $this->assertFalse($item->openInNewTab);
        $this->assertSame(md5('Dashboard'.'/dashboard'), $item->id);
    }

    public function test_make_accepts_group_as_third_argument(): void
    {
        $item = CommandItem::make('Settings', '/settings', 'Custom');

        $this->assertSame('Custom', $item->group);
    }

    public function test_fluent_group_method(): void
    {
        $item = CommandItem::make('Test', '/test')->group('My Group');

        $this->assertSame('My Group', $item->group);
        $this->assertInstanceOf(CommandItem::class, $item);
    }

    public function test_fluent_icon_method(): void
    {
        $item = CommandItem::make('Test', '/test')->icon('heroicon-o-cog');

        $this->assertSame('heroicon-o-cog', $item->icon);
    }

    public function test_fluent_open_in_new_tab_method(): void
    {
        $item = CommandItem::make('Test', '/test')->openInNewTab(true);

        $this->assertTrue($item->openInNewTab);

        $item->openInNewTab(false);
        $this->assertFalse($item->openInNewTab);
    }

    public function test_fluent_methods_can_be_chained(): void
    {
        $item = CommandItem::make('My Action', '/action')
            ->group('Custom')
            ->icon('heroicon-o-bolt')
            ->openInNewTab(true);

        $this->assertSame('My Action', $item->label);
        $this->assertSame('/action', $item->url);
        $this->assertSame('Custom', $item->group);
        $this->assertSame('heroicon-o-bolt', $item->icon);
        $this->assertTrue($item->openInNewTab);
    }

    public function test_to_array_returns_all_properties(): void
    {
        $item = CommandItem::make('Dashboard', '/dashboard', 'Nav');
        $array = $item->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('label', $array);
        $this->assertArrayHasKey('url', $array);
        $this->assertArrayHasKey('group', $array);
        $this->assertArrayHasKey('icon', $array);
        $this->assertArrayHasKey('openInNewTab', $array);
        $this->assertSame('Dashboard', $array['label']);
        $this->assertSame('/dashboard', $array['url']);
        $this->assertSame('Nav', $array['group']);
    }

    public function test_description_defaults_to_null_and_keywords_to_empty_array(): void
    {
        $item = CommandItem::make('Dashboard', '/dashboard');

        $this->assertNull($item->description);
        $this->assertSame([], $item->keywords);
    }

    public function test_fluent_description_method(): void
    {
        $item = CommandItem::make('Billing', '/billing')->description('Manage invoices and payments');

        $this->assertSame('Manage invoices and payments', $item->description);
        $this->assertInstanceOf(CommandItem::class, $item);
    }

    public function test_fluent_keywords_method(): void
    {
        $item = CommandItem::make('Billing', '/billing')->keywords(['invoices', 'payments']);

        $this->assertSame(['invoices', 'payments'], $item->keywords);
        $this->assertInstanceOf(CommandItem::class, $item);
    }

    public function test_to_array_includes_description_and_keywords(): void
    {
        $item = CommandItem::make('Billing', '/billing')
            ->description('Manage invoices')
            ->keywords(['invoices', 'payments']);

        $array = $item->toArray();

        $this->assertArrayHasKey('description', $array);
        $this->assertArrayHasKey('keywords', $array);
        $this->assertSame('Manage invoices', $array['description']);
        $this->assertSame(['invoices', 'payments'], $array['keywords']);
    }

    public function test_description_and_keywords_chain_with_other_fluent_methods(): void
    {
        $item = CommandItem::make('Billing', '/billing')
            ->group('Finance')
            ->description('Manage invoices')
            ->keywords(['invoices'])
            ->openInNewTab();

        $this->assertSame('Finance', $item->group);
        $this->assertSame('Manage invoices', $item->description);
        $this->assertSame(['invoices'], $item->keywords);
        $this->assertTrue($item->openInNewTab);
    }

    public function test_same_label_and_url_produce_same_id(): void
    {
        $a = CommandItem::make('Dashboard', '/dashboard');
        $b = CommandItem::make('Dashboard', '/dashboard');

        $this->assertSame($a->id, $b->id);
    }

    public function test_different_label_or_url_produce_different_id(): void
    {
        $a = CommandItem::make('Dashboard', '/dashboard');
        $b = CommandItem::make('Settings', '/dashboard');
        $c = CommandItem::make('Dashboard', '/settings');

        $this->assertNotSame($a->id, $b->id);
        $this->assertNotSame($a->id, $c->id);
    }
}
