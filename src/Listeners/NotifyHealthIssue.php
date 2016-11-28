<?php

namespace PragmaRX\Health\Listeners;

use Notification;
use PragmaRX\Health\Events\RaiseHealthIssue;
use PragmaRX\Health\Notifications\HealthStatus;

class NotifyHealthIssue
{
    /**
     * @return static
     */
    private function getNotifiableUsers()
    {
        return collect(config('health.notifications.users.emails'))->map(function($item, $key) {
            $model = app(config('health.notifications.users.model'));

            $model->email = $item;

            return $model;
        });
    }

    /**
     * Handle the event.
     *
     * @param  RaiseHealthIssue  $event
     * @return void
     */
    public function handle(RaiseHealthIssue $event)
    {
        Notification::send(
            $this->getNotifiableUsers(),
            new HealthStatus($event->failure, $event->channel)
        );
    }
}
