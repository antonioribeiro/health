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
     * @param Commands $commands
     * @return int Exit code: 0 = success; 1 = failed
     */
    public function handle(Commands $commands)
    {
        if (false === $commands->check($this)) {
            return 1;
        }

        return 0;
    }
}
