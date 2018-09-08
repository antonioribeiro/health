<?php

namespace PragmaRX\Health\Checkers;

class ServerLoad extends ServerUptime
{
    /**
     * Check resource.
     *
     * @return bool
     */
    public function check()
    {
        $current = $this->getCurrentUptime();

        $inTrouble =
            $current['load_1'] > $this->resource['max_load']['load_1'] ||
            $current['load_5'] > $this->resource['max_load']['load_5'] ||
            $current['load_15'] > $this->resource['max_load']['load_15'];

        return $this->makeResult(! $inTrouble, $this->makeMessage($current));
    }

    protected function makeMessage($current, $saved = null)
    {
        $current['load_1'] > $this->resource['max_load']['load_1'] ||
        $current['load_5'] > $this->resource['max_load']['load_5'] ||
        $current['load_15'] > $this->resource['max_load']['load_15'];

        return sprintf(
            $this->resource['error_message'],
            $current['load_1'],
            $current['load_5'],
            $current['load_15'],
            $this->resource['max_load']['load_1'],
            $this->resource['max_load']['load_5'],
            $this->resource['max_load']['load_15']
        );
    }
}
