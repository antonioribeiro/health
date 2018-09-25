<?php

namespace PragmaRX\Health\Support;

use Exception;
use Illuminate\Support\Collection;
use PragmaRX\Health\Support\Traits\HandleExceptions;

class ResourceChecker
{
    use HandleExceptions;

    /**
     * Unknown error.
     */
    const UNKNOWN_ERROR = 'Unknown error.';

    /**
     * The current action.
     *
     * @var
     */
    protected $currentAction = 'check';

    /**
     * All resources.
     *
     * @var
     */
    protected $resources;

    /**
     * Was checked?
     *
     * @var
     */
    protected $checked;

    /**
     * Services already notified of error.
     *
     * @var
     */
    protected $notified = [];

    /**
     * The cache service.
     *
     * @var Cache
     */
    protected $cache;

    /**
     * Resource loader.
     *
     * @var ResourceLoader
     */
    protected $resourceLoader;

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
     * @param bool $force
     * @return \Illuminate\Support\Collection
     * @throws Exception
     */
    public function checkResources($force = false)
    {
        if (! ($resources = $this->getCachedResources($force))->isEmpty()) {
            return $resources;
        }

        if (! $this->allResourcesAreGood()) {
            return $this->resources = collect();
        }

        $resources = $this->sortResources(
            $this->getNonGlobalResources()
                ->each(function ($resource) {
                    $this->checkResource($resource);
                })
                ->merge(
                    $this->getGlobalResources()->each(function ($resource) {
                        return $resource->checkGlobal($this->getResources());
                    })
                )
        );

        $this->checked = true;

        return $this->resources = $this->cache->cacheResources($resources);
    }

    /**
     * Check a resource.
     *
     * @param $resource
     * @return array
     * @throws Exception
     */
    public function checkResource($resource)
    {
        $resource =
            $resource instanceof Resource
                ? $resource
                : $this->getResourceBySlug($resource);

        $checked = $this->cache->remember($resource->slug, function () use (
            $resource
        ) {
            return $resource->check($this->getCurrentAction());
        });

        $resource->targets = $checked->targets;

        return $resource;
    }

    /**
     * Get cached resources.
     *
     * @param bool $force
     * @return \Illuminate\Support\Collection
     * @throws Exception
     */
    protected function getCachedResources($force = false)
    {
        if ($force) {
            return collect();
        }

        if ($this->checked) {
            return $this->getResources();
        }

        return $this->resources = $this->cache->getCachedResources();
    }

    /**
     * Get current action.
     *
     * @return mixed
     */
    public function getCurrentAction()
    {
        return $this->currentAction;
    }

    /**
     * Get all non global resources.
     *
     * @return bool
     * @throws Exception
     */
    protected function allResourcesAreGood()
    {
        return ! $this->getResources()
            ->reject(function ($resource) {
                return ! $resource instanceof Resource;
            })
            ->isEmpty();
    }

    /**
     * Get all non global resources.
     *
     * @return Collection
     * @throws Exception
     */
    protected function getNonGlobalResources()
    {
        return $this->getResources()->filter(function (Resource $resource) {
            return ! $resource->isGlobal;
        });
    }

    /**
     * Get all global resources.
     *
     * @return Collection
     * @throws Exception
     */
    protected function getGlobalResources()
    {
        return $this->getResources()->filter(function (Resource $resource) {
            return $resource->isGlobal;
        });
    }

    /**
     * Get a resource by slug.
     *
     * @param $slug
     * @return mixed
     * @throws Exception
     */
    public function getResourceBySlug($slug)
    {
        return $this->getResources()
            ->where('slug', $slug)
            ->first();
    }

    /**
     * Make the result array.
     *
     * @param $exception
     * @param $resourceChecker
     * @return array
     */
    protected function makeResult($exception, $resourceChecker)
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
     * Get all resources.
     *
     * @return \Illuminate\Support\Collection
     * @throws Exception
     */
    public function getResources()
    {
        return $this->sortResources($this->loadResources());
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
     *
     * @return \Illuminate\Support\Collection
     * @throws Exception
     */
    public function loadResources()
    {
        if (is_null($this->resources) || $this->resources->isEmpty()) {
            $this->resources = $this->resourceLoader->loadResources()->map(
                function ($resource) {
                    return $this->handleExceptions(function () use ($resource) {
                        return $this->makeResource($resource);
                    });
                }
            );
        }

        return $this->resources;
    }

    /**
     * Get one resource.
     *
     * @param resource|Collection $resource
     * @return resource
     * @throws Exception
     */
    public function makeResource($resource)
    {
        if ($resource instanceof Resource) {
            return $resource;
        }

        return Resource::factory($resource);
    }

    /**
     * Sort resources.
     *
     * @param $resources
     * @return \Illuminate\Support\Collection
     */
    protected function sortResources($resources)
    {
        if ($sortBy = config('health.sort_by')) {
            return $resources->sortBy(function ($resource) use ($sortBy) {
                return $this->handleExceptions(function () use (
                    $resource,
                    $sortBy
                ) {
                    return
                        ($resource->isGlobal ? 'a-' : 'z-').$resource->$sortBy;
                });
            });
        }

        return $resources;
    }
}
