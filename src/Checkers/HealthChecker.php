<?php

namespace PragmaRX\Health\Checkers;

class HealthChecker extends BaseChecker
{
    /**
     * Check health.
     *
     * @return bool
     */
    public function check()
    {
        $healthy = $this->isHealthy();

        return $this->makeResult(
            $healthy,
            $healthy ? '' : $this->resource['error_message']
        );
    }

    /**
     * Compute health.
     *
     * @param $previous
     * @param $current
     * @return bool
     */
    private function computeHealth($previous, $current)
    {
        return isset($current['is_global']) && $current['is_global']
            ? $previous
            : $previous && $current['health']['healthy'];
    }

    /**
     * Check if the resource is healty.
     *
     * @return mixed
     */
    protected function isHealthy()
    {
        $healthy = $this->resources->reduce(function ($carry, $item) {
            return $this->computeHealth($carry, $item);
        }, true);

        return $healthy;
    }
}
