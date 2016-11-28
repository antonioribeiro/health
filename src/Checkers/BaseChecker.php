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
     * @param bool $healthy
     * @param null $message
     * @return array
     */
    protected function makeResult($healthy = true, $message = null)
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

    public function setHealthy($healthy)
    {
        $this->healthy = $healthy;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function healthArray()
    {
        return [
            'healthy' => $this->healthy,

            'message' => $this->message,
        ];
    }
}
