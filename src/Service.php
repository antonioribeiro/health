<?php

namespace PragmaRX\Health;

use PragmaRX\Health\Support\Cache;
use PragmaRX\Health\Support\Result;
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
     * Make a string result of all resources.
     *
     * @param $string
     * @param string Result::status $resultStatus
     * @return string
     */
    private function makeString($string, $resultStatus)
    {
        // To preserve current ok/fail behaviour, it will override the result
        // status string with 'fail', when the status is critical, and if the
        // fail string was set.
        if (
            $resultStatus === Result::CRITICAL
            && is_null(config('health.string.'.strtolower($resultStatus)))
            && null !== config('health.string.fail')
        ) {
            $resultStatus = 'fail';
        }
        $resultStatusOutput = config('health.string.'.strtolower($resultStatus));

        // If not defined, it should use the default string for the status.
        return $string.($resultStatusOutput ?? $resultStatus);
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
    public function string(?string $filters = '')
    {
        // If filters are required, return "" for results that should not be included.
        if (!empty($filters)) {
            $filters = explode(',', strtolower($filters));
        }

        return collect($this->health())->reduce(function ($current, $resource) use($filters) {

            $resourceStatus = $resource->getStatus();

            if (!empty($filters) && !in_array(strtolower($resourceStatus), $filters)) {
                return $current;
            }

            return
                $current.
                ($current ? config('health.string.glue') : '').
                $this->makeString(
                    $resource->abbreviation,
                    $resourceStatus
                );
        });
    }
}
