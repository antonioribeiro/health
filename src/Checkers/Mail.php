<?php

namespace PragmaRX\Health\Checkers;

use Illuminate\Support\Facades\Mail as IlluminateMail;

class Mail extends Base
{
    /**
     * Store mail configuration.
     *
     * @var
     */
    private $mailConfiguration;

    /**
     * Check resource.
     *
     * @return bool
     */
    public function check()
    {
        return $this->checkMail();
    }

    /**
     * Configure mail for testing.
     */
    private function configureMail()
    {
        $this->mailConfiguration = config('mail');

        config(['mail' => $this->resource['config']->toArray()]);
    }

    /**
     * Send a test e-mail.
     */
    private function checkMail()
    {
        $this->configureMail();

        try {
            $this->sendMail();

            $result = $this->makeHealthyResult();
        } catch (\Exception $exception) {
            $result = $this->makeResultFromException($exception);
        }

        $this->restoreMailConfiguration();

        return $result;
    }

    /**
     * Restore mail configuration.
     */
    private function restoreMailConfiguration()
    {
        config(['mail' => $this->mailConfiguration]);
    }

    /**
     * Send a test e-mail message.
     */
    private function sendMail()
    {
        IlluminateMail::send($this->resource['view'], [], function ($message) {
            $fromAddress = array_get($this->resource, 'config.from.address');

            $message->returnPath($fromAddress);

            $message->cc($fromAddress);

            $message->bcc($fromAddress);

            $message->replyTo($fromAddress);

            $message->to($this->resource['to']);

            $message->subject($this->resource['subject']);
        });
    }
}
