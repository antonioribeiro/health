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

        if (
            $outdated->count() > $this->target->resource->should_count_at_most
        ) {
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
        if ($this->target->resource->json_result) {
            return collect(json_decode($output, true) ?? collect([]));
        }

        return $output;
    }

    /**
     * Get the ping binary.
     */
    protected function getBinary()
    {
        return $this->target->resource->binary;
    }

    /**
     * Execute the Composer command.
     *
     * @return \Illuminate\Support\Collection|mixed
     */
    protected function executeCommand()
    {
        $process = new SymfonyProcess(
            $this->getBinary(),
            $this->target->working_dir
        );

        $process->run();

        $output = $this->outputToCollection($process->getOutput());

        if ($output->count() == 0) {
            return $output;
        }

        if ($rootItem = $this->target->resource->root_item) {
            return collect($output[$rootItem]);
        }

        return $output;
    }
}
