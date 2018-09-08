<?php

namespace PragmaRX\Health\Checkers;

class Database extends  Base
{
    /**
     * @return bool
     */
    public function check()
    {
        try {
            collect($this->resource['models'])->each(function ($model) {
                instantiate($model)->first();
            });

            return $this->makeHealthyResult();
        } catch (\Exception $exception) {
            return $this->makeResultFromException($exception);
        }
    }
}
