<?php

namespace PragmaRX\Health\Support;

use DomainException;
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
     * @var \Illuminate\Support\Collection
     */
    private $resources;

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
     * Can load resources?
     *
     * @param $what
     * @param $type
     * @return bool
     */
    private function canLoadResources($what, $type)
    {
        return $this->shouldLoadAnyType($type) ||
               $this->isArrayLoader($what, $type) ||
               $this->isFileLoader($what, $type);
    }

    /**
     * Get enabled resources.
     *
     * @param $resources
     * @return \Illuminate\Support\Collection
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

        throw new DomainException("Invalid value for config('$configKey'')");
    }

    /**
     * Resources getter.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getResources()
    {
        $this->load();

        return $this->resources;
    }

    /**
     * Is it an array loader?
     *
     * @param $what
     * @param $type
     * @return bool
     */
    private function isArrayLoader($what, $type)
    {
        return $what == Constants::ARRAY_RESOURCE &&
                $type == Constants::RESOURCES_TYPE_ARRAY;
    }

    /**
     * Is it a file loader?
     *
     * @param $what
     * @param $type
     * @return bool
     */
    private function isFileLoader($what, $type)
    {
        return $what == Constants::FILES_RESOURCE &&
                $type == Constants::RESOURCES_TYPE_FILES;
    }

    /**
     * Load all resources.
     *
     * @return \Illuminate\Support\Collection
     */
    public function load()
    {
        if (! empty($this->resources)) {
            return $this->resources;
        }

        $type = config('health.resources_location.type');

        return $this->resources = $this->sanitizeResources(
            $this->getEnabledResources(
                $this->loadResourcesFrom(
                    Constants::FILES_RESOURCE,
                    $type,
                    $this->loadResourcesFrom(
                        Constants::ARRAY_RESOURCE,
                        $type, $this->resources
                    )
                )
            )
        );
    }

    /**
     * Load arrays and files?
     *
     * @param $what
     * @return boolean
     */
    private function shouldLoadAnyType($what)
    {
        return $what == Constants::RESOURCES_TYPE_BOTH;
    }

    /**
     * Load resources in array.
     *
     * @return \Illuminate\Support\Collection
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
     * @return \Illuminate\Support\Collection
     */
    private function loadFiles()
    {
        return $this->loadResourcesFiles()->mapWithKeys(function ($value, $key) {
            return [$this->removeExtension($key) => $value];
        });
    }

    /**
     * Load Resources.
     *
     * @return \Illuminate\Support\Collection
     */
    public function loadResources()
    {
        return $this->sortResources($this->makeResourcesCollection());
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    private function loadResourcesFiles()
    {
        $files = $this->yaml->loadYamlFromDir(config('health.resources_location.path'));

        $files = $files->count() == 0
            ? $this->yaml->loadYamlFromDir(package_resources_dir())
            : $files;

        return $files;
    }

    /**
     * Load resources for a particular type.
     *
     * @param $what
     * @param $resources
     * @return array
     */
    private function loadResourcesForType($what, $resources)
    {
        return $what == Constants::ARRAY_RESOURCE
            ? array_merge($resources, $this->loadArray()->toArray())
            : array_merge($resources, $this->loadFiles()->toArray());
    }

    /**
     * Load resources from array.
     *
     * @param $what
     * @param $type
     * @param $resources
     * @return array
     */
    private function loadResourcesFrom($what, $type, $resources)
    {
        return $this->canLoadResources($what, $type)
                ? $this->loadResourcesForType($what, $resources)
                : $resources;
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
     * @return string
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
     * @return \Illuminate\Support\Collection
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
     * @return \Illuminate\Support\Collection
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
