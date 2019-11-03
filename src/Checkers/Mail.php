<?php

namespace PragmaRX\Health\Checkers;

use PragmaRX\Health\Support\Result;
use Illuminate\Support\Facades\Mail as IlluminateMail;
use Illuminate\Support\Arr;

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
     * @return Result
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

        config(['mail' => $this->target->config->toArray()]);
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
            report($exception);

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
        IlluminateMail::send($this->target->view, [], function ($message) {
            $fromAddress = Arr::get($this->target->config, 'from.address');

            $message->returnPath($fromAddress);

            $message->cc($fromAddress);

            $message->bcc($fromAddress);

            $message->replyTo($fromAddress);

            $message->to($this->target->to);

            $message->subject($this->target->subject);
        });
    }
}
