<?php

namespace PragmaRX\Health\Checkers;

class DiskSpace extends Base
{
    /**
     * Check resource.
     *
     * @return bool
     */
    public function check()
    {
        foreach ($this->resource['volumes'] as $volume) {
            $free = $this->getFreeSpace($volume);

            if (! $this->isEnouth($free, $volume['minimum'])) {
                return $this->makeResult(false, sprintf($volume['message'], $volume['path'], bytes_to_human($free), $volume['minimum']));
            }
        }

        return $this->makeHealthyResult();
    }

    public function getFreeSpace($volume)
    {
        return disk_free_space($volume['path']);
    }

    public function isEnouth($free, $minimum)
    {
        return $free > human_to_bytes($minimum);
    }
}
