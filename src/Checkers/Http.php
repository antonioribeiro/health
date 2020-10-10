<?php

namespace PragmaRX\Health\Checkers;

use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\TransferStats;
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
                [$url, $parameters] = $this->parseConfigUrl($url);

                [$healthy, $message] = $this->checkWebPage(
                    $this->makeUrlWithScheme($url, $this->secure),
                    $this->secure,
                    $parameters
                );

                if (! $healthy) {
                    return $this->makeResult($healthy, $message);
                }
            }

            return $this->makeHealthyResult();
        } catch (\Exception $exception) {
            report($exception);

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
    private function checkWebPage($url, $ssl = false, $parameters = [])
    {
        $success = $this->requestSuccessful($url, $ssl, $parameters);

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
    private function fetchResponse($url, $ssl, $parameters = [])
    {
        $this->url = $url;

        return (new Guzzle())->request(
            'GET',
            $this->url,
            array_merge($this->getConnectionOptions($ssl), $parameters)
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
        $message = $this->target->resource->timeoutMessage;

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
        return $this->target->resource->connectionTimeout;
    }

    /**
     * The the roundtrip timeout.
     *
     * @return int
     */
    private function getRoundtripTimeout()
    {
        return $this->target->resource->roundtripTimeout;
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
            'http'.($secure ? 's' : '').'://\\3',
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
    private function requestSuccessful($url, $ssl, $parameters)
    {
        return
            $this->fetchResponse($url, $ssl, $parameters)->getStatusCode() == 200 &&
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

    public function parseConfigUrl($data)
    {
        if (is_string($data)) {
            return [$data, []];
        }

        $url = array_keys($data)[0];

        $parameters = $data[$url];

        return [$url, $parameters];
    }
}
