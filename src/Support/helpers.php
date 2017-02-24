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
        return package_dir() .
            DIRECTORY_SEPARATOR .
            'config' .
            DIRECTORY_SEPARATOR .
            'resources';
    }
}

if (! function_exists('is_absolute_path')) {
    /**
     * Check if string is absulute path.
     *
     * @return string
     */
    function is_absolute_path($path)
    {
        if (!is_string($path)) {
            $mess = sprintf('String expected but was given %s', gettype($path));
            throw new \InvalidArgumentException($mess);
        }

        if (!ctype_print($path)) {
            $mess = 'Path can NOT have non-printable characters or be empty';
            throw new \DomainException($mess);
        }

        // Optional wrapper(s).
        $regExp = '%^(?<wrappers>(?:[[:print:]]{2,}://)*)';

        // Optional root prefix.
        $regExp .= '(?<root>(?:[[:alpha:]]:/|/)?)';

        // Actual path.
        $regExp .= '(?<path>(?:[[:print:]]*))$%';

        $parts = [];

        if (!preg_match($regExp, $path, $parts)) {
            $mess = sprintf('Path is NOT valid, was given %s', $path);
            throw new \DomainException($mess);
        }

        if ('' !== $parts['root']) {
            return true;
        }

        return false;
    }
}
