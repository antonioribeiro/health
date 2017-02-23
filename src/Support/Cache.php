<?php

namespace PragmaRX\Health\Support;

use Exception;
use Illuminate\Http\Request;
use Cache as IlluminateCache;

class Cache
{
    /**
     * Cache was flushed?
     *
     * @var
     */
    private $cacheFlushed;

    /**
     * Flush cache.
     */
    public function flush()
    {
        if ($this->needsToFlush()) {
            try {
                $this->forceFlush();
            } catch (Exception $exception) {
                // cache service may be down
            }
        }
    }

    /**
     * Force cache flush.
     */
    protected function forceFlush()
    {
        IlluminateCache::forget(config('health.cache.key'));

        $this->cacheFlushed = true;
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
    private function getCurrentRequest()
    {
        return instantiate(Request::class);
    }

    /**
     * Get cached resources.
     *
     * @param $checker
     */
    public function getCachedResources($checker)
    {
        $this->flush();

        try {
            if (($minutes = $this->getMinutes()) !== false) {
                return IlluminateCache::remember(config('health.cache.key'), $minutes, $checker);
            }
        } catch (Exception $exception) {
            // cache service may be down
        }

        return $checker();
    }

    /**
     * @return bool
     */
    protected function needsToFlush()
    {
        return ! $this->cacheFlushed &&
                $this->getMinutes() !== false &&
                $this->getCurrentRequest()->get('flush');
    }
}
