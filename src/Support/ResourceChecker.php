<?php

namespace PragmaRX\Health\Support;

use Exception;
use Throwable;
use PragmaRX\Health\Events\RaiseHealthIssue;

class ResourceChecker
{
    /**
     * Unknown error.
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
     * @return \Illuminate\Support\Collection
     */
    public function checkResources($force = true)
    {
        if ($this->checked && ! $force) {
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

        list($resourceChecker, $result) = $this->tryToCheckResource($name, $resource);

        if ($result) {
            return $result;
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
        return (! $health['healthy'])
                && $this->notificationIsEnabled($resource)
                && ! isset($this->notified[$name]);
    }

    /**
     * Make the result array.
     *
     * @param $exception
     * @param $resourceChecker
     * @return array
     */
    private function makeResult($exception, $resourceChecker)
    {
        $message = $exception->getMessage()
                    ? $exception->getMessage()
                    : static::UNKNOWN_ERROR;

        if (! isset($resourceChecker)) {
            return [
                null,
                [
                    'healthy' => false,
                    'message' => $message,
                ],
            ];
        }

        $resourceChecker->makeResult(false, $message);

        return [$resourceChecker, null];
    }

    /**
     * Is notification enabled for this resource?
     *
     * @param $resource
     * @return bool
     */
    private function notificationIsEnabled($resource)
    {
        return $resource['notify']
                && config('health.notifications.enabled')
                && config('health.notifications.notify_on.'.$this->currentAction);
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
        return instantiate(
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
        if (is_null($this->resources)) {
            $this->loadResources();
        }

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
        if (is_null($this->resources)) {
            $this->resources = $this->resourceLoader->loadResources();
        }
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

    /**
     * @param $name
     * @param $resource
     * @return array
     */
    private function tryToCheckResource($name, $resource)
    {
        try {
            try {
                $resourceChecker = $this->getResourceCheckerInstance($name, $resource);

                $resourceChecker->check($resource, $this->getResources());

                return [$resourceChecker, null];
            } catch (Exception $exception) {
                return $this->makeResult($exception, $resourceChecker);
            }
        } catch (Throwable $error) {
            return $this->makeResult(
                $error,
                isset($resourceChecker) ? $resourceChecker : null
            );
        }
    }
}
