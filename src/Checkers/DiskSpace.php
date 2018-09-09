<?php

namespace PragmaRX\Health\Checkers;

use PragmaRX\Health\Support\Result;

class DiskSpace extends Base
{
    /**
     * Check resource.
     *
     * @return Result
     */
    public function check()
    {
        $free = disk_free_space($this->target->path);

        if (!$this->isEnough($free, $this->target->minimum)) {
            return $this->makeResult(
                false,
                sprintf(
                    $this->target->message,
                    $this->target->path,
                    bytes_to_human($free),
                    $this->target->minimum
                )
            );
        }

        return $this->makeHealthyResult();
    }

    public function isEnough($free, $minimum)
    {
        return $free > human_to_bytes($minimum);
    }
}
