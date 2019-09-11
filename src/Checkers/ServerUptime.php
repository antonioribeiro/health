<?php

namespace PragmaRX\Health\Checkers;

use DomainException;
use Carbon\CarbonInterval;
use Illuminate\Support\Str;
use PragmaRX\Health\Support\Result;
use PragmaRX\Health\Support\Traits\Database;

class ServerUptime extends Base
{
    use Database;

    /**
     * Check resource.
     *
     * @return Result
     */
    public function check()
    {
        $this->loadDatabase();

        $current = $this->getCurrentUptime();

        $this->persist($current);

        $rebooted =
            ($cs = $this->uptimeInSeconds($current)) <
            ($ss = $this->uptimeInSeconds($this->database));

        return $this->makeResult(
            ! $rebooted,
            $this->makeMessage($current, $this->database)
        );
    }

    /**
     * Execute command to get an uptime string.
     *
     * @return mixed|string
     * @throws Exception
     */
    private function executeUptimeCommand()
    {
        $error = exec($this->target->command, $system_string, $output);

        if ($output !== 0) {
            throw new DomainException((string) $error);
        }

        return (! is_array($system_string) || empty($system_string))
            ? ''
            : $system_string[0];
    }

    /**
     * Get current uptime.
     *
     * @return array
     * @throws Exception
     */
    protected function getCurrentUptime()
    {
        return $this->parseUptimeString($this->executeUptimeCommand());
    }

    /**
     * Normalize uptime matches.
     *
     * @param $matches
     * @return \Illuminate\Support\Collection
     */
    protected function normalizeMatches($matches)
    {
        return collect($matches)
            ->filter(function ($item, $key) {
                return ! is_numeric($key);
            })
            ->map(function ($item, $key) {
                $return = $item[0];

                if (Str::startsWith($key, 'load')) {
                    $return = floatval($return);
                } elseif (is_numeric($return)) {
                    $return = (int) $return;
                } elseif (empty($return)) {
                    $return = null;
                }

                return $return;
            })
            ->toArray();
    }

    /**
     * Parse the uptime string.
     *
     * @param $system_string
     * @return array
     */
    protected function parseUptimeString($system_string)
    {
        $matches = [];

        preg_match(
            $this->target->regex,
            $system_string,
            $matches,
            PREG_OFFSET_CAPTURE
        );

        $matches = $this->normalizeMatches($matches);

        $matches['uptime_string'] = $system_string;

        return $matches;
    }

    /**
     * Convert uptime to seconds.
     *
     * @param $date
     * @return int
     */
    protected function uptimeInSeconds($date)
    {
        return
            (isset($date['up_days']) ? $date['up_days'] * 24 * 60 : 0) +
            (isset($date['up_hours']) ? $date['up_hours'] * 60 : 0) +
            (isset($date['up_minutes']) ? $date['up_minutes'] : 0);
    }

    /**
     * Make uptime message.
     *
     * @param $current
     * @param $saved
     * @return string
     */
    protected function makeMessage($current, $saved = null)
    {
        $current = $this->toUptimeString($current);

        $saved = $this->toUptimeString($saved);

        return sprintf($this->target->getErrorMessage(), $current, $saved);
    }

    /**
     * Convert uptime to human readable string.
     *
     * @param $uptime
     * @return string
     */
    public function toUptimeString($uptime)
    {
        return (string) CarbonInterval::days(
            isset($uptime['up_days']) ? $uptime['up_days'] : 0
        )
            ->hours(isset($uptime['up_hours']) ? $uptime['up_hours'] : 0)
            ->minutes(isset($uptime['up_minutes']) ? $uptime['up_minutes'] : 0);
    }
}
