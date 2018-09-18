<?php

namespace PragmaRX\Health\Notifications;

use Request;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;

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
     * Get sender instance.
     *
     * @param $name
     * @return \Illuminate\Foundation\Application|mixed
     */
    private function getSenderInstance($name)
    {
        $name = substr($name, 2);

        return instantiate(
            config(
                'health.notifications.channels.' . strtolower($name) . '.sender'
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
     * Magic getter.
     *
     * @param $name
     * @return null
     */
    public function __get($name)
    {
        if (isset($this->item->$name)) {
            return $this->item->$name;
        }

        return null;
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

    /**
     * Create Slack message.
     *
     * @param $notifiable
     * @return SlackMessage
     */
    public function toSlack($notifiable)
    {
        return (new SlackMessage())
            ->error()
            ->from(
                config('health.notifications.from.name'),
                config('health.notifications.from.icon_emoji')
            )
            ->content($this->getMessage())
            ->attachment(function ($attachment) {
                $attachment
                    ->title($this->getActionTitle(), $this->getActionLink())
                    ->content($this->result->errorMessage);
            });
    }

    /**
     * Create Mail message.
     *
     * @param $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage())
            ->line($this->getMessage($notifiable))
            ->from(
                config('health.notifications.from.address'),
                config('health.notifications.from.name')
            )
            ->action($this->getActionTitle(), $this->getActionLink());
    }

    /**
     * Get the action message.
     *
     * @param $item
     * @return \Illuminate\Config\Repository|mixed
     */
    private function getActionMessage($item)
    {
        return isset($item->errorMessage)
            ? $item->errorMessage
            : config('health.notifications.action_message');
    }

    /**
     * Get the action title.
     *
     * @return mixed
     */
    protected function getActionTitle()
    {
        return config('health.notifications.action-title');
    }

    /**
     * Get failing message.
     *
     * @return string
     */
    protected function getMessage()
    {
        $domain = Request::server('SERVER_NAME');

        return sprintf(
            $this->getActionMessage($this),
            studly_case($this->resource->name),
            $domain ? " in {$domain}." : '.'
        );
    }

    /**
     * Get the action link.
     *
     * @return string
     */
    protected function getActionLink()
    {
        return route(config('health.routes.notification'));
    }
}
