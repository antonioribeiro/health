<?php

namespace PragmaRX\Health\Events;

use Illuminate\Contracts\Queue\ShouldQueue;

class RaiseHealthIssue implements ShouldQueue
{
    /**
     * @var
     */
    public $failure;

    /**
     * @var
     */
    public $channel;

    /**
     * Create a new event instance.
     *
     */
    public function __construct($failure, $channel)
    {
        $this->failure = $failure;

        $this->channel = $channel;
    }
}
