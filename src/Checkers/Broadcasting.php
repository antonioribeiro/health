<?php

namespace PragmaRX\Health\Checkers;

use Carbon\Carbon;
use PragmaRX\Health\Support\Result;
use PragmaRX\Health\Events\HealthPing;
use PragmaRX\Health\Support\Traits\Routing;
use PragmaRX\Health\Support\Traits\Database;

class Broadcasting extends Base
{
    use Routing, Database;

    protected function bootRouter()
    {
        $this->target->routes->each(function ($route, $name) {
            $this->registerRoute($route, $name);
        });
    }

    /**
     * Check resource.
     *
     * @return Result
     */
    public function check()
    {
        $this->loadDatabase();

        $this->bootRouter();

        $isHealthy = !$this->pingTimedout();

        $this->createPing();

        $this->dispatchEvent();

        return $this->makeResult($isHealthy, $this->target->getErrorMessage());
    }

    /**
     * Dispatch event.
     */
    protected function dispatchEvent()
    {
        event(
            new HealthPing(
                $this->target->channel,
                route($this->target->routeName, [$this->target->secret]),
                $this->target
            )
        );
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
    protected function createPingRow()
    {
        info('Laravel Health Panel - PING - secret: ' . $this->target->secret);

        return [
            'pinged_at' => Carbon::now(),
            'ponged_at' => null,
            'secret' => $this->target->secret,
        ];
    }

    /**
     * Parse date.
     *
     * @param $date
     * @return Carbon
     */
    protected function parseDate($date)
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
        info('Laravel Health Panel - PONG - secret: ' . $secret);

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
    protected function pingTimedout()
    {
        $timedout = false;

        $this->database = $this->database->filter(function ($item) use (
            &$timedout
        ) {
            if (!$item['ponged_at']) {
                if (
                    Carbon::now()->diffInSeconds(
                        $this->parseDate($item['pinged_at'])
                    ) > $this->target->timeout
                ) {
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
