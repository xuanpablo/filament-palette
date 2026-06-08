<?php

namespace Xuanpablo\CommandPalette\Livewire;

use Filament\Facades\Filament;
use Filament\Panel;
use Filament\Support\Enums\IconSize;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Xuanpablo\CommandPalette\Support\CommandItem;
use Xuanpablo\CommandPalette\Support\CommandRegistry;

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
            $default = $item->event !== null
                ? Heroicon::OutlinedBolt
                : Heroicon::OutlinedArrowTopRightOnSquare;
            $icon = $item->icon ?? $default;
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
                'event' => $item->event,
                'eventData' => (object) $item->eventData,
            ];
        })->values()->all();
    }

    /**
     * Fetch matching records from the panel's global search provider. Called
     * lazily (debounced) from the browser as the user types. Returns [] when the
     * feature is disabled, the query is too short, or no provider exists.
     *
     * @return array<int, array<string, mixed>>
     */
    public function searchRecords(string $query): array
    {
        if (! (bool) config('filament-palette.include_global_search_results', false)) {
            return [];
        }

        $query = trim($query);

        if (mb_strlen($query) < 2) {
            return [];
        }

        try {
            $results = $this->resolvePanel()?->getGlobalSearchProvider()?->getResults($query);

            if ($results === null) {
                return [];
            }

            $iconHtml = \Filament\Support\generate_icon_html(Heroicon::OutlinedMagnifyingGlass, size: IconSize::ExtraSmall)?->toHtml() ?? '';
            $limit = (int) config('filament-palette.global_search_results_limit', 10);

            $out = [];

            foreach ($results->getCategories() as $category => $items) {
                $items = $items instanceof Arrayable ? $items->toArray() : $items;

                foreach ($items as $result) {
                    $title = $result->title instanceof Htmlable
                        ? strip_tags($result->title->toHtml())
                        : (string) $result->title;

                    $description = collect($result->details)
                        ->map(fn ($value): string => is_string($value) ? $value : '')
                        ->filter()
                        ->implode(' · ');

                    $out[] = [
                        'id' => 'gs:'.md5($category.$result->url.$title),
                        'label' => $title,
                        'url' => $result->url,
                        'group' => (string) $category,
                        'iconHtml' => $iconHtml,
                        'openInNewTab' => false,
                        'description' => $description !== '' ? $description : null,
                        'keywords' => [],
                        'event' => null,
                        'eventData' => (object) [],
                    ];

                    if (count($out) >= $limit) {
                        return $out;
                    }
                }
            }

            return $out;
        } catch (\Throwable $e) {
            report($e);

            return [];
        }
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
            'includeRecords' => (bool) config('filament-palette.include_global_search_results', false),
            'recordsDebounce' => (int) config('filament-palette.global_search_debounce_ms', 300),
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
