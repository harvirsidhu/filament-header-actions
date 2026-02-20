<?php

namespace Harvirsidhu\FilamentHeaderActions\Support;

use Composer\InstalledVersions;

class FilamentCompatibility
{
    public static function defaultMoreIcon(): string
    {
        return match (static::filamentMajor()) {
            4, 5 => 'heroicon-m-ellipsis-horizontal',
            default => 'heroicon-m-ellipsis-horizontal',
        };
    }

    public static function supportsHiddenLabel(object $component): bool
    {
        return method_exists($component, 'hiddenLabel');
    }

    protected static function filamentMajor(): ?int
    {
        if (! class_exists(InstalledVersions::class)) {
            return null;
        }

        if (! InstalledVersions::isInstalled('filament/filament')) {
            return null;
        }

        $version = InstalledVersions::getPrettyVersion('filament/filament');

        if (! is_string($version) || $version === '') {
            return null;
        }

        if (! preg_match('/^\D*(\d+)/', $version, $matches)) {
            return null;
        }

        return (int) $matches[1];
    }
}
