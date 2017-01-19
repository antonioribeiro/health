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
        if ($parameters) {
            $reflection = new ReflectionClass($abstract);

            return $reflection->newInstanceArgs((array) $parameters);
        }

        return app($abstract);
    }
}
