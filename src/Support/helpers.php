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
     * Get the package root directory.
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
     * Get package resources directory.
     *
     * @return string
     */
    function package_resources_dir()
    {
        return
            package_dir().
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
        $regExp =
            // Optional root prefix.
            '%^(?<wrappers>(?:[[:print:]]{2,}://)*)'.
            '(?<root>(?:[[:alpha:]]:/|/)?)'.
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

if (! function_exists('bytes_to_human')) {
    /**
     * Convert bytes to human readable.
     *
     * @return string
     */
    function bytes_to_human($bytes)
    {
        $base = log($bytes) / log(1024);

        $suffix = ['', 'KB', 'MB', 'GB', 'TB'];

        $f_base = floor($base);

        return round(pow(1024, $base - floor($base)), 1).$suffix[$f_base];
    }
}

if (! function_exists('human_to_bytes')) {
    /**
     * Convert bytes to human readable.
     *
     * @return string
     */
    function human_to_bytes($str)
    {
        $str = trim($str);

        $num = (float) $str;

        if (strtoupper(substr($str, -1)) == 'B') {
            $str = substr($str, 0, -1);
        }

        switch (strtoupper(substr($str, -1))) {
            case 'P':
                $num *= 1024;
            case 'T':
                $num *= 1024;
            case 'G':
                $num *= 1024;
            case 'M':
                $num *= 1024;
            case 'K':
                $num *= 1024;
        }

        return $num;
    }
}

if (! function_exists('ip_address_from_hostname')) {
    function ip_address_from_hostname($host)
    {
        if (
            filter_var(
                $host,
                FILTER_VALIDATE_IP,
                FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
            )
        ) {
            return $host;
        }

        if (filter_var($host, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
            return gethostbyname($host);
        }

        return false;
    }
}
