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

        dd($alerts);

        return $this->makeResult($isHealthy, $this->resource['error_message']);
    }
}
