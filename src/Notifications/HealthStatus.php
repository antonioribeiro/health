<?php

namespace PragmaRX\Health\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class HealthStatus extends Notification
{
    use Queueable;

    /**
     * @var
     */
    private $item;
    /**
     * @var
     */
    private $channel;

    /**
     * Create a new notification instance.
     *
     * @param $item
     */
    public function __construct($item, $channel)
    {
        $this->item = $item;

        $this->channel = $channel;
    }

    /**
     * @param $name
     * @return \Illuminate\Foundation\Application|mixed
     */
    private function getSenderInstance($name)
    {
        $name = substr($name, 2);

        return instantiate(
            config(
                'health.notifications.channels.'.strtolower($name).'.sender'
            )
        );
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array
     */
    public function via()
    {
        return [$this->channel];
    }

    /**
     * @param $name
     * @param $parameters
     * @return mixed
     */
    public function __call($name, $parameters)
    {
        $parameters[] = $this->item;

        return call_user_func_array(
            [$this->getSenderInstance($name), 'send'],
            $parameters
        );
    }
}
