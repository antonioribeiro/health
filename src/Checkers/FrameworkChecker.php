<?php

namespace PragmaRX\Health\Checkers;

class FrameworkChecker extends BaseChecker
{
    /**
     * @return bool
     */
    public function check()
    {
        return $this->makeHealthyResult();
    }
}
