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

        $message = isset($item['action_message'])
                    ? $item['action_message']
                    : config('health.notifications.action_message');

        return sprintf(
            $message,
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
