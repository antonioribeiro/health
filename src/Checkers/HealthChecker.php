<?php

namespace PragmaRX\Health\Checkers;

class HealthChecker extends BaseChecker
{
    /**
     * @return bool
     */
    public function check()
    {
        $healthy = $this->resources->reduce(function($carry, $item) {
            return isset($item['is_global']) && $item['is_global']
                    ? $carry
                    : $carry && $item['health']['healthy']
            ;
        }, true);

        return $this->makeResult(
            $healthy,
            $healthy ? '' : $this->resource['error_message']
        );
    }
}
