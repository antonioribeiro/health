<?php

namespace PragmaRX\Health\Support;

class Result
{
    /**
     * @var bool
     */
    public $healthy;

    /**
     * @var string
     */
    public $errorMessage;

    public function __construct(bool $healthy = false, $errorMessage = null)
    {
        $this->healthy = $healthy;

        $this->errorMessage = $errorMessage;
    }
}
