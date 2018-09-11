<?php

namespace PragmaRX\Health\Checkers;

use PragmaRX\Health\Support\Result;

class DirectoryAndFilePresence extends Base
{
    /**
     * File exists constant.
     */
    const FILE_EXISTS = 0;

    /**
     * File does not exists constant.
     */
    const FILE_DOES_NOT_EXISTS = 1;

    /**
     * Directory exists constant.
     */
    const DIRECTORY_EXISTS = 2;

    /**
     * Directory does not exists constant.
     */
    const DIRECTORY_DOES_NOT_EXISTS = 3;

    /**
     * Checker.
     *
     * @return Result
     */
    public function check()
    {
        list($messages, $result) = $this->checkPresence();

        if ($result->count() == 0) {
            return $this->makeHealthyResult();
        }

        return $this->makeResult(
            false,
            $this->target->getErrorMessage().' - '.implode(' ', $messages)
        );
    }

    /**
     * Check file or dir presence.
     *
     * @return static
     */
    protected function checkPresence()
    {
        $messages = [];

        $result = collect($this->getFiles())
            ->map(function ($files, $type) use (&$messages) {
                $isGood = true;

                $files = collect($files);

                foreach ($files as $file) {
                    if (! is_null($file)) {
                        foreach ($this->getCheckers($type) as $checker) {
                            if (is_string($message = $checker($file))) {
                                $messages[] = $message;
                                $isGood = false;
                            }
                        }
                    }
                }

                return $isGood;
            })
            ->filter(function ($value) {
                return $value === false;
            });

        return [$messages, $result];
    }

    public function getFiles()
    {
        return [
            static::FILE_EXISTS => $this->target->fileExists,
            static::FILE_DOES_NOT_EXISTS => $this->target->fileDoNotExists,
            static::DIRECTORY_EXISTS => $this->target->directoryExists,
            static::DIRECTORY_DOES_NOT_EXISTS => $this->target->directoryDoNotExists,
        ];
    }

    /**
     * Build file exists checker.
     *
     * @return \Closure
     */
    public function buildFileExistsChecker()
    {
        return function ($file) {
            return $this->fileExists($file);
        };
    }

    /**
     * Build file does not exists checker.
     *
     * @return \Closure
     */
    public function buildFileDoesNotExistsChecker()
    {
        return function ($file) {
            return $this->fileDoesNotExists($file);
        };
    }

    /**
     * Build is directory checker.
     *
     * @return \Closure
     */
    public function buildIsDirectoryChecker()
    {
        return function ($file) {
            return $this->isDirectory($file);
        };
    }

    /**
     * Get checkers.
     *
     * @return array
     */
    public function getCheckers($checker)
    {
        switch ($checker) {
            case static::FILE_EXISTS:
                return [$this->buildFileExistsChecker()];
            case static::FILE_DOES_NOT_EXISTS:
                return [$this->buildFileDoesNotExistsChecker()];
            case static::DIRECTORY_EXISTS:
                return [
                    $this->buildFileExistsChecker(),
                    $this->buildIsDirectoryChecker(),
                ];
            case static::DIRECTORY_DOES_NOT_EXISTS:
                return [
                    $this->buildFileDoesNotExistsChecker(),
                    $this->buildIsDirectoryChecker(),
                ];
        }

        return [];
    }

    /**
     * Check if a file exists.
     *
     * @param $file
     * @return bool|string
     */
    public function fileExists($file)
    {
        if (file_exists($file)) {
            return true;
        }

        return sprintf('File "%s" does not exists.', $file);
    }

    /**
     * Check if a file does not exists.
     *
     * @param $file
     * @return bool|string
     */
    public function fileDoesNotExists($file)
    {
        if (! file_exists($file)) {
            return true;
        }

        return sprintf('File "%s" exists.', $file);
    }

    /**
     * Check if a path is a directory.
     *
     * @param $file
     * @return bool|string
     */
    public function isDirectory($file)
    {
        if (is_dir($file)) {
            return true;
        }

        return sprintf('"%s" is not a directory.', $file);
    }
}
