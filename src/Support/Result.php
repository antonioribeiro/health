<?php

namespace PragmaRX\Health\Support;

class Result
{
    /**
     * @var bool
     */
    public $healthy;

    /**
     * @var float
     */
    public $elapsedTime;

    /**
     * @var mixed
     */
    public $value;

    /**
     * @var string
     */
    public $valueHuman;

    /**
     * @var string
     */
    public $errorMessage;

    public function __construct(bool $healthy = false, $errorMessage = null)
    {
        $this->healthy = $healthy;

        $this->errorMessage = $errorMessage;
    }

    /**
     * Value setter.
     *
     * @param mixed $value
     * @return Result
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Value for humans setter.
     *
     * @param string $valueHuman
     * @return Result
     */
    public function setValueHuman(string $valueHuman): self
    {
        $this->valueHuman = $valueHuman;

        return $this;
    }
}
