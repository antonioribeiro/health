<?php

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

if (! function_exists('package_resources_dir')) {
    /**
     * Instantiate a class.
     * @return string
     */
    function package_resources_dir()
    {
        return __DIR__.DIRECTORY_SEPARATOR.
            '..'.
            DIRECTORY_SEPARATOR.
            'config'.
            DIRECTORY_SEPARATOR.
            'resources';
    }
}
