<?php

namespace PragmaRX\Health\Checkers;

use PragmaRX\Health\Support\Result;
use Symfony\Component\Process\Process as SymfonyProcess;

class Composer extends Base
{
    /**
     * Check resource.
     *
     * @return Result
     */
    public function check()
    {
        $outdated = $this->executeCommand();

        if ($outdated->count() > $this->target->resource->shouldCountAtMost) {
            return $this->makeResult(
                false,
                sprintf($this->target->getErrorMessage(), $outdated->count())
            );
        }

        return $this->makeHealthyResult();
    }

    /**
     * Convert output to array.
     *
     * @param string $output
     * @return \Illuminate\Support\Collection|mixed
     */
    protected function outputToCollection(string $output)
    {
        if ($this->target->resource->jsonResult) {
            return collect(json_decode($output, true) ?? collect([]));
        }

        return $output;
    }

    /**
     * Get the ping binary.
     */
    protected function makeCommand()
    {
        return [
            sprintf(
                '%s %s',
                $this->target->resource->binary,
                $this->target->resource->command
            ),
        ];
    }

    /**
     * Execute the Composer command.
     *
     * @return \Illuminate\Support\Collection|mixed
     */
    protected function executeCommand()
    {
        $process = new SymfonyProcess(
            $this->makeCommand(),
            $this->target->workingDir
        );

        $process->run();

        $output = $this->outputToCollection($process->getOutput());

        if ($output->count() == 0) {
            return $output;
        }

        if ($rootItem = $this->target->resource->rootItem) {
            return collect($output[$rootItem]);
        }

        return $output;
    }
}
