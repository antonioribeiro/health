<?php

namespace PragmaRX\Health\Checkers;

class DirectoryAndFilePresenceChecker extends BaseChecker
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
     * @return bool
     */
    public function check()
    {
        list($messages, $result) = $this->checkPresence();

        if ($result->count() == 0) {
            return $this->makeHealthyResult();
        }

        return $this->makeResult(false, $this->resource['error_message'].' - '.implode(' ', $messages));
    }

    /**
     * Check file or dir presence.
     *
     * @return static
     */
    private function checkPresence()
    {
        $messages = [];

        $result = collect($this->resource['files'])->map(function ($checkType, $file) use (&$messages) {
            $isGood = true;

            foreach ($this->getCheckers()[$checkType] as $checker) {
                if (is_string($message = $checker($file))) {
                    $messages[] = $message;
                    $isGood = false;
                }
            }

            return $isGood;
        })->filter(function ($value) {
            return $value === false;
        });

        return [$messages, $result];
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
    public function getCheckers()
    {
        return [
            static::FILE_EXISTS => [$this->buildFileExistsChecker()],

            static::FILE_DOES_NOT_EXISTS => [$this->buildFileDoesNotExistsChecker()],

            static::DIRECTORY_EXISTS => [
                $this->buildFileExistsChecker(),
                $this->buildIsDirectoryChecker(),
            ],

            static::DIRECTORY_DOES_NOT_EXISTS => [
                $this->buildFileDoesNotExistsChecker(),
                $this->buildIsDirectoryChecker(),
            ],
        ];
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
