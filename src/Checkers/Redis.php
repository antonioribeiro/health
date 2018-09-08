<?php

namespace PragmaRX\Health\Checkers;

use Redis as IlluminateRedis;

class Redis extends  Base
{
    /**
     * Check resource.
     *
     * @return bool
     */
    public function check()
    {
        IlluminateRedis::set($key = $this->resource['key'], $number = random_bytes(80));

        $result = IlluminateRedis::get($key);

        return $this->makeResult(
            $number == $result,
            $number !== $result ? $this->resource['key'] : ''
        );
    }
}
