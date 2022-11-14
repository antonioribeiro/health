<?php

namespace PragmaRX\Health\Checkers;

use GuzzleHttp\Client as Guzzle;
use Illuminate\Support\Collection;
use Mockery\Exception;
use PragmaRX\Health\Support\Result;

class HealthPanel extends Http
{
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
     * @var
     */
    protected $secure = true;

    /**
     * HTTP Checker.
     *
     * @return Result
     */
    public function check()
    {
        $resources = $this->getResourceUrlArray();

        $first = collect($resources)->first();

        if (filled($first)) {
            $this->target->setDisplay("{$first['url']}");
        }

        try {
            foreach ($resources as $url) {
                [$healthy, $message] = $this->checkHealthPanel($url);

                if (! $healthy) {
                    return $this->makeResult(false, $message);
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
        $urls = $this->target->urls;

        if (! is_a($urls, Collection::class)) {
            $urls = collect($urls);
        }

        $result = collect();

        $index = 0;

        foreach ($urls as $urlGroup) {
            foreach ($urlGroup as $url => $values) {
                if (blank($values['url'] ?? null)) {
                    $values['url'] = $url;
                }

                $result[$index] = $values;

                $index++;
            }
        }

        return $result->toArray();
    }

    /**
     * HTTP Checker.
     *
     * @return Result
     */
    public function checkHealthPanel($url)
    {
        $resources = $this->getJson($url);

        if ($resources === null) {
            throw new \Exception('Error reading Health Panel json from '.$url['url']);
        }

        $messages = [];

        foreach ($resources as $resource) {
            foreach ($resource['targets'] as $target) {
                if (! $target['result']['healthy']) {
                    $messages[] = "{$resource['name']}: failing.";
                }
            }
        }

        if (count($messages) > 0) {
            throw new Exception(join(' ', $messages));
        }

        return [true, null];
    }

    /**
     * Send an http request and fetch the panel json.
     *
     * @param $url
     * @return mixed|\Psr\Http\Message\ResponseInterface
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function fetchJson($url, $parameters = [])
    {
        $this->url = $url;

        return (new Guzzle())->request(
            $parameters['method'],
            $this->url,
            array_merge($this->getConnectionOptions($this->secure), $parameters)
        );
    }

    public function getJson($parameters)
    {
        $url = $parameters['url'];

        unset($parameters['url']);

        return json_decode((string) $this->fetchJson($url, $parameters)->getBody(), true);
    }
}
