<?php

namespace PragmaRX\Health\Checkers;

class Framework extends Base
{
    /**
     * @return bool
     */
    public function check()
    {
        return $this->makeHealthyResult();
    }
}
