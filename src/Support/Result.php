<?php

namespace PragmaRX\Health\Support;

class Result
{
    /**
     * States the result of the check could be in
     * Further reading: https://nagios-plugins.org/doc/guidelines.html#AEN78
     */
    const OK = 'OK';
    const WARNING = 'Warning';
    const CRITICAL = 'Critical';
    const UNKNOWN = 'Unknown';

    /**
     * Defaults the state of the result to unknown
     */
    protected $state = self::UNKNOWN;

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

        // Currently the status is inferred from the $healthy flag until full support is added.
        $this->status = $healthy ? self::OK : self::CRITICAL;
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
    public function setValueHuman(string $valueHuman)
    {
        $this->valueHuman = $valueHuman;

        return $this;
    }

    /**
     * Get the result's status of the check
     *
     * @return string one of the consts e.g. result::OK
     */
    public function getStatus(): string {
        return $this->status;
    }

}
