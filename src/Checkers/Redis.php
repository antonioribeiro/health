<?php

namespace PragmaRX\Health\Checkers;

use PragmaRX\Health\Support\Result;
use Illuminate\Support\Facades\Redis as IlluminateRedis;

class Redis extends Base
{
    /**
     * Check resource.
     *
     * @return Result
     * @throws \Exception
     */
    public function check()
    {
        IlluminateRedis::set(
            $key = $this->target->key,
            $number = random_bytes(80)
        );

        $result = IlluminateRedis::get($key);

        return $this->makeResult(
            $number == $result,
            $number !== $result ? $this->target->key : ''
        );
    }
}
