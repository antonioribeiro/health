<?php

namespace PragmaRX\Health;

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

    public function panel(Command $command)
    {
        $columns = ['Resource', 'State', 'Message'];

        $rows = $this->healthService->health()->map(function ($resource) {
            return [
                $resource['name'],
                $resource['health']['healthy']
                    ? '<info>healthy</info>'
                    : '<fg=red>failing</fg=red>',
                $this->normalizeMessage($resource['health']['message']),
            ];
        })->toArray();

        $command->table($columns, $rows);
    }

    public function check(Command $command)
    {
        $checker = $this->healthService->getSilentChecker();

        $errors = $checker()->where('is_global', false)->reduce(function ($carry, $item) {
            return $carry + ($item['health']['healthy'] ? 0 : 1);
        }, 0);

        if ($errors) {
            $command->error(
                "Application needs attention, $errors ".
                str_plural('resouce', $errors).' '.
                ($errors > 1 ? 'are' : 'is').
                ' currently failing.'
            );
        }

        $command->info('Check completed with no errors.');
    }
}
