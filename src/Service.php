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
     */
    public function checkResources()
    {
        return $this->resourceChecker->checkResources();
    }

    /**
     * Get services health.
     *
     * @return mixed
     */
    public function health()
    {
        $this->checkResources();

        return $this->getResources();
    }

    /**
     * Get resources.
     *
     * @return mixed
     */
    private function getResources()
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
            $this->cache->flush();

            return $this->checkResources();
        };
    }
}
