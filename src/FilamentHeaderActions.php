<?php

namespace Harvirsidhu\FilamentHeaderActions;

use Harvirsidhu\FilamentHeaderActions\Actions\HeaderActionsComposer;

class FilamentHeaderActions
{
    /**
     * @param  array<mixed>  $actions
     */
    public function compose(array $actions): HeaderActionsComposer
    {
        return HeaderActionsComposer::make($actions);
    }
}
