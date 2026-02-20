<?php

namespace Harvirsidhu\FilamentHeaderActions;

use Harvirsidhu\FilamentHeaderActions\Actions\HeaderActionsComposer;

class HeaderActionsManager
{
    /**
     * @param  array<mixed>  $actions
     */
    public function make(array $actions): HeaderActionsComposer
    {
        return HeaderActionsComposer::make($actions);
    }
}
