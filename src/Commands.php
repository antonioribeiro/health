<?php

namespace PragmaRX\Health;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use PragmaRX\Health\Service as HealthService;

class Commands
{
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

    public function panel(Command $command = null)
    {
        $columns = ['Resource', 'State', 'Message'];

        $rows = $this->getTargetsFomResources($this->healthService->health())
            ->map(function ($target) {
                return [
                    "{$target->resource->name} ({$target->display})",
                    $target->result->healthy
                        ? '<info>healthy</info>'
                        : '<fg=red>failing</fg=red>',
                    $this->normalizeMessage($target->result->errorMessage),
                ];
            })
            ->toArray();

        $this->table($command, $columns, $rows);
    }

    public function check(Command $command = null)
    {
        $checker = $this->healthService->getSilentChecker();

        $errors = $this->getTargetsFomResources($checker()->filter(function ($resource) {
            return ! $resource->isGlobal;
        }))->reduce(function ($carry, $target) {
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
