<?php

namespace PragmaRX\Health\Checkers;

use PragmaRX\Health\Support\Result;

class Database extends Base
{
    /**
     * @return Result
     */
    public function check()
    {
        try {
            collect($this->target->models)->each(function ($model) {
                instantiate($model)->first();
            });

            return $this->makeHealthyResult();
        } catch (\Exception $exception) {
            return $this->makeResultFromException($exception);
        }
    }
}
