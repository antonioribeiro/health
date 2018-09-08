<?php

namespace PragmaRX\Health\Checkers;

use GuzzleHttp\TransferStats;
use GuzzleHttp\Client as Guzzle;
use Illuminate\Support\Collection;

class Http extends Base
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
     * @var
     */
    private $totalTime;

    /**
     * @var
     */
    private $url;

    /**
     * HTTP Checker.
     *
     * @return bool
     */
    public function check()
    {
        try {
            $health = [];

            foreach ($this->getResourceUrlArray() as $url) {
                list($healthy, $message) = $this->checkWebPage(
                    $this->makeUrlWithScheme($url, $this->secure),
                    $this->secure
                );

                $health[] = $this->makeResult($healthy, $message);
            }

            return $health;
        } catch (\Exception $exception) {
            return $this->makeResultFromException($exception);
        }
    }

    /**
     *  Get array of resource urls.
     *
     * @return array
     */
    private function getResourceUrlArray()
    {
        if (is_a($this->resource['url'], Collection::class)) {
            return $this->resource['url']->toArray();
        }

        return (array) $this->resource['url'];
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
        $success = $this->requestSuccessful($url, $ssl);

        return [
            $success,
            $success ? '' : $this->getErrorMessage(),
        ];
    }

    /**
     * Send an http request and fetch the response.
     *
     * @param $url
     * @param $ssl
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    private function fetchResponse($url, $ssl)
    {
        $this->url = $url;

        return (new Guzzle())->request(
            'GET',
            $this->url,
            $this->getConnectionOptions($ssl)
        );
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
            'connect_timeout' => $this->getConnectionTimeout(),
            'timeout' => $this->getConnectionTimeout(),
            'verify' => $ssl,
            'on_stats' => $this->onStatsCallback(),
        ];
    }

    /**
     * Get the error message.
     *
     * @return string
     */
    private function getErrorMessage()
    {
        $message = array_get($this->resource, 'timeout_message') ?:
                    '[TIMEOUT] A request to %s took %s seconds. Timeout is set to %s seconds.';

        return sprintf(
            $message,
            $this->url,
            $this->totalTime,
            $this->getRoundtripTimeout()
        );
    }

    /**
     * The the connection timeout.
     *
     * @return int
     */
    private function getConnectionTimeout()
    {
        return array_get($this->resource, 'connection_timeout') ?: 30;
    }

    /**
     * The the roundtrip timeout.
     *
     * @return int
     */
    private function getRoundtripTimeout()
    {
        return array_get($this->resource, 'roundtrip_timeout') ?: 30;
    }

    /**
     * Make a url with a proper scheme.
     *
     * @param $url
     * @param $secure
     * @return mixed
     */
    private function makeUrlWithScheme($url, $secure)
    {
        return preg_replace('|^((https?:)?\/\/)?(.*)|', 'http'.($secure ? 's' : '').'://\\3', $url);
    }

    /**
     * Guzzle OnStats callback.
     *
     * @return \Closure
     */
    private function onStatsCallback()
    {
        return function (TransferStats $stats) {
            $this->totalTime = $stats->getTransferTime();
        };
    }

    /**
     * Send a request and get the result.
     *
     * @param $url
     * @param $ssl
     * @return bool
     * @internal param $response
     */
    private function requestSuccessful($url, $ssl)
    {
        return $this->fetchResponse($url, $ssl)->getStatusCode() == 200 &&
                ! $this->requestTimedout();
    }

    /**
     * Check if the request timed out.
     *
     * @return bool
     */
    private function requestTimedout()
    {
        return $this->totalTime > $this->getRoundtripTimeout();
    }
}
