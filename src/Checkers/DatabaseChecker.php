<?php

namespace PragmaRX\Health\Checkers;

class DatabaseChecker extends BaseChecker
{
    /**
     * @param $resources
     * @return bool
     */
    public function check($resources)
    {
        try {
            collect(config('health.database.models'))->each(function($model) {
                app($model)->first();
            });

            return $this->makeHealthyResult();
        } catch (\Exception $exception) {
            return $this->makeResultFromException($exception);
        }
    }
}
