<?php

namespace PragmaRX\Health\Checkers;

class HttpChecker extends BaseChecker
{
    /**
     * @var bool
     */
    protected $secure = false;

    /**
     * @return bool
     */
    public function check()
    {
        try {
            $url = $this->setScheme($this->resource['url'], $this->secure);

            list($healthy, $message) = $this->checkWebPage($url, $this->secure);

            return $this->makeResult($healthy, $message);
        } catch (\Exception $exception) {
            return $this->makeResultFromException($exception);
        }
    }

    /**
     * @param $url
     * @param bool $ssl
     * @return mixed
     */
    private function checkWebPage($url, $ssl = false)
    {
        // Initialize session and set URL.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);

        // Set so curl_exec returns the result instead of outputting it.
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $ssl ? 1 : 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $ssl ? 2 : 0);

        // Get the response
        $response = curl_exec($ch);
        $error = curl_error($ch);

        // close the channel
        curl_close($ch);

        return [(bool) $response, $error];
    }

    private function setScheme($url, $secure)
    {
        return preg_replace('|^((https?:)?\/\/)?(.*)|', 'http'.($secure ? 's' : '').'://\\3', $url);
    }
}
