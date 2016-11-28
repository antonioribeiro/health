<?php

namespace PragmaRX\Health\Checkers;

use Storage;

class CloudStorageChecker extends BaseChecker
{
    /**
     * @return bool
     */
    public function check()
    {
        try {
            Storage::disk($this->resource['driver'])
                ->put(
                    $this->resource['file'],
                    $this->resource['contents']
                );

            $contents = Storage::disk($this->resource['driver'])->get($this->resource['file']);

            Storage::disk($this->resource['driver'])->delete($this->resource['file']);

            if ($contents !== $this->resource['contents']) {
                return $this->makeResult(false, $this->resource['error_message']);
            }

            return $this->makeHealthyResult();
        } catch (\Exception $exception) {
            return $this->makeResultFromException($exception);
        }
    }
}
