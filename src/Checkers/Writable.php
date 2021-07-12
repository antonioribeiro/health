<?php

namespace PragmaRX\Health\Checkers;

use Illuminate\Filesystem\Filesystem;
use PragmaRX\Health\Support\Result;

class Writable extends Base
{
    protected $filesystem;

    /**
     * Check resource.
     *
     * @return Result
     */
    public function check()
    {
        foreach ($this->target->paths as $path) {
            if (! $this->getFilesystem()->isWritable($path)) {
                return $this->makeResult(
                    false,
                    sprintf($this->target->getErrorMessage(), $path)
                );
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
