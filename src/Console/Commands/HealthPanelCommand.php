<?php

namespace PragmaRX\Health\Console\Commands;

use PragmaRX\Health\Commands;
use Illuminate\Console\Command;

class HealthPanelCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'health:panel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show all resources and their current health states.';

    /**
     * @return void
     */
    public function handle(Commands $commands): void
    {
        $commands->panel($this);
    }
}
