<?php

namespace PragmaRX\Health;

use PragmaRX\Health\Support\Cache;
use PragmaRX\Health\Support\ResourceChecker;

class Service
{
    /**
     * @var ResourceChecker
     */
    private $resourceChecker;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * Service constructor.
     *
     * @param ResourceChecker $resourceChecker
     * @param Cache $cache
     */
    public function __construct(ResourceChecker $resourceChecker, Cache $cache)
    {
        $this->resourceChecker = $resourceChecker;

        $this->cache = $cache;
    }

    /**
     * Check Resources.
     *
     * @param bool $force
     * @return \Illuminate\Support\Collection
     * @throws \Exception
     */
    public function checkResources($force = false)
    {
        return $this->resourceChecker->checkResources($force);
    }

    /**
     * Check one resource.
     *
     * @param $slug
     * @return array
     * @throws \Exception
     */
    public function checkResource($slug)
    {
        return $this->resourceChecker->checkResource($slug);
    }

    /**
     * Get services health.
     *
     * @return mixed
     * @throws \Exception
     */
    public function health()
    {
        return $this->checkResources();
    }

    /**
     * Get resources.
     *
     * @return mixed
     * @throws \Exception
     */
    public function getResources()
    {
        return $this->resourceChecker->getResources();
    }

    /**
     * Get one resource.
     *
     * @param $slug
     * @return mixed
     * @throws \Exception
     */
    private function getResource($slug)
    {
        return $this->resourceChecker->getResourceBySlug($slug);
    }

    /**
     * Get a silent checker and notifier closure.
     *
     * @return \Closure
     */
    public function getSilentChecker()
    {
        return function () {
            return $this->checkResources();
        };
    }

    /**
     * Check if server is healthy.
     *
     * @return mixed
     * @throws \Exception
     */
    public function isHealthy()
    {
        return $this->checkResources()->reduce(function ($carry, $item) {
            return $carry && $item->isHealthy();
        }, true);
    }

    /**
     * Make a string result of all resources.
     *
     * @param $string
     * @param $checkSystem
     * @return string
     */
    private function makeString($string, $checkSystem)
    {
        return (
            $string .
            ($checkSystem
                ? config('health.string.ok')
                : config('health.string.fail'))
        );
    }

    /**
     * Check and get a resource.
     *
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    public function resource($slug)
    {
        return $this->checkResource($slug);
    }

    /**
     * Set the action.
     *
     * @param $action
     */
    public function setAction($action)
    {
        $this->resourceChecker->setCurrentAction($action);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function string()
    {
        return collect($this->health())->reduce(function ($current, $resource) {
            return (
                $current .
                ($current ? config('health.string.glue') : '') .
                $this->makeString(
                    $resource->abbreviation,
                    $resource->isHealthy()
                )
            );
        });
    }
}
