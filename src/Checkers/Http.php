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

                if (!$healthy) {
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
        try {
            $success = $this->requestSuccessful($url, $ssl, $parameters);

            $message = $this->getErrorMessage();
        } catch (\Exception $exception) {
            $success = false;

            $message = "Target: {$url} - ERROR: " . $exception->getMessage();

            report($exception);
        }

        return [$success, $success ? '' : $message];
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
            $this->getMethod($parameters),
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
            'on_stats' => $this->onStatsCallback()
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
    private function requestSuccessful($url, $ssl, $parameters)
    {
        $response = $this->fetchResponse($url, $ssl, $parameters);

        if ($response->getStatusCode() >= 400) {
            throw new \Exception((string) $response->getBody());
        }

        return !$this->requestTimeout();
    }

    /**
     * Check if the request timed out.
     *
     * @return bool
     */
    private function requestTimeout()
    {
        return $this->totalTime > $this->getRoundtripTimeout();
    }

    /**
     * Parse URL from config.
     *
     * @return array
     */
    protected function parseConfigUrl($data)
    {
        if (is_string($data)) {
            return [$data, []];
        }

        $url = array_keys($data)[0];

        $parameters = $data[$url];

        $url = isset($parameters['url']) ? $parameters['url'] : $url;

        return [$url, $parameters];
    }

    /**
     * Get the request method.
     *
     * @return bool
     */
    protected function getMethod($parameters)
    {
        if (!isset($parameters['method'])) {
            return 'GET';
        }

        return $parameters['method'];
    }
}
