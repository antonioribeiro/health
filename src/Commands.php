<?php

namespace PragmaRX\Health;

use Illuminate\Console\Command;
use PragmaRX\Yaml\Package\Yaml;
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

    public function panel(Command $command = null)
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

        $this->table($command, $columns, $rows);
    }

    public function check(Command $command = null)
    {
        $checker = $this->healthService->getSilentChecker();

        $errors = $checker()->where('is_global', false)->reduce(function ($carry, $item) {
            return $carry + ($item['health']['healthy'] ? 0 : 1);
        }, 0);

        $exitCode = 0;

        if ($errors) {
            $this->error(
                $command,
                "Application needs attention, $errors " .
                str_plural('resouce', $errors) . ' ' .
                ($errors > 1 ? 'are' : 'is') .
                ' currently failing.'
            );

            $exitCode = 255;
        } else {
            $this->info($command, 'Check completed with no errors.');
        }

        exit($exitCode);
    }

    public function export(Command $command = null)
    {
        $yaml = new Yaml();

        collect(config('health.resources'))->each(function ($resource, $key) use ($command, $yaml) {
            $path = config('health.resources.path');

            $resource['column_size'] = (int) $resource['columnSize'];

            unset($resource['columnSize']);

            if (! file_exists($path)) {
                mkdir($path, 0660, true);
            }

            $dump = $yaml->dump($resource, 5);

            file_put_contents($file = $path.DIRECTORY_SEPARATOR.studly_case($key).'.yml', $dump);

            $this->info($command, 'Exported '.$file);
        });
    }

    public function publish(Command $command = null)
    {
        $yaml = new Yaml();

        $yaml->loadFromDirectory(package_resources_dir(), false)->each(function ($value, $key) use ($command, $yaml) {
            if (! file_exists($path = config('health.resources.path'))) {
                mkdir($path, 0755, true);
            }

            if (file_exists($file = $path.DIRECTORY_SEPARATOR.$key.'.yaml')) {
                $this->warn($command, 'Skipped: '.$file);

                return;
            }

            file_put_contents($file, $yaml->dump($value));

            $this->info($command, 'Saved: '.$file);
        });
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
