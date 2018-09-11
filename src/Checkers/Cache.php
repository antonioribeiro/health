<?php

namespace PragmaRX\Health\Checkers;

use Cache as IlluminateCache;
use PragmaRX\Health\Support\Result;

class Cache extends Base
{
    /**
     * @return Result
     */
    public function check()
    {
        try {
            $checker = $this->getChecker();

            $value1 = $this->getCached();

            $value2 = $this->getCached();

            if ($value1 !== $value2 || $value2 !== $checker()) {
                return $this->makeResult(
                    false,
                    $this->target->getErrorMessage()
                );
            }

            return $this->makeHealthyResult();
        } catch (\Exception $exception) {
            report($exception);

            return $this->makeResultFromException($exception);
        }
    }

    private function getCached()
    {
        $checker = $this->getChecker();

        return IlluminateCache::remember(
            $this->target->key,
            $this->target->minutes,
            $checker
        );
    }

    private function getChecker()
    {
        return function () {
            return 'DUMMY DATA';
        };
    }
}
