<?php

namespace PragmaRX\Health\Support;

use Symfony\Component\Yaml\Yaml as SymfonyYaml;

class Yaml
{
    /**
     * Load yaml files from directory.
     *
     * @param $directory
     * @param bool $parseYaml
     * @return static
     */
    public function loadYamlFromDir($directory, $parseYaml = true)
    {
        return collect(scandir($directory) ?: [])->reject(function ($item) {
            return is_dir($item) || ! ends_with($item, '.yml');
        })->mapWithKeys(function ($file) use ($directory, $parseYaml) {
            return [$file => $this->loadFile($directory, $file, $parseYaml)];
        });
    }

    /**
     * Parse a yaml file.
     *
     * @param $contents
     * @return mixed
     */
    private function parseFile($contents)
    {
        return SymfonyYaml::parse($this->replaceContents($contents));
    }

    /**
     * Replace contents.
     *
     * @param $contents
     * @return mixed
     */
    private function replaceContents($contents)
    {
        preg_match_all('/{{(.*)}}/', $contents, $matches);

        foreach ($matches[0] as $key => $match) {
            if (count($match)) {
                $contents = str_replace($matches[0][$key], $this->resolveVariable($matches[1][$key]), $contents);
            }
        }

        return $contents;
    }

    /**
     * Resolve variable.
     *
     * @param $key
     * @return string
     */
    private function resolveVariable($key)
    {
        $key = trim($key);

        if ($result = $this->executeFunction($key)) {
            return $result;
        }

        return config($key) ?: 'null';
    }

    /**
     * Execute function.
     *
     * @param $string
     * @return mixed
     */
    private function executeFunction($string)
    {
        preg_match_all('/(.*)\((.*)\)/', $string, $matches);

        if (count($matches) && count($matches[0])) {
            $function = $matches[1][0];

            return $function($matches[2][0]);
        }
    }

    /**
     * Load yaml file.
     *
     * @param $directory
     * @param $file
     * @param bool $parseYaml
     * @return mixed|string
     */
    private function loadFile($directory, $file, $parseYaml = true)
    {
        $file = file_get_contents($directory.DIRECTORY_SEPARATOR.$file);

        if ($parseYaml) {
            return $this->parseFile($file);
        }

        return $file;
    }

    /**
     * Dump array to yaml.
     *
     * @param $input
     * @param int $inline
     * @param int $indent
     * @param int $flags
     * @return string
     */
    public function dump($input, $inline = 5, $indent = 4, $flags = 0)
    {
        return SymfonyYaml::dump($input, $inline, $indent, $flags);
    }
}
