<?php

namespace PragmaRX\Health\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class HealthPing implements ShouldBroadcast
{
    use InteractsWithSockets;

    /**
     * @var
     */
    public $callbackUrl;

    /**
     * @var
     */
    private $channel;

    /**
     * @var
     */
    public $target;

    /**
     * Create a new event instance.
     */
    public function __construct($channel, $callbackUrl, $target)
    {
        $this->callbackUrl = $callbackUrl;

        $this->channel = $channel;

        $this->target = $target;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [$this->channel];
    }
}
