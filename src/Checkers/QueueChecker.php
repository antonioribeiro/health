<?php

namespace PragmaRX\Health\Checkers;

use Queue;
use Illuminate\Queue\Worker;
use Illuminate\Queue\WorkerOptions;

class QueueChecker extends BaseChecker
{
    /**
     * Check resource.
     *
     * @return bool
     */
    public function check()
    {
        Queue::pushOn($this->resource['name'], app($this->resource['test_job']));

        $worker = app(Worker::class);

        $connection = $this->resource['connection'] ?: app('config')['queue.default'];

        $queue = app('config')->get(
            "queue.connections.{$connection}.queue", 'default'
        );

        $worker->setCache(app($this->resource['cache_instance'])->driver());

        $worker->runNextJob(
            $connection, $queue, $this->gatherWorkerOptions()
        );

        return $this->makeResult(true);
    }

    /**
     * Gather all of the queue worker options as a single object.
     *
     * @return \Illuminate\Queue\WorkerOptions
     */
    protected function gatherWorkerOptions()
    {
        return new WorkerOptions(
            false, false,
            false, false,
            false, false
        );
    }
}
