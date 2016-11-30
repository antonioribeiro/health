<?php

namespace PragmaRX\Health\Support;

use Illuminate\Support\Str;

class ResourceLoader
{
    /**
     * Load application resources.
     *
     */
    public function loadResources()
    {
        $resources = collect(config('health.resources'))->map(function ($item, $key) {
            $item['slug'] = $key;

            $item['name'] = Str::studly($key);

            $item['is_global'] = (isset($item['is_global']) && $item['is_global']);

            return $item;
        });

        if ($sortBy = config('health.sort_by')) {
            $resources = $resources->sortBy(function ($item) use ($sortBy) {
                return $item['is_global']
                    ? 0
                    : $item[$sortBy];
            });
        }

        return $resources;
    }
}
