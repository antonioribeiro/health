<?php

namespace PragmaRX\Health\Console\Commands;

use Illuminate\Console\Command;
use PragmaRX\Health\Commands;

class HealthCheckCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'health:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check resources health and send error notifications.';

    /**
     * @return void
     */
    public function handle(Commands $commands): void
    {
        $commands->check($this);
    }
}
