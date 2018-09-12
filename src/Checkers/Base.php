<?php

namespace PragmaRX\Health\Checkers;

use SebastianBergmann\Timer\Timer;
use PragmaRX\Health\Support\Result;
use PragmaRX\Health\Support\Target;
use PragmaRX\Health\Support\Traits\Database;

abstract class Base implements Contract
{
    use Database;

    /**
     * @var Target
     */
    protected $target;

    /**
     * Check target and store elapsed time.
     */
    protected function checkAndStoreTime()
    {
        Timer::start();

        $result = $this->check();

        $result->elapsedTime = Timer::stop();

        return $result;
    }

    /**
     * Create base directory for files.
     *
     * @param $fileName
     */
    protected function makeDir($fileName)
    {
        $dir = dirname($fileName);

        if (!file_exists($dir)) {
            mkdir($dir, 0775, true);
        }
    }

    /**
     * Make a result.
     *
     * @param bool $healthy
     * @param null $errorMessage
     * @return Result
     */
    public function makeResult($healthy = true, $errorMessage = null)
    {
        return new Result($healthy, $errorMessage);
    }

    /**
     * Make a healthy result.
     *
     * @return Result
     */
    protected function makeHealthyResult()
    {
        return $this->makeResult();
    }

    /**
     * Make a result from an exception.
     *
     * @param $exception
     * @return Result
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
     * Save result to database.
     *
     * @param $result
     * @return
     */
    protected function saveToDatabase($result)
    {
        if ($this->databaseEnabled()) {
            return $this->saveResultsToDatabase($this->target, $result);
        }
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

    /**
     * Load cache.
     *
     * @return \Illuminate\Support\Collection
     */
    public function load()
    {
        if (!file_exists($file = $this->getFileName())) {
            return collect();
        }

        return collect(json_decode(file_get_contents($file), true));
    }

    /**
     * Persist to database cache file.
     *
     * @param $data
     */
    public function persist($data = null)
    {
        if (is_null($data)) {
            $data = $this->database->toArray();
        }

        if (!is_array($data)) {
            $data = $data->toArray();
        }

        $this->makeDir($this->getFileName());

        file_put_contents($this->getFileName(), json_encode($data));
    }

    /**
     * Get cache filename.
     *
     * @return string
     */
    protected function getFileName()
    {
        return $this->target->saveTo ?? '';
    }

    /**
     * Target setter.
     *
     * @param $target
     * @return $this
     */
    public function setTarget($target)
    {
        $this->target = $target;

        return $this;
    }

    public function checkTarget()
    {
        $result = $this->checkAndStoreTime();

        $result->checks = $this->saveToDatabase($result);

        return $result;
    }
}
