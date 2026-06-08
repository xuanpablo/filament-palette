<?php

namespace Xuanpablo\CommandPalette\Support;

use Illuminate\Contracts\Support\Arrayable;

class CommandItem implements Arrayable
{
    /**
     * @param  array<int, string>  $keywords
     * @param  array<string, mixed>  $eventData
     */
    public function __construct(
        public string $id,
        public string $label,
        public ?string $url = null,
        public string $group = 'Navigation',
        public string|\BackedEnum|null $icon = null,
        public bool $openInNewTab = false,
        public ?string $description = null,
        public array $keywords = [],
        public ?string $event = null,
        public array $eventData = [],
    ) {}

    public static function make(
        string $label,
        string $url,
        ?string $group = 'Navigation',
        string|\BackedEnum|null $icon = null,
        bool $openInNewTab = false,
    ): static {
        return new static(
            id: md5($label.$url),
            label: $label,
            url: $url,
            group: $group ?? 'Navigation',
            icon: $icon,
            openInNewTab: $openInNewTab,
        );
    }

    /**
     * Create a command that dispatches a browser event instead of navigating.
     * Listen with window.addEventListener('<event>', e => …) or, for Livewire,
     * a #[On('<event>')] handler. The payload arrives as the event detail.
     *
     * @param  array<string, mixed>  $data
     */
    public static function action(
        string $label,
        string $event,
        array $data = [],
        ?string $group = 'Actions',
        string|\BackedEnum|null $icon = null,
    ): static {
        return new static(
            id: md5($label.$event),
            label: $label,
            group: $group ?? 'Actions',
            icon: $icon,
            event: $event,
            eventData: $data,
        );
    }

    public function group(string $group): static
    {
        $this->group = $group;

        return $this;
    }

    public function icon(string|\BackedEnum|null $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function openInNewTab(bool $openInNewTab = true): static
    {
        $this->openInNewTab = $openInNewTab;

        return $this;
    }

    public function description(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @param  array<int, string>  $keywords
     */
    public function keywords(array $keywords): static
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function dispatch(string $event, array $data = []): static
    {
        $this->event = $event;
        $this->eventData = $data;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'url' => $this->url,
            'group' => $this->group,
            'icon' => $this->icon,
            'openInNewTab' => $this->openInNewTab,
            'description' => $this->description,
            'keywords' => $this->keywords,
            'event' => $this->event,
            'eventData' => $this->eventData,
        ];
    }
}
