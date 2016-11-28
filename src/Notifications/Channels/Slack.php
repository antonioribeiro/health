<?php

namespace PragmaRX\Health\Notifications\Channels;

use Illuminate\Notifications\Messages\SlackMessage;

class Slack extends BaseChannel
{
    /**
     * @param $notifiable
     * @param $item
     * @return $this
     */
    public function send($notifiable, $item)
    {
        return (new SlackMessage)
            ->error()
            ->from(
                config('health.notifications.from.name'),
                config('health.notifications.from.icon_emoji')
            )
            ->content($this->getMessage($item))
            ->attachment(function ($attachment) use ($item) {
                $attachment->title($this->getActionTitle(), $this->getActionLink())
                            ->content($item['health']['message']);
            })
        ;
    }
}
