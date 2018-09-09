<?php

namespace PragmaRX\Health\Support;

use Illuminate\Support\Collection;
use PragmaRX\Health\Support\Traits\ToArray;
use PragmaRX\Health\Support\Traits\MagicData;

class Target
{
    use MagicData, ToArray;

    /**
     * @var String
     */
    public $name;

    /**
     * @var Collection
     */
    public $data;

    /**
     * @var Resource
     */
    public $resource;

    /**
     * @var Result
     */
    public $result;

    /**
     * @param \Exception|\Throwable
     */
    protected function exceptionResult($exception)
    {
        $this->result([
            'healthy' => false,
            'exception_message' => $exception->getMessage(),
        ]);
    }

    /**
     * Target factory.
     *
     * @param $resource
     * @param $data
     * @return Target
     */
    public static function factory($resource, $data)
    {
        $instance = new static();

        $instance->data = $data;

        $instance->name = $data['name'] ?? $data['hostname'] ?? 'default';

        $instance->resource = $resource;

        return $instance;
    }

    /**
     * Check a resource target.
     *
     * @return Target
     */
    public function check()
    {
        try {
            try {
                $this->result(
                    $this->resource->checker->setTarget($this)->check($this)
                );
            } catch (\Exception $exception) {
                $this->exceptionResult($exception);
            }
        } catch (\Throwable $error) {
            $this->exceptionResult($error);
        }

        return $this;
    }

    /**
     * @return String
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Result getter.
     *
     * @return Result
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Make a result.
     *
     * @param $result
     * @return Result
     */
    private function result($result)
    {
        $this->result =
            $result instanceof Result
                ? $result
                : new Result($result['healthy'], $result['exception_message']);

        return $this->result;
    }

    /**
     * Get result error message.
     *
     * @return mixed
     */
    public function getMessage()
    {
        return $this->result->healthy
            ? $this->getSuccessMessage()
            : $this->getErrorMessage();
    }

    /**
     * Get result error message.
     *
     * @return mixed
     */
    public function getErrorMessage()
    {
        return $this->result->errorMessage ?? $this->resource->errorMessage;
    }

    /**
     * Get result error message.
     *
     * @return mixed
     */
    public function getSuccessMessage()
    {
        return config('health.action.success.message');
    }

    /**
     * @param String $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Result setter.
     *
     * @param Result $result
     * @return Target
     */
    public function setResult(Result $result)
    {
        $this->result = $result;

        return $this;
    }

    /**
     * Object to json.
     *
     * @return false|string
     */
    public function __toString()
    {
        return json_encode($this->__toArray($this, 6));
    }
}
