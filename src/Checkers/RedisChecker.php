<?php

namespace PragmaRX\Health\Checkers;

use Redis;

class UptimeChecker extends BaseChecker
{
    /**
     * Check resource.
     *
     * @return bool
     */
    public function check()
    {
        Redis::set($key = $this->resource['key'], $number = random_bytes(80));

        $result = Redis::get($key);

        return $this->makeResult(
            $number == $result,
            $number !== $result ? $this->resource['key'] : ''
        );
    }


}
