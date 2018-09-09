<?php

namespace PragmaRX\Health\Checkers;

use PragmaRX\Health\Support\Result;

class Framework extends Base
{
    /**
     * @return Result
     */
    public function check()
    {
        return $this->makeHealthyResult();
    }
}
