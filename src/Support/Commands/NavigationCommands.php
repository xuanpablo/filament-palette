<?php

namespace Xuanpablo\FilamentPalette\Support\Commands;

use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Xuanpablo\FilamentPalette\Support\CommandItem;

class NavigationCommands
{
    public static function get(?Panel $panel = null): Collection
    {
        $commands = collect();

        try {
            $panel = $panel ?? Filament::getCurrentOrDefaultPanel();

            if (! $panel) {
                return $commands;
            }

            $commands = $commands
                ->merge(static::getFromNavigation($panel))
                ->merge(static::getFromPages($panel))
                ->merge(static::getFromResources($panel));
        } catch (\Throwable $e) {
            report($e);
        }

        return $commands;
    }

    protected static function getFromNavigation($panel): Collection
    {
        $commands = collect();

        try {
            $navigation = $panel->getNavigation();

            foreach ($navigation as $group) {
                if ($group instanceof NavigationGroup) {
                    $groupLabel = $group->getLabel() ?? __('filament-palette::filament-palette.groups.navigation');
                    $items = $group->getItems();

                    if ($items instanceof \Traversable) {
                        $items = iterator_to_array($items);
                    }

                    foreach ($items as $item) {
                        if ($item instanceof NavigationItem && $item->isVisible()) {
                            static::flattenNavigationItem($item, $groupLabel, $commands);
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return $commands;
    }

    protected static function flattenNavigationItem(NavigationItem $item, string $groupLabel, Collection $commands): void
    {
        $url = $item->getUrl();
        $childItems = $item->getChildItems();

        if ($childItems instanceof \Traversable) {
            $childItems = iterator_to_array($childItems);
        }

        if (filled($url)) {
            $icon = $item->getIcon();
            $icon = ($icon instanceof \BackedEnum || is_string($icon)) ? $icon : Heroicon::OutlinedArrowTopRightOnSquare;

            $commands->push(CommandItem::make(
                label: $item->getLabel(),
                url: $url,
                group: $groupLabel,
                icon: $icon,
                openInNewTab: $item->shouldOpenUrlInNewTab(),
            ));
        }

        foreach ($childItems as $child) {
            if ($child instanceof NavigationItem && $child->isVisible() && filled($child->getUrl())) {
                $icon = $child->getIcon();
                $icon = ($icon instanceof \BackedEnum || is_string($icon)) ? $icon : Heroicon::OutlinedArrowTopRightOnSquare;

                $commands->push(CommandItem::make(
                    label: $child->getLabel(),
                    url: $child->getUrl(),
                    group: $groupLabel,
                    icon: $icon,
                    openInNewTab: $child->shouldOpenUrlInNewTab(),
                ));
            }
        }
    }

    protected static function getFromPages($panel): Collection
    {
        $commands = collect();

        try {
            $pages = $panel->getPages();

            foreach ($pages as $page) {
                if (! is_string($page) || ! is_subclass_of($page, Page::class)) {
                    continue;
                }

                if (static::isAuthPage($page)) {
                    continue;
                }

                if (! static::pageIsAccessible($page)) {
                    continue;
                }

                try {
                    $url = $page::getUrl();

                    if (blank($url)) {
                        continue;
                    }

                    $title = $page::getNavigationLabel();
                    $group = $page::getNavigationGroup();

                    if (blank($title)) {
                        $title = Str::headline(class_basename($page));
                    }

                    if ($title instanceof Htmlable) {
                        $title = $title->toHtml();
                    } else {
                        $title = (string) $title;
                    }

                    $groupValue = $group instanceof \UnitEnum
                        ? (string) ($group->value ?? $group->name)
                        : ((string) ($group ?? __('filament-palette::filament-palette.groups.pages')));

                    $navIcon = $page::getNavigationIcon();
                    $icon = ($navIcon instanceof \BackedEnum || is_string($navIcon)) ? $navIcon : Heroicon::OutlinedHome;

                    $commands->push(CommandItem::make(
                        label: strip_tags($title),
                        url: $url,
                        group: $groupValue,
                        icon: $icon,
                    ));
                } catch (\Throwable $e) {
                    continue;
                }
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return $commands;
    }

    protected static function isAuthPage(string $page): bool
    {
        return str_starts_with($page, 'Filament\\Auth\\');
    }

    /**
     * Whether the current user may access the given page class.
     * Fails closed: if the gate throws, the page is excluded.
     */
    public static function pageIsAccessible(string $page): bool
    {
        try {
            return ! method_exists($page, 'canAccess') || (bool) $page::canAccess();
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Whether the current user may access the given resource class.
     * Fails closed: if the gate throws, the resource is excluded.
     */
    public static function resourceIsAccessible(string $resource): bool
    {
        try {
            return ! method_exists($resource, 'canAccess') || (bool) $resource::canAccess();
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Whether the current user may create records for the given resource class.
     * Fails closed: if the gate throws, creation is hidden.
     */
    public static function resourceCanCreate(string $resource): bool
    {
        try {
            return ! method_exists($resource, 'canCreate') || (bool) $resource::canCreate();
        } catch (\Throwable $e) {
            return false;
        }
    }

    protected static function getFromResources($panel): Collection
    {
        $commands = collect();

        try {
            $resources = $panel->getResources();

            foreach ($resources as $resource) {
                if (! is_string($resource) || ! is_subclass_of($resource, Resource::class)) {
                    continue;
                }

                if (! static::resourceIsAccessible($resource)) {
                    continue;
                }

                $modelLabel = $resource::getModelLabel();
                $pluralLabel = $resource::getPluralModelLabel();

                if ($pluralLabel instanceof \Closure) {
                    $pluralLabel = $pluralLabel();
                }
                if (is_object($pluralLabel) && method_exists($pluralLabel, '__toString')) {
                    $pluralLabel = (string) $pluralLabel;
                }
                $pluralLabel = (string) $pluralLabel;

                if ($modelLabel instanceof \Closure) {
                    $modelLabel = $modelLabel();
                }
                if (is_object($modelLabel) && method_exists($modelLabel, '__toString')) {
                    $modelLabel = (string) $modelLabel;
                }
                $modelLabel = (string) $modelLabel;

                $resourcesGroup = __('filament-palette::filament-palette.groups.resources');

                try {
                    if ($resource::hasPage('index')) {
                        $listUrl = $resource::getUrl('index', shouldGuessMissingParameters: true);
                        $commands->push(CommandItem::make(
                            label: __('filament-palette::filament-palette.actions.list', ['label' => $pluralLabel]),
                            url: $listUrl,
                            group: $resourcesGroup,
                            icon: Heroicon::OutlinedListBullet,
                        ));
                    }

                    if ($resource::hasPage('create') && static::resourceCanCreate($resource)) {
                        $createUrl = $resource::getUrl('create', shouldGuessMissingParameters: true);
                        $commands->push(CommandItem::make(
                            label: __('filament-palette::filament-palette.actions.create', ['label' => $modelLabel]),
                            url: $createUrl,
                            group: $resourcesGroup,
                            icon: Heroicon::OutlinedPlus,
                        ));
                    }
                } catch (\Throwable $e) {
                    continue;
                }
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return $commands;
    }
}
