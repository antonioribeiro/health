<?php

namespace PragmaRX\Health\Checkers;

use PragmaRX\Health\Support\Result;
use SensioLabs\Security\SecurityChecker as SensioLabsSecurityChecker;

class SecurityChecker extends Base
{
    /**
     * Check resource.
     *
     * @return Result
     */
    public function check()
    {
        $checker = new SensioLabsSecurityChecker();

        $result = $checker->check(base_path('composer.lock'));
        $alerts = json_decode((string) $result, true);
        if (0 === count($alerts)) {
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
