<?php

namespace PragmaRX\Health\Checkers;

use GuzzleHttp\TransferStats;
use GuzzleHttp\Client as Guzzle;
use Illuminate\Support\Collection;
use PragmaRX\Health\Support\Result;

class Http extends Base
{
    /**
     * @return Result
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
     * @return Result
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

                if (!$healthy) {
                    return $this->makeResult($healthy, $message);
                }
            }

            return $this->makeHealthyResult();
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
        if (is_a($this->target->urls, Collection::class)) {
            return $this->target->urls->toArray();
        }

        return (array) $this->target->urls;
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

        return [$success, $success ? '' : $this->getErrorMessage()];
    }

    /**
     * Send an http request and fetch the response.
     *
     * @param $url
     * @param $ssl
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
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
        $message = $this->target->resource->timeout_message;

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
        return $this->target->resource->connection_timeout;
    }

    /**
     * The the roundtrip timeout.
     *
     * @return int
     */
    private function getRoundtripTimeout()
    {
        return $this->target->resource->roundtrip_timeout;
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
        return preg_replace(
            '|^((https?:)?\/\/)?(.*)|',
            'http' . ($secure ? 's' : '') . '://\\3',
            $url
        );
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
        return (
            $this->fetchResponse($url, $ssl)->getStatusCode() == 200 &&
            !$this->requestTimedout()
        );
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
