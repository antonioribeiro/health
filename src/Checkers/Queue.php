<?php

namespace PragmaRX\Health\Checkers;

use Illuminate\Queue\Worker;
use Illuminate\Queue\WorkerOptions;
use PragmaRX\Health\Support\Result;

class Queue extends Base
{
    /**
     * Check resource.
     *
     * @return Result
     */
    public function check()
    {
        app('queue')->pushOn(
            $this->target->name,
            instantiate($this->target->testJob)
        );

        $worker = app('queue.worker');

        $connection = $this->target->connection
            ?: app('config')['queue.default'];

        $queue = app('config')->get(
            "queue.connections.{$connection}.queue",
            'default'
        );

        $worker->setCache(instantiate($this->target->cacheInstance)->driver());

        $worker->runNextJob($connection, $queue, $this->gatherWorkerOptions());

        return $this->makeResult(true);
    }

    /**
     * Gather all of the queue worker options as a single object.
     *
     * @return \Illuminate\Queue\WorkerOptions
     */
    protected function gatherWorkerOptions()
    {
        return new WorkerOptions(0, 0, 0, 0, 0, false);
    }
}
