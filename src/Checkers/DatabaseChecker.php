<?php

namespace PragmaRX\Health\Checkers;

class DatabaseChecker extends BaseChecker
{
    /**
     * @return bool
     */
    public function check()
    {
        try {
            collect($this->resource['models'])->each(function ($model) {
                app($model)->first();
            });

            return $this->makeHealthyResult();
        } catch (\Exception $exception) {
            return $this->makeResultFromException($exception);
        }
    }
}
