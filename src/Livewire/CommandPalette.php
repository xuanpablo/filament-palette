<?php

namespace Xuanpablo\FilamentPalette\Livewire;

use Filament\Facades\Filament;
use Filament\Panel;
use Filament\Support\Enums\IconSize;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Xuanpablo\FilamentPalette\Support\CommandItem;
use Xuanpablo\FilamentPalette\Support\CommandRegistry;

class CommandPalette extends Component
{
    public ?string $panelId = null;

    public function mount(?string $panelId = null): void
    {
        $this->panelId = $panelId;
    }

    /**
     * Build the command list for the current panel. Called lazily from the
     * browser the first time the palette is opened, so the (potentially large)
     * command list and its server-rendered icons are not embedded on every
     * page load.
     *
     * @return array<int, array<string, mixed>>
     */
    public function loadCommands(): array
    {
        $commands = collect();
        try {
            $commands = app(CommandRegistry::class)->getAllCommands($this->resolvePanel());
        } catch (\Throwable $e) {
            report($e);
        }

        return $commands->map(function (CommandItem $item): array {
            $icon = $item->icon ?? Heroicon::OutlinedArrowTopRightOnSquare;
            $iconHtml = \Filament\Support\generate_icon_html($icon, size: IconSize::ExtraSmall);

            return [
                'id' => $item->id,
                'label' => $item->label,
                'url' => $item->url,
                'group' => $item->group,
                'iconHtml' => $iconHtml?->toHtml() ?? '',
                'openInNewTab' => $item->openInNewTab,
                'description' => $item->description,
                'keywords' => array_values($item->keywords),
            ];
        })->values()->all();
    }

    public function render(): View
    {
        return view('filament-palette::livewire.command-palette', [
            'maxResults' => config('filament-palette.max_results', 5),
            'placeholder' => config('filament-palette.placeholder') ?? __('filament-palette::filament-palette.placeholder'),
            'showFooter' => (bool) config('filament-palette.show_footer', true),
            'showRecent' => (bool) config('filament-palette.show_recent', true),
            'recentLimit' => (int) config('filament-palette.recent_limit', 5),
            'recentLabel' => __('filament-palette::filament-palette.recent'),
            'paletteKey' => $this->resolvePanel()?->getId() ?? 'default',
        ]);
    }

    protected function resolvePanel(): ?Panel
    {
        try {
            return $this->panelId
                ? Filament::getPanel($this->panelId, isStrict: false)
                : Filament::getCurrentOrDefaultPanel();
        } catch (\Throwable $e) {
            return null;
        }
    }
}
