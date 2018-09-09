<?php

namespace PragmaRX\Health\Checkers;

use PragmaRX\Health\Support\Result;

class ServerLoad extends ServerUptime
{
    /**
     * Check resource.
     *
     * @return Result
     */
    public function check()
    {
        $current = $this->getCurrentUptime();

        $inTrouble =
            $current['load_1'] > $this->target->max_load['load_1'] ||
            $current['load_5'] > $this->target->max_load['load_5'] ||
            $current['load_15'] > $this->target->max_load['load_15'];

        return $this->makeResult(!$inTrouble, $this->makeMessage($current));
    }

    protected function makeMessage($current, $saved = null)
    {
        $current['load_1'] > $this->target->max_load['load_1'] ||
            $current['load_5'] > $this->target->max_load['load_5'] ||
            $current['load_15'] > $this->target->max_load['load_15'];

        return sprintf(
            $this->target->getErrorMessage(),
            $current['load_1'],
            $current['load_5'],
            $current['load_15'],
            $this->target->max_load['load_1'],
            $this->target->max_load['load_5'],
            $this->target->max_load['load_15']
        );
    }
}
