<?php

namespace PragmaRX\Health\Checkers;

use PragmaRX\Health\Support\Result;

class Health extends Base
{
    /**
     * Check health.
     *
     * @return Result
     */
    public function check()
    {
        $healthy = $this->isHealthy();

        return $this->makeResult(
            $healthy,
            $healthy ? '' : $this->target->getErrorMessage()
        );
    }

    /**
     * Compute health.
     *
     * @param $previous
     * @param $resource
     * @return bool
     */
    private function computeHealth($previous, $resource)
    {
        return $resource->isGlobal
            ? $previous
            : $previous && $this->computeHealthForAllTargets($resource);
    }

    /**
     * Compute health for targets.
     *
     * @param $resource
     * @return boolean
     */
    private function computeHealthForAllTargets($resource)
    {
        return $resource->targets->reduce(function ($carry, $target) {
            return $target->result->healthy;
        }, true);
    }

    /**
     * Check if the resource is healty.
     *
     * @return mixed
     */
    protected function isHealthy()
    {
        $healthy = $this->target->resource->resources->reduce(function (
            $carry,
            $item
        ) {
            return $this->computeHealth($carry, $item);
        },
        true);

        return $healthy;
    }
}
