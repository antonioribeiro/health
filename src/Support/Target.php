<?php

namespace PragmaRX\Health\Support;

use JsonSerializable;
use Ramsey\Uuid\Uuid;
use PragmaRX\Health\Support\Traits\ToArray;
use Illuminate\Database\Eloquent\Collection;
use PragmaRX\Health\Support\Traits\ImportProperties;

class Target implements JsonSerializable
{
    use ToArray, ImportProperties;

    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $display;

    /**
     * @var resource
     */
    public $resource;

    /**
     * @var Result
     */
    public $result;

    /**
     * @var Result
     */
    public $value;

    /**
     * @var Collection
     */
    public $checks;

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
    public static function factory($resource, $data, $name = null)
    {
        $instance = new static();

        $instance->id = (string) Uuid::uuid4();

        $instance->name = self::makeName($data, $name);

        $instance->display = $instance->name;

        $instance->resource = $resource;

        $instance->importProperties($data);

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
                    $this->resource->checker->setTarget($this)->checkTarget(
                        $this
                    )
                );
            } catch (\Exception $exception) {
                report($exception);

                $this->exceptionResult($exception);
            }
        } catch (\Throwable $error) {
            report($error);

            $this->exceptionResult($error);
        }

        $this->moveChecksBackToTarget();

        return $this;
    }

    /**
     * Display getter.
     *
     * @return string
     */
    public function getDisplay()
    {
        return $this->display;
    }

    /**
     * @return string
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
     * Make target name.
     *
     * @param $data
     * @param $name
     * @return string
     */
    protected static function makeName($data, $name)
    {
        return
            (
                $data['name'] ??
                    ($name !== 'default' ? $name : ($data['hostname'] ?? null))
            ) ?? 'default';
    }

    /**
     * Move checks to target object.
     */
    protected function moveChecksBackToTarget()
    {
        if (isset($this->result->checks)) {
            $this->checks = $this->result->checks;

            unset($this->result->checks);
        }
    }

    /**
     * Make a result.
     *
     * @param $result
     * @return Result
     */
    protected function result($result)
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
     * Display setter.
     *
     * @param string $display
     * @return Target
     */
    public function setDisplay(string $display): self
    {
        $this->display = $display;

        return $this;
    }

    /**
     * @param string $name
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

    /**
     * Prepare the resource for JSON serialization.
     *
     * @return string
     */
    public function jsonSerialize()
    {
        return json_decode($this->__toString(), true);
    }
}
