<?php

namespace PragmaRX\Health\Checkers;

use Storage;
use PragmaRX\Health\Support\Result;

class CloudStorage extends Base
{
    /**
     * @return Result
     */
    public function check()
    {
        try {
            Storage::disk($this->target->driver)->put(
                $this->target->file,
                $this->target->contents
            );

            $contents = Storage::disk($this->target->driver)->get(
                $this->target->file
            );

            Storage::disk($this->target->driver)->delete($this->target->file);

            if ($contents !== $this->target->contents) {
                return $this->makeResult(
                    false,
                    $this->target->getErrorMessage()
                );
            }

            return $this->makeHealthyResult();
        } catch (\Exception $exception) {
            return $this->makeResultFromException($exception);
        }
    }
}
