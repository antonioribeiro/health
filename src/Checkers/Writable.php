<?php

namespace PragmaRX\Health\Checkers;

use Illuminate\Filesystem\Filesystem;

class Writable extends Base
{
    protected $filesystem;

    /**
     * Check resource.
     *
     * @return bool
     */
    public function check()
    {
        foreach ($this->resource['paths'] as $path) {
            if (! $this->getFilesystem()->isWritable($path)) {
                return $this->makeResult(false, sprintf($this->resource['error_message'], $path));
            }
        }

        return $this->makeHealthyResult();
    }

    public function getFilesystem()
    {
        if ($this->filesystem) {
            return $this->filesystem;
        }

        return $this->filesystem = app(Filesystem::class);
    }
}
