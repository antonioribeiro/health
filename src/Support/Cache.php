<?php

namespace PragmaRX\Health\Support;

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
        if ($this->cacheFlushed) {
            return;
        }

        if ($this->getMinutes() !== false && $this->getCurrentRequest()->get('flush')) {
            IlluminateCache::forget(config('health.cache.key'));

            $this->cacheFlushed = true;
        }
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
        return app(Request::class);
    }

    /**
     * Get cached resources.
     *
     * @param $checker
     */
    public function getCachedResources($checker)
    {
        $this->flush();

        if (($minutes = $this->getMinutes()) !== false) {
            return IlluminateCache::remember(config('health.cache.key'), $minutes, $checker);
        }

        return $checker();
    }
}
