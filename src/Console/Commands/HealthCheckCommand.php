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
     *
     * @return int Exit code: 0 = success; 2 = failed
     *
     * @throws \Exception
     */
    public function handle(Commands $commands)
    {
        return $commands->check($this);
    }
}
