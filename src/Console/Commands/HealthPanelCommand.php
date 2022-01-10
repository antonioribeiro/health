<?php

namespace PragmaRX\Health\Console\Commands;

use Illuminate\Console\Command;
use PragmaRX\Health\Commands;

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
     * @return int Exit code: 0 = success; 2 = failed
     */
    public function handle(Commands $commands): int
    {
        $this->info('Checking resources and gathering information to build the panel...');

        return $commands->panel($this);
    }
}
