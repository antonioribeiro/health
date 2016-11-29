<?php

namespace PragmaRX\Health\Checkers;

use Cache;

class CacheChecker extends BaseChecker
{
    /**
     * @return bool
     */
    public function check()
    {
        try {
            $checker = $this->getChecker();

            $value1 = $this->getCached();

            $value2 = $this->getCached();

            if ($value1 !== $value2 || $value2 !== $checker()) {
                return $this->makeResult(false, $this->resource['error_message']);
            }

            return $this->makeHealthyResult();
        } catch (\Exception $exception) {
            return $this->makeResultFromException($exception);
        }
    }

    private function getCached()
    {
        $checker = $this->getChecker();

        return Cache::remember(
            $this->resource['key'],
            $this->resource['minutes'],
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
