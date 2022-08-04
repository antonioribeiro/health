<?php

namespace PragmaRX\Health\Support;

use DomainException;
use PragmaRX\Yaml\Package\Support\Resolver;
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
     * @param  Yaml  $yaml
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
     *
     * @throws \Exception
     */
    private function getEnabledResources($resources)
    {
        if (
            is_array($filters = $this->config())
        ) {
            return collect($resources)->filter(function ($resource, $name) use (
                $filters
            ) {
                foreach ($filters as $filter) {
                    if (preg_match("|^$filter$|", $name)) {
                        return true;
                    }
                }

                return false;
            });
        }

        throw new DomainException("Invalid value for config.");
    }

    /**
     * @return array
     */
    private function config()
    {
        $applicationType = config('health.application_type');

        $disabledResources = config("health.resources.disabled_by_type.{$applicationType}", []);

        $disabledByEnv = config('health.resources.disabled_by_environment.' . app()->environment(), []);

        if (!empty($disabledByEnv)) {
            $disabledResources = array_merge($disabledResources, $disabledByEnv);
        }

        $config = config('health.resources.enabled');

        if (empty($config)) {
            throw new DomainException("Invalid value for config('health.resources.enabled')");
        }

        $enabledResources = collect($config)->filter(function ($resource) use ($disabledResources) {
            return !in_array($resource, $disabledResources);
        });

        return $enabledResources->toArray();
    }

    /**
     * Resources getter.
     *
     * @return \Illuminate\Support\Collection
     *
     * @throws \Exception
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
     *
     * @throws \Exception
     */
    public function load()
    {
        if (! empty($this->resources)) {
            return $this->resources;
        }

        return $this->resources = $this->sanitizeResources(
            $this->getEnabledResources($this->loadResourceFiles())
        );
    }

    /**
     * Load resources in files.
     *
     * @return \Illuminate\Support\Collection
     */
    private function loadResourceFiles()
    {
        return $this->replaceExecutableCode(
            $this->loadResourcesFiles()
        )->mapWithKeys(function ($value, $key) {
            return [$this->removeExtension($key) => $value];
        });
    }

    /**
     * Replace executable code.
     *
     * @return \Illuminate\Support\Collection
     */
    private function replaceExecutableCode($files)
    {
        return $files->map(function ($item) {
            return collect($item)
                ->map(function ($value) {
                    return (new Resolver())->recursivelyFindAndReplaceExecutableCode(
                        $value
                    );
                })
                ->all();
        });
    }

    /**
     * Load Resources.
     *
     * @return \Illuminate\Support\Collection
     *
     * @throws \Exception
     */
    public function loadResources()
    {
        return $this->load();
    }

    /**
     * Load resource files.
     *
     * @return \Illuminate\Support\Collection
     */
    private function loadResourcesFiles()
    {
        $files = $this->yaml->loadFromDirectory(
            config('health.resources.path')
        );

        $files =
            $files->count() == 0
                ? $this->yaml->loadFromDirectory(package_resources_dir())
                : $files;

        return $files;
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
}
