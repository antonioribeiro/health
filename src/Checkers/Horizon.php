<?php

namespace PragmaRX\Health\Checkers;

use Laravel\Horizon\Contracts\MasterSupervisorRepository;

class Horizon extends Base
{
    /**
     * Check resource.
     *
     * @return \PragmaRX\Health\Support\Result
     */
    public function check()
    {
        return $this->horizonIsRunning()
            ? $this->makeHealthyResult()
            : $this->makeResult(false, $this->target->getErrorMessage());
    }

    /**
     * Check if Horizon is up.
     *
     * @return bool
     */
    protected function horizonIsRunning()
    {
        if (!$masters = app(MasterSupervisorRepository::class)->all()) {
            return false;
        }

        return collect($masters)->contains(function ($master) {
            return $master->status === 'paused';
        })
            ? false
            : true;
    }
}
