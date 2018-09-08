<?php

namespace PragmaRX\Health\Checkers;

use Carbon\Carbon;
use PragmaRX\Health\Events\HealthPing;

class Broadcasting extends  Base
{
    /**
     * Check resource.
     *
     * @return bool
     */
    public function check()
    {
        $isHealthy = ! $this->pingTimedout();

        $this->createPing();

        $this->dispatchEvent();

        return $this->makeResult($isHealthy, $this->resource['error_message']);
    }

    /**
     * Dispatch event.
     */
    private function dispatchEvent()
    {
        event(new HealthPing(
            $this->resource['channel'],
            route($this->resource['route_name'], [$this->resource['secret']]),
            $this->resource
        ));
    }

    /**
     * Create and persist ping.
     */
    protected function createPing()
    {
        $this->database->push($this->createPingRow());

        $this->persist();
    }

    /**
     * Create ping row array.
     *
     * @return array
     */
    private function createPingRow()
    {
        info('Laravel Health Panel - PING - secret: '.$this->resource['secret']);

        return [
            'pinged_at' => Carbon::now(),
            'ponged_at' => null,
            'secret' => $this->resource['secret'],
        ];
    }

    /**
     * Parse date.
     *
     * @param $date
     * @return static
     */
    private function parseDate($date)
    {
        return Carbon::parse($date['date'], $date['timezone']);
    }

    /**
     * Create and persist pong.
     *
     * @param $secret
     */
    public function pong($secret)
    {
        info('Laravel Health Panel - PONG - secret: '.$secret);

        $this->database = $this->database->map(function ($item) use ($secret) {
            if ($item['secret'] == $secret) {
                $item['ponged_at'] = Carbon::now();
            }

            return $item;
        });

        $this->persist();
    }

    /**
     * Check if a ping timed out.
     *
     * @return bool
     */
    private function pingTimedout()
    {
        $timedout = false;

        $this->database = $this->database->filter(function ($item) use (&$timedout) {
            if (! $item['ponged_at']) {
                if (Carbon::now()->diffInSeconds($this->parseDate($item['pinged_at'])) > $this->resource['timeout']) {
                    $timedout = true;

                    return false;
                }

                return true;
            }

            return false;
        });

        return $timedout;
    }
}
