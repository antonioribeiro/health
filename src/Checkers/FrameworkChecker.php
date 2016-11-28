<?php

namespace PragmaRX\Health\Checkers;

class FrameworkChecker extends BaseChecker
{
    /**
     * @param $resources
     * @return bool
     */
    public function check($resources)
    {
        return $this->makeHealthyResult();
    }
}
