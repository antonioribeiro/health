<?php

namespace PragmaRX\Health\Checkers;

class DirectoryAndFilePresenceChecker extends BaseChecker
{
    const FILE_EXISTS = 0;
    const FILE_DOES_NOT_EXISTS = 1;
    const DIRECTORY_EXISTS = 2;
    const DIRECTORY_DOES_NOT_EXISTS = 3;

    /**
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

    public function getCheckers()
    {
        return [
            static::FILE_EXISTS => [
                function ($file) {
                    return $this->fileExists($file);
                },
            ],

            static::FILE_DOES_NOT_EXISTS => [
                function ($file) {
                    return $this->fileDoesNotExists($file);
                },
            ],

            static::DIRECTORY_EXISTS => [
                function ($file) {
                    return $this->fileExists($file);
                },
                function ($file) {
                    return $this->isDirectory($file);
                },
            ],

            static::DIRECTORY_DOES_NOT_EXISTS => [
                function ($file) {
                    return $this->fileDoesNotExists($file);
                },
                function ($file) {
                    return $this->isDirectory($file);
                },
            ],
        ];
    }

    public function fileExists($file)
    {
        if (file_exists($file)) {
            return true;
        }

        return sprintf('File "%s" does not exists.', $file);
    }

    public function fileDoesNotExists($file)
    {
        if (! file_exists($file)) {
            return true;
        }

        return sprintf('File "%s" exists.', $file);
    }

    public function isDirectory($file)
    {
        if (is_dir($file)) {
            return true;
        }

        return sprintf('"%s" is not a directory.', $file);
    }
}
