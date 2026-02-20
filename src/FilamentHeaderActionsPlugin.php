<?php

namespace Harvirsidhu\FilamentHeaderActions;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Harvirsidhu\FilamentHeaderActions\Actions\HeaderActionsComposer;

class FilamentHeaderActionsPlugin implements Plugin
{
    public function getId(): string
    {
        return 'filament-header-actions';
    }

    public function register(Panel $panel): void
    {
        //
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }

    /**
     * @param  array<mixed>  $actions
     */
    public function compose(array $actions): HeaderActionsComposer
    {
        return app(FilamentHeaderActions::class)->compose($actions);
    }
}
