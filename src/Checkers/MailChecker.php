<?php

namespace PragmaRX\Health\Checkers;

use Mail;

class MailChecker extends BaseChecker
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
     * @param $resources
     * @return bool
     */
    public function check($resources)
    {
        return $this->checkMail();
    }

    /**
     * Configure mail for testing.
     *
     */
    private function configureMail()
    {
        $this->mailConfiguration = config('mail');

        config(['mail' => config('health.mail.config')]);
    }

    /**
     * Send a test e-mail.
     *
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
     *
     */
    private function restoreMailConfiguration()
    {
        config(['mail' => $this->mailConfiguration]);
    }

    /**
     * Send a test e-mail message.
     *
     */
    private function sendMail()
    {
        Mail::send(config('health.views.email'), [], function ($message) {
            $message->returnPath(config('health.mail.config.from.address'));

            $message->cc(config('health.mail.config.from.address'));

            $message->bcc(config('health.mail.config.from.address'));

            $message->replyTo(config('health.mail.config.from.address'));

            $message->to(config('health.mail.to'));

            $message->subject(config('health.mail.subject'));
        });
    }
}
