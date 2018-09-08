<?php

namespace PragmaRX\Health\Checkers;

class SecurityChecker extends  Base
{
    /**
     * Check resource.
     *
     * @return bool
     */
    public function check()
    {
        $isHealthy = ! $this->pingTimedout();

        $this->createPing();

        $this->dispatchEvent();

        return $this->makeResult($isHealthy, $this->resource['error_message']);
    }
}
