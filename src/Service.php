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

        $this->loadResources();
    }

    /**
     * Check Resources.
     *
     */
    public function checkResources()
    {
        return $this->resourceChecker->checkResources();
    }

    /**
     * @return mixed
     */
    public function isHealthy()
    {
        $this->checkResources();

        return $this->getResources()->reduce(function ($carry, $item) {
            return $carry && $item['health'];
        }, true);
    }

    /**
     * @return mixed
     */
    public function health()
    {
        $this->checkResources();

        return $this->getResources();
    }

    /**
     * Check one resource.
     *
     * @param $name
     * @return array
     */
    public function checkResource($name)
    {
        return $this->resourceChecker->checkResource($name);
    }

    /**
     * Check and get a resource.
     *
     * @param $name
     * @return mixed
     */
    public function resource($name)
    {
        $this->checkResources();

        return $this->getResource($name);
    }

    /**
     * @param $action
     */
    public function setAction($action)
    {
        $this->resourceChecker->setCurrentAction($action);
    }

    /**
     * @return mixed
     */
    public function string()
    {
        $this->health();

        return collect($this->getResources())->reduce(function ($current, $item) {
            return $current.
                ($current ? config('health.string.glue') : '').
                $this->makeString($item['abbreviation'], $this->checkResource($item['slug']));
        });
    }

    /**
     * Get one resource.
     *
     * @param $name
     * @return mixed
     */
    private function getResource($name)
    {
        return $this->resourceChecker->getResources($name);
    }

    /**
     * @return mixed
     */
    private function getResources()
    {
        return $this->resourceChecker->getResources();
    }

    /**
     * Load all resources.
     *
     */
    public function loadResources()
    {
        $this->resourceChecker->loadResources();
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
        return $string.
                ($checkSystem
                    ? config('health.string.ok')
                    : config('health.string.fail')
                );
    }

    /**
     * Get results for panel.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function panel()
    {
        return $this->health();
    }

    /**
     * Get a silent checker and notifier closure.
     *
     * @return \Closure
     */
    public function getSilentChecker()
    {
        return function () {
            $this->cache->flush();

            return $this->checkResources();
        };
    }
}
