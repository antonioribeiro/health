<?php

namespace PragmaRX\Health\Checkers;

abstract class BaseChecker implements Contract
{
    /**
     * @var
     */
    protected $healthy;

    /**
     * @var
     */
    protected $message;

    /**
     * @var
     */
    protected $resource;

    /**
     * @var
     */
    protected $resources;

    /**
     * BaseChecker constructor.
     * @param $resource
     * @param $resources
     */
    public function __construct($resource, $resources)
    {
        $this->resource = $resource;

        $this->resources = $resources;
    }

    /**
     * @param bool $healthy
     * @param null $message
     * @return array
     */
    public function makeResult($healthy = true, $message = null)
    {
        $this->setHealthy($healthy);

        $this->setMessage($message);
    }

    /**
     * @return array
     */
    protected function makeHealthyResult()
    {
        return $this->makeResult();
    }

    /**
     * @param $exception
     * @return array
     */
    protected function makeResultFromException($exception)
    {
        return $this->makeResult(false, $exception->getMessage());
    }

    /**
     * @param $resources
     * @return mixed
     */
    public function healthy($resources)
    {
        return $this->healthy;
    }

    /**
     * @param $resources
     * @return mixed
     */
    public function message($resources)
    {
        return $this->message;
    }

    /**
     * @param $healthy
     */
    public function setHealthy($healthy)
    {
        $this->healthy = $healthy;
    }

    /**
     * @param $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return array
     */
    public function healthArray()
    {
        return [
            'healthy' => $this->healthy,

            'message' => $this->message,
        ];
    }
}
