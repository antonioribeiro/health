<?php

namespace PragmaRX\Health\Support;

use DomainException;
use Illuminate\Support\Str;
use PragmaRX\Yaml\Package\Yaml;

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
     * Get enabled resources.
     *
     * @param $resources
     * @return \Illuminate\Support\Collection
     * @throws \Exception
     */
    private function getEnabledResources($resources)
    {
        if (is_array($filters = config($configKey = 'health.resources.enabled'))) {
            return collect($resources)->filter(function ($resource, $name) use ($filters) {
                foreach ($filters as $filter) {
                    if (preg_match("|^$filter$|", $name)) {
                        return true;
                    }
                };

                return false;
            });
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
     * Load all resources.
     *
     * @return \Illuminate\Support\Collection
     */
    public function load()
    {
        if (! empty($this->resources)) {
            return $this->resources;
        }

        return $this->resources = $this->sanitizeResources(
            $this->getEnabledResources(
                $this->loadResourceFiles()
            )
        );
    }

    /**
     * Load resources in files.
     *
     * @return \Illuminate\Support\Collection
     */
    private function loadResourceFiles()
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
     * Load resource files.
     *
     * @return \Illuminate\Support\Collection
     */
    private function loadResourcesFiles()
    {
        $files = $this->yaml->loadFromDirectory(config('health.resources.path'));

        $files = $files->count() == 0
            ? $this->yaml->loadFromDirectory(package_resources_dir())
            : $files;

        return $files;
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

            $item['name'] = $item['name'] ?? Str::studly($key);

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
