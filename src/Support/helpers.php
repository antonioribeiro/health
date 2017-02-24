<?php

use PragmaRX\Health\Service;

if (! function_exists('instantiate')) {
    /**
     * Instantiate a class.
     *
     * @param $abstract
     * @param array $parameters
     * @return object
     */
    function instantiate($abstract, $parameters = [])
    {
        if (is_array($parameters) && count($parameters)) {
            $reflection = new ReflectionClass($abstract);

            return $reflection->newInstanceArgs((array) $parameters);
        }

        return app($abstract);
    }
}

if (! function_exists('package_dir')) {
    /**
     * Get package root dir.
     *
     * @return string
     */
    function package_dir()
    {
        $reflector = new ReflectionClass(Service::class);

        return dirname($reflector->getFileName());
    }
}

if (! function_exists('package_resources_dir')) {
    /**
     * Instantiate a class.
     * @return string
     */
    function package_resources_dir()
    {
        return package_dir().
            DIRECTORY_SEPARATOR.
            'config'.
            DIRECTORY_SEPARATOR.
            'resources';
    }
}

if (! function_exists('is_absolute_path')) {
    /**
     * Check if string is absolute path.
     *
     * @param $path
     * @return string
     */
    function is_absolute_path($path)
    {
                    // Optional wrapper(s).
        $regExp = '%^(?<wrappers>(?:[[:print:]]{2,}://)*)' .
                    // Optional root prefix.
                    '(?<root>(?:[[:alpha:]]:/|/)?)' .
                    // Actual path.
                    '(?<path>(?:[[:print:]]*))$%';

        $parts = [];

        preg_match($regExp, $path, $parts);

        if ('' !== $parts['root']) {
            return true;
        }

        return false;
    }
}
