<?php

namespace PragmaRX\Health\Support;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache as IlluminateCache;

class Cache
{
    /**
     * Check if the cache is enabled.
     *
     * @return bool
     */
    protected function cacheIsEnabled()
    {
        return $this->getMinutes() !== false;
    }

    /**
     * Flush cache.
     *
     * @param bool $force
     * @param null $key
     */
    public function flush($force = false, $key = null)
    {
        if ($force || $this->needsToFlush()) {
            try {
                $this->forceFlush($key);
            } catch (Exception $exception) {
                // cache service may be down
            }
        }
    }

    /**
     * Force cache flush.
     *
     * @param string|null $key
     */
    protected function forceFlush($key = null)
    {
        IlluminateCache::forget($key ?? config('health.cache.key'));
    }

    /**
     * Get cache minutes.
     *
     * @return mixed
     */
    public function getMinutes()
    {
        return config('health.cache.minutes');
    }

    /**
     * Get the request.
     *
     * @return \Illuminate\Foundation\Application|mixed
     */
    protected function getCurrentRequest()
    {
        return instantiate(Request::class);
    }

    /**
     * Get cached resources.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getCachedResources()
    {
        $this->flush();

        return $this->cacheIsEnabled()
            ? IlluminateCache::get(config('health.cache.key'), collect())
            : collect();
    }

    /**
     * Check if cache needs to be flushed.
     *
     * @return bool
     */
    protected function needsToFlush()
    {
        return
            $this->cacheIsEnabled() && $this->getCurrentRequest()->get('flush');
    }

    /**
     * Cache all resources.
     *
     * @param Collection $resources
     * @return Collection
     */
    public function cacheResources($resources)
    {
        if ($this->cacheIsEnabled()) {
            IlluminateCache::put(
                config('health.cache.key'),
                $resources,
                $this->getMinutes()
            );
        }

        return $resources;
    }

    /**
     * Get an item from the cache, or store the default value.
     *
     * @param  string $key
     * @param \Closure $callback
     * @return mixed
     */
    public function remember($key, \Closure $callback)
    {
        if (! $this->cacheIsEnabled()) {
            return $callback();
        }

        $key = config('health.cache.key').$key;

        $this->flush(false, $key);

        return IlluminateCache::remember($key, $this->getMinutes(), $callback);
    }
}
