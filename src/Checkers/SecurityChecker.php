<?php

namespace PragmaRX\Health\Checkers;

use PragmaRX\Health\Support\Result;
use SensioLabs\Security\SecurityChecker as BaseSecurityChecker;

class SecurityChecker extends Base
{
    /**
     * Check resource.
     *
     * @return Result
     */
    public function check()
    {
        $checker = new BaseSecurityChecker();

        $alerts = $checker->check(base_path('composer.lock'));

        if (count($alerts) == 0) {
            return $this->makeHealthyResult();
        }

        $problems = collect($alerts)
            ->keys()
            ->implode(', ');

        return $this->makeResult(
            false,
            sprintf($this->target->getErrorMessage(), $problems)
        );
    }
}
