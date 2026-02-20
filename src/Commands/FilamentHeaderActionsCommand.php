<?php

namespace Harvirsidhu\FilamentHeaderActions\Commands;

use Illuminate\Console\Command;

class FilamentHeaderActionsCommand extends Command
{
    public $signature = 'filament-header-actions';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
