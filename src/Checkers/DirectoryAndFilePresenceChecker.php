<?php

namespace PragmaRX\Health\Checkers;

use Storage;

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

        return $this->makeResult(false, $this->resource['error_message']. ' - ' . implode(' ', $messages));
    }

    /**
     * @return static
     */
    private function checkPresence()
    {
        $messages = [];

        $result = collect($this->resource['files'])->map(function ($checkType, $file) use (&$messages) {
            if ($checkType == static::FILE_EXISTS) {
                if (file_exists($file)) {
                    return true;
                }

                $messages[] = sprintf('File "%s" does not exists.', $file);

                return false;
            }

            if ($checkType == static::FILE_DOES_NOT_EXISTS) {
                if (! file_exists($file)) {
                    return true;
                }

                $messages[] = sprintf('File "%s" exists.', $file);

                return false;
            }

            if ($checkType == static::DIRECTORY_EXISTS) {
                if (file_exists($file) && is_dir($file)) {
                    return true;
                }

                if (! file_exists($file)) {
                    $messages[] = sprintf('Directory "%s" does not exists.', $file);
                }

                if (! is_dir($file)) {
                    $messages[] = sprintf('"%s" is not a directory.', $file);
                }

                return false;
            }

            if ($checkType == static::DIRECTORY_DOES_NOT_EXISTS) {
                if (! file_exists($file) && is_dir($file)) {
                    return true;
                }

                if (! file_exists($file)) {
                    $messages[] = sprintf('Directory "%s" exists.', $file);
                }

                if (! is_dir($file)) {
                    $messages[] = sprintf('"%s" is not a directory.', $file);
                }

                return false;
            }

            return true;
        })->filter(function ($value) {
            return $value === false;
        });

        return [$messages, $result];
    }
}
