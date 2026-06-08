<?php

namespace Xuanpablo\CommandPalette\Tests\Unit;

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
                .'Keep the state in the Alpine.data() component (in <script>), not inline.'
        );
    }

    /**
     * State must live in a registered Alpine.data component referenced from a
     * tiny x-data call — not as a large inline object in the attribute.
     */
    public function test_state_is_registered_as_an_alpine_data_component(): void
    {
        $blade = $this->blade();

        $this->assertStringContainsString("Alpine.data('commandPalette'", $blade, 'Alpine component is not registered.');
        $this->assertStringContainsString('x-data="commandPalette(', $blade, 'x-data does not reference the registered component.');
    }
}
