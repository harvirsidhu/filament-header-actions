<?php

namespace Harvirsidhu\FilamentHeaderActions\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Harvirsidhu\FilamentHeaderActions\HeaderActionsManager
 *
 * @method static \Harvirsidhu\FilamentHeaderActions\Actions\HeaderActionsComposer make(array $actions)
 */
class FilamentHeaderActions extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Harvirsidhu\FilamentHeaderActions\HeaderActionsManager::class;
    }
}
