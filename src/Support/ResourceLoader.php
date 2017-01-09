<?php

namespace PragmaRX\Health\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ResourceLoader
{
    /**
     * Load application resources.
     */
    public function loadResources()
    {
        return $this->sortResources($this->makeResourcesCollection());
    }

    /**
     * Create a resources collection.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function makeResourcesCollection()
    {
        return collect(config('health.resources'))->map(function ($item, $key) {
            $item['slug'] = $key;

            $item['name'] = Str::studly($key);

            $item['is_global'] = (isset($item['is_global']) && $item['is_global']);

            return $item;
        });
    }

    /**
     * @param $resources
     * @return mixed
     */
    protected function sortResources($resources)
    {
        if ($sortBy = config('health.sort_by')) {
            $resources = $resources->sortBy(function ($item) use ($sortBy) {
                return $item['is_global']
                    ? 0
                    : $item[ $sortBy ];
            });

            return $resources;
        }

        return $resources;
    }
}
