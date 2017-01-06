<?php

namespace PragmaRX\Health\Checkers;

use Carbon\CarbonInterval;
use PragmaRX\Support\Exceptions\Exception;

class UptimeChecker extends BaseChecker
{
    /**
     * Check resource.
     *
     * @return bool
     */
    public function check()
    {
        $saved = $this->load();

        $current = $this->getCurrentUptime();

        $this->save($current);

        $rebooted = ($cs = $this->uptimeInSeconds($current)) < ($ss = $this->uptimeInSeconds($saved));

        return $this->makeResult(! $rebooted, $this->makeMessage($current, $saved));
    }

    protected function getCurrentUptime()
    {
        $error = exec($this->resource['command'], $system_string, $output);

        if ($output !== 0) {
            throw new Exception((string) $error);
        }

        $system_string = ! is_array($system_string) || ! $system_string ? '' : $system_string[0];

        $system_string = '0:54  up 0 days, 0:01, 1 users, load averages: 1.43 1.66 1.64';

        preg_match($this->resource['regex'], $system_string, $matches, PREG_OFFSET_CAPTURE);

        $matches = collect($matches)->filter(function ($item, $key) {
            return ! is_numeric($key);
        })->map(function ($item, $key) {
            $return = $item[0];

            if (starts_with($key, 'load')) {
                $return = floatval($return);
            } elseif (is_numeric($return)) {
                $return = (int) $return;
            } elseif (empty($return)) {
                $return = null;
            }

            return $return;
        });

        $matches['uptime_string'] = $system_string;

        return $matches;
    }

    /**
     * @return string
     */
    protected function getFileName(): string
    {
        return storage_path($this->resource['save_to']);
    }

    public function load()
    {
        if (! file_exists($file = $this->getFileName())) {
            return;
        }

        return collect(json_decode(file_get_contents($file)));
    }

    public function save($current)
    {
        json_encode(file_put_contents($this->getFileName(), $current), true);
    }

    protected function uptimeInSeconds($date)
    {
        return (isset($date['up_days']) ? $date['up_days'] * 24 * 60 : 0) +
                (isset($date['up_hours']) ? $date['up_hours'] * 60 : 0) +
                ($date['up_minutes'] ? $date['up_minutes'] : 0);
    }

    protected function makeMessage($current, $saved)
    {
        $current = $this->toUptimeString($current);

        $saved = $this->toUptimeString($saved);

        return sprintf($this->resource['error_message'], $current, $saved);
    }

    public function toUptimeString($uptime)
    {
        return (string) CarbonInterval::days(isset($uptime['up_days']) ? $uptime['up_days'] : 0)
                            ->hours(isset($uptime['up_hours']) ? $uptime['up_hours'] : 0)
                            ->minutes($uptime['up_minutes'] ? $uptime['up_minutes'] : 0);
    }
}
