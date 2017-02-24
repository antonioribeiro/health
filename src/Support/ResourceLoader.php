<?php

namespace PragmaRX\Health\Support;

use Illuminate\Support\Str;

class ResourceLoader
{
    /**
     * Yaml service.
     *
     * @var Yaml
     */
    protected $yaml;

    /**
     * ResourceLoader constructor.
     *
     * @param Yaml $yaml
     */
    public function __construct(Yaml $yaml)
    {
        $this->yaml = $yaml;
    }

    /**
     * Get enabled resources.
     *
     * @param $resources
     * @return \Illuminate\Support\Collection|static
     * @throws \Exception
     */
    private function getEnabledResources($resources)
    {
        if (is_array($keys = config($configKey = 'health.resources_enabled'))) {
            return collect($resources)->reject(function ($value, $key) use ($keys) {
                return ! in_array($key, $keys);
            });
        }

        if ($keys == Constants::RESOURCES_ENABLED_ALL) {
            return collect($resources);
        }

        throw new \Exception("Invalid value for config('$configKey'')");
    }

    /**
     * Load all resources.
     *
     * @return mixed
     */
    private function load()
    {
        $resources = [];

        $type = config('health.resources_location.type');

        return $this->sanitizeResources(
            $this->getEnabledResources(
                $this->loadResourcesFromFiles($type, $this->loadResourcesFromArray($type, $resources))
            )
        );
    }

    /**
     * Load resources in array.
     *
     * @return static
     */
    private function loadArray()
    {
        return collect(config('health.resources'))->mapWithKeys(function ($value, $key) {
            return [studly_case($key) => $value];
        });
    }

    /**
     * Load resources in files.
     *
     * @return mixed
     */
    private function loadFiles()
    {
        $local = $this->yaml->loadYamlFromDir(config('health.resources_location.path'));

        return $this->yaml->loadYamlFromDir(package_resources_dir())->reject(function ($item, $key) use ($local) {
            return $local->keys()->has($key);
        })->merge($local)->mapWithKeys(function ($value, $key) {
            return [$this->removeExtension($key) => $value];
        });
    }

    /**
     * Load Resources.
     *
     * Load application resources.
     */
    public function loadResources()
    {
        return $this->sortResources($this->makeResourcesCollection());
    }

    /**
     * Load resources from array.
     *
     * @param $type
     * @param $resources
     * @return array
     */
    private function loadResourcesFromArray($type, $resources)
    {
        if ($type == Constants::RESOURCES_TYPE_ARRAY || $type == Constants::RESOURCES_TYPE_BOTH) {
            $resources = array_merge($resources, $this->loadArray()->toArray());

            return $resources;
        }

        return $resources;
    }

    /**
     * Load resources from files.
     *
     * @param $type
     * @param $resources
     * @return array
     */
    private function loadResourcesFromFiles($type, $resources)
    {
        if ($type == Constants::RESOURCES_TYPE_FILES || $type == Constants::RESOURCES_TYPE_BOTH) {
            $resources = array_merge($resources, $this->loadFiles()->toArray());

            return $resources;
        }

        return $resources;
    }

    /**
     * Create a resources collection.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function makeResourcesCollection()
    {
        return collect($this->load())->map(function ($item, $key) {
            $item['slug'] = $key;

            $item['name'] = Str::studly($key);

            $item['is_global'] = (isset($item['is_global']) && $item['is_global']);

            return $item;
        });
    }

    /**
     * Remove extension from file name.
     *
     * @param $key
     * @return mixed
     */
    private function removeExtension($key)
    {
        return preg_replace('/\.[^.]+$/', '', $key);
    }

    /**
     * Sanitize resource key.
     *
     * @param $key
     * @return string
     */
    private function sanitizeKey($key)
    {
        if ($key == 'column_size') {
            return 'columnSize';
        }

        return $key;
    }

    /**
     * Sanitize resources.
     *
     * @param $resources
     * @return mixed
     */
    private function sanitizeResources($resources)
    {
        return $resources->map(function ($resource) {
            return collect($resource)->mapWithKeys(function ($value, $key) {
                return [$this->sanitizeKey($key) => $value];
            });
        });
    }

    /**
     * Sort resources.
     *
     * @param $resources
     * @return mixed
     */
    protected function sortResources($resources)
    {
        if ($sortBy = config('health.sort_by')) {
            $resources = $resources->sortBy(function ($item) use ($sortBy) {
                return $item['is_global']
                    ? 0 .$item[$sortBy]
                    : 1 .$item[$sortBy];
            });

            return $resources;
        }

        return $resources;
    }
}
