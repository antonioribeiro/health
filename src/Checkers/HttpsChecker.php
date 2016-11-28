<?php

namespace PragmaRX\Health\Checkers;

class HttpsChecker extends HttpChecker
{
    /**
     * @var bool
     */
    protected $secure = true;
}
