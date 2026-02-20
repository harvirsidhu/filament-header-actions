<?php

namespace Harvirsidhu\FilamentHeaderActions\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Harvirsidhu\FilamentHeaderActions\FilamentHeaderActions
 *
 * @method static \Harvirsidhu\FilamentHeaderActions\Actions\HeaderActionsComposer compose(array $actions)
 */
class FilamentHeaderActions extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Harvirsidhu\FilamentHeaderActions\FilamentHeaderActions::class;
    }
}
