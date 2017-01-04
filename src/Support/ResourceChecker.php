<?php

namespace PragmaRX\Health\Support;

use PragmaRX\Health\Events\RaiseHealthIssue;

class ResourceChecker
{
    /**
     * Unknown error
     */
    const UNKNOWN_ERROR = 'Unknown error.';

    /**
     * The current action.
     *
     * @var
     */
    private $currentAction = 'check';

    /**
     * All resources.
     *
     * @var
     */
    private $resources;

    /**
     * Was checked?
     *
     * @var
     */
    private $checked;

    /**
     * Services already notified of error.
     *
     * @var
     */
    private $notified = [];

    /**
     * The cache service.
     *
     * @var Cache
     */
    private $cache;

    /**
     * Resource loader.
     *
     * @var ResourceLoader
     */
    private $resourceLoader;

    /**
     * ResourceChecker constructor.
     *
     * @param ResourceLoader $resourceLoader
     * @param Cache $cache
     */
    public function __construct(ResourceLoader $resourceLoader, Cache $cache)
    {
        $this->cache = $cache;

        $this->resourceLoader = $resourceLoader;
    }

    /**
     * Check all resources.
     *
     * @return array
     */
    public function checkResources()
    {
        if ($this->checked) {
            return $this->getResources();
        }

        $resourceChecker = $this->makeResourceChecker();

        $checker = function () use ($resourceChecker) {
            $resourceChecker(false);

            $resourceChecker(true);

            return $this->getResources();
        };

        $this->resources = $this->getCachedResources($checker);

        $this->checked = true;

        return $this->getResources();
    }

    /**
     * Check a resource.
     *
     * @param $name
     * @return array
     */
    public function checkResource($name)
    {
        $resource = $this->getResource($name);

        try {
            $resourceChecker = $this->getResourceCheckerInstance($name, $resource);

            $resourceChecker->check($resource, $this->getResources());
        } catch (\Exception $exception) {
            if (! isset($resourceChecker)) {
                return [
                    'healthy' => false,
                    'message' => $exception->getMessage()
                                    ? $exception->getMessage()
                                    : static::UNKNOWN_ERROR,
                ];
            }

            $resourceChecker->makeResult(false, static::UNKNOWN_ERROR);
        }

        $health = $resourceChecker->healthArray();

        if ($this->canNotify($name, $health, $resource)) {
            $resource['health'] = $health;

            $this->notify($resource);

            $this->notified[$name] = true;
        }

        return $health;
    }

    /**
     * Get cached resources.
     *
     * @param $checker
     */
    private function getCachedResources($checker)
    {
        return $this->cache->getCachedResources($checker);
    }

    /**
     * @return mixed
     */
    public function getCurrentAction()
    {
        return $this->currentAction;
    }

    /**
     * Make a resource checker.
     *
     * @return \Closure
     */
    private function makeResourceChecker()
    {
        $resourceChecker = function ($allowGlobal) {
            $this->resources = $this->getResources()->map(function ($item, $key) use ($allowGlobal) {
                if ($item['is_global'] == $allowGlobal) {
                    $item['health'] = $this->checkResource($key);
                }

                return $item;
            });
        };

        return $resourceChecker;
    }

    /**
     * Can we notify about errors on this resource?
     *
     * @param $name
     * @param $health
     * @param $resource
     * @return bool
     */
    private function canNotify($name, $health, $resource)
    {
        return ! $health['healthy'] &&
            $resource['notify'] &&
            ! isset($this->notified[$name]) &&
            config('health.notifications.enabled') &&
            config('health.notifications.notify_on.'.$this->currentAction);
    }

    /**
     * Send notifications.
     *
     * @param $resource
     * @return static
     */
    private function notify($resource)
    {
        return collect(config('health.notifications.channels'))->filter(function ($value, $channel) use ($resource) {
            try {
                event(new RaiseHealthIssue($resource, $channel));
            } catch (\Exception $exception) {
                // Notifications are broken, ignore it
            }
        });
    }

    /**
     * Get the checker instance.
     *
     * @param $name
     * @return \Illuminate\Foundation\Application|mixed
     */
    public function getResourceCheckerInstance($name, $resource)
    {
        return app(
            $this->getResource($name)['checker'],
            [$resource, $this->getResources()]
        );
    }

    /**
     * Get all resources.
     *
     * @return mixed
     */
    public function getResources()
    {
        return $this->resources;
    }

    /**
     * Set the current action.
     *
     * @param mixed $currentAction
     */
    public function setCurrentAction($currentAction)
    {
        $this->currentAction = $currentAction;
    }

    /**
     * Resources setter.
     *
     * @param mixed $resources
     */
    public function setResources($resources)
    {
        $this->resources = $resources;
    }

    /**
     * Load all resources.
     */
    public function loadResources()
    {
        $this->resources = $this->resourceLoader->loadResources();
    }

    /**
     * Get one resource.
     *
     * @param $name
     * @return mixed
     */
    public function getResource($name)
    {
        return $this->getResources()[$name];
    }
}
