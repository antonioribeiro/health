<?php

namespace PragmaRX\Health\Checkers;

use PragmaRX\Health\Support\Result;

class Filesystem extends Base
{
    /**
     * @return Result
     */
    public function check()
    {
        try {
            $file = $this->temporaryFile(
                'health-check-',
                'just testing',
                storage_path()
            );

            if (!file_exists($file)) {
                return $this->makeResult(
                    false,
                    sprintf($this->target->getErrorMessage(), $file)
                );
            }

            unlink($file);

            return $this->makeHealthyResult();
        } catch (\Exception $exception) {
            return $this->makeResultFromException($exception);
        }
    }

    /**
     * @param $name
     * @param $content
     * @param null $folder
     * @return string
     */
    private function temporaryFile($name, $content, $folder = null)
    {
        $folder = $folder ?: sys_get_temp_dir();

        $file = tempnam($folder, $name);

        file_put_contents($file, $content);

        register_shutdown_function(function () use ($file) {
            if (file_exists($file)) {
                unlink($file);
            }
        });

        return $file;
    }
}
