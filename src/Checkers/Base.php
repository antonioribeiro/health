<?php

namespace PragmaRX\Health\Checkers;

use Illuminate\Support\Collection;

abstract class Base implements Contract
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
     * @var Collection|void
     */
    protected $database;

    /**
     *  Base constructor.
     *
     * @param $resource
     * @param $resources
     */
    public function __construct($resource, $resources)
    {
        $this->resource = $resource;

        $this->resources = $resources;

        $this->database = $this->load();
    }

    /**
     * Create base directory for files.
     *
     * @param $fileName
     */
    private function makeDir($fileName)
    {
        $dir = dirname($fileName);

        if (! file_exists($dir)) {
            mkdir($dir, 0775, true);
        }
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

    /**
     * Load cache.
     *
     * @return \Illuminate\Support\Collection|void
     */
    public function load()
    {
        if (! file_exists($file = $this->getFileName())) {
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

        if (! is_array($data)) {
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
        if (! isset($this->resource['save_to'])) {
            return;
        }

        return storage_path($this->resource['save_to']);
    }
}
