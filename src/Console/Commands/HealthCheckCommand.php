<?php

namespace PragmaRX\Health\Console\Commands;

use PragmaRX\Health\Commands;
use Illuminate\Console\Command;

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
