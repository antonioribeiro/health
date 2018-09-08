<?php

namespace PragmaRX\Health\Checkers;

use SensioLabs\Security\SecurityChecker as SensioLabsSecurityChecker;

class SecurityChecker extends Base
{
    /**
     * Check resource.
     *
     * @return bool
     */
    public function check()
    {
        $checker = new SensioLabsSecurityChecker();

        $alerts = $checker->check(base_path('composer.lock'));

        if (count($alerts) == 0) {
            return $this->makeHealthyResult();
        }

        $problems = collect($alerts)->keys()->implode(', ');

        return $this->makeResult($isHealthy, sprintf($this->resource['error_message'], $problems));
    }
}
