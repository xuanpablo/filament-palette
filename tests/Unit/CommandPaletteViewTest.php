<?php

namespace Xuanpablo\FilamentPalette\Tests\Unit;

use PHPUnit\Framework\TestCase;

class CommandPaletteViewTest extends TestCase
{
    private function blade(): string
    {
        return (string) file_get_contents(
            __DIR__.'/../../resources/views/livewire/command-palette.blade.php'
        );
    }

    /**
     * The Alpine state lives in a double-quoted x-data="{ ... }" attribute.
     * Any raw " inside it (in code OR comments) closes the attribute early,
     * truncating the object and breaking the whole component. The only "
     * allowed in that span is the single closing delimiter.
     */
    public function test_x_data_attribute_has_no_attribute_breaking_double_quote(): void
    {
        $blade = $this->blade();

        $start = strpos($blade, 'x-data="');
        $this->assertNotFalse($start, 'x-data attribute not found.');
        $start += strlen('x-data="');

        // The x-mousetrap attribute immediately follows x-data.
        $end = strpos($blade, 'x-mousetrap', $start);
        $this->assertNotFalse($end, 'x-mousetrap attribute not found after x-data.');

        $attribute = substr($blade, $start, $end - $start);

        $this->assertSame(
            1,
            substr_count($attribute, '"'),
            'A raw double-quote inside the x-data attribute truncates it and breaks Alpine. '
                .'Use String.fromCharCode(34) or \\u0022 instead, and avoid " in comments.'
        );
    }
}
