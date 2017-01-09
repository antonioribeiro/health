<?php

namespace PragmaRX\Health\Checkers;

use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\RequestException;

class HttpChecker extends BaseChecker
{
    /**
     * @var bool
     */
    protected $secure = false;

    /**
     * @var
     */
    protected $guzzle;

    /**
     * Boot the checker.
     */
    public function boot()
    {
        $this->guzzle = new Guzzle();
    }

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
     *  Check web pages.
     *
     * @param $url
     * @param bool $ssl
     * @return mixed
     */
    private function checkWebPage($url, $ssl = false)
    {
        try {
            $response = $this->guzzle->request('GET', $url, $this->getConnectionOptions($ssl));
        } catch (RequestException $e) {
            return [false, $e->getMessage()];
        }

        return [$response->getStatusCode() == 200, ''];
    }

    /**
     * Get http connection options.
     *
     * @param $ssl
     * @return array
     */
    private function getConnectionOptions($ssl)
    {
        return [
            'connect_timeout' => 2000,
            'timeout' => 2000,
            'verify' => $ssl,
        ];
    }

    /**
     * @param $url
     * @param $secure
     * @return mixed
     */
    private function setScheme($url, $secure)
    {
        return preg_replace('|^((https?:)?\/\/)?(.*)|', 'http'.($secure ? 's' : '').'://\\3', $url);
    }
}
