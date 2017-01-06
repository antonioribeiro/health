<?php

namespace PragmaRX\Health\Notifications\Channels;

use Request;

abstract class BaseChannel implements Contract
{
    /**
     * @return mixed
     */
    protected function getActionTitle()
    {
        return config('health.notifications.action-title');
    }

    /**
     * @param $item
     * @return string
     */
    protected function getMessage($item)
    {
        $domain = Request::server('SERVER_NAME');

        return sprintf(
            config('health.notifications.action_message'),
            studly_case($item['name']),
            $domain ? " in {$domain}." : '.'
        );
    }

    /**
     * @return string
     */
    protected function getActionLink()
    {
        return route(config('health.routes.notification'));
    }
}
