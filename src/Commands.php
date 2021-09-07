<?php

namespace PragmaRX\Health;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use PragmaRX\Health\Support\Result;
use PragmaRX\Health\Service as HealthService;

class Commands
{
    /**
     * List of exit code mappings to the numerical value following the NPRE standard
     *
     * More information: https://nagios-plugins.org/doc/guidelines.html#AEN78
     */
    const EXIT_CODES = [
        result::OK        => 0, // Healthy.
        result::WARNING   => 1,
        result::CRITICAL  => 2, // Not healthy.
        result::UNKNOWN   => 3,
    ];

    /**
     * @var Service
     */
    private $healthService;

    /**
     * Commands constructor.
     *
     * @param Service $healthService
     */
    public function __construct(HealthService $healthService)
    {
        $this->healthService = $healthService;
    }

    /**
     * @param $rows
     * @return \Illuminate\Support\Collection|\IlluminateAgnostic\Arr\Support\Collection|\IlluminateAgnostic\Collection\Support\Collection|\IlluminateAgnostic\Str\Support\Collection|\Tightenco\Collect\Support\Collection|\Vanilla\Support\Collection
     * @throws \Exception
     */
    protected function getTargetsFomResources($resources)
    {
        $targets = collect();

        foreach ($resources as $resource) {
            foreach ($resource->targets as $target) {
                $targets->push($target);
            }
        }

        return $targets;
    }

    private function normalizeMessage($message)
    {
        $message = str_replace("\n", '', $message);
        $message = str_replace("\r", '', $message);
        $message = str_replace("\t", ' ', $message);
        $message = str_replace('<br>', ' ', $message);
        $message = str_replace('  ', ' ', $message);

        $message = wordwrap($message, 60);

        return $message;
    }

    /**
     * Builds and displays the CLI Panel / table, with the check, state and any
     * additional information.
     *
     * @param  Command|null $command
     * @return int $exitCode based on the Result's state
     */
    public function panel(Command $command = null): int
    {
        $columns = ['Resource', 'State', 'Message'];

        $exitCode = self::EXIT_CODES[result::OK];

        $rows = $this->getTargetsFomResources($this->healthService->health())
            ->map(function ($target) use(&$exitCode) {
                // Handles exit codes based on the result's status.
                $thisStatus = $target->result->getStatus();
                $thisExitCode = self::EXIT_CODES[$thisStatus];
                // An exit code with a greater value should be preferred as the output.
                if ($thisExitCode > $exitCode) {
                    $exitCode = $thisExitCode;
                }

                return [
                    "{$target->resource->name} ({$target->display})",
                    $target->result->healthy
                        ? '<info>'.$target->result->getStatus().'</info>'
                        : '<fg=red>'.$target->result->getStatus().'</fg=red>',
                    $this->normalizeMessage($target->result->errorMessage),
                ];
            })
            ->toArray();

        $this->table($command, $columns, $rows);

        return $exitCode;
    }

    /**
     * Performs the health check, printing out a one line summary of application
     * health.
     *
     * @param  Command|null $command
     * @return int $exitCode based on the Result's state
     *
     * @throws \Exception
     */
    public function check(Command $command = null): int
    {
        $checker = $this->healthService->getSilentChecker();

        $exitCode = self::EXIT_CODES[result::OK];

        $errors = $this->getTargetsFomResources($checker()->filter(function ($resource) {
            return ! $resource->isGlobal;
        }))->reduce(function ($carry, $target) use(&$exitCode) {
            // Handles exit codes based on the result's status.
            $thisStatus = $target->result->getStatus();
            $thisExitCode = self::EXIT_CODES[$thisStatus];
            // An exit code with a greater value should be preferred as the output.
            if ($thisExitCode > $exitCode) {
                $exitCode = $thisExitCode;
            }

            return $carry + ($target->result->healthy ? 0 : 1);
        }, 0);

        if ($errors) {
            $this->error(
                $command,
                "Application needs attention, $errors ".
                    Str::plural('resouce', $errors).
                    ' '.
                    ($errors > 1 ? 'are' : 'is').
                    ' currently failing.'
            );
        } else {
            $this->info($command, 'Check completed with no errors.');
        }

        return $exitCode;
    }

    /**
     * Format input to textual table.
     *
     * @param Command|null $command
     * @param $columns
     * @param  \Illuminate\Contracts\Support\Arrayable|array $rows
     */
    private function table($command, $columns, $rows)
    {
        if ($command) {
            $command->table($columns, $rows);
        }
    }

    /**
     * Write a string as information output.
     *
     * @param Command|null $command
     * @param $string
     */
    private function info($command, $string)
    {
        if ($command) {
            $command->info($string);
        }
    }

    /**
     * Write a string as information output.
     *
     * @param Command|null $command
     * @param $string
     */
    private function error($command, $string)
    {
        if ($command) {
            $command->error($string);
        }
    }

    /**
     * Write a string as information output.
     *
     * @param Command|null $command
     * @param $string
     */
    private function warn($command, $string)
    {
        if ($command) {
            $command->warn($string);
        }
    }
}
