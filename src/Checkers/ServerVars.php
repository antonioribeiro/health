<?php

namespace PragmaRX\Health\Checkers;

use GuzzleHttp\Client as Guzzle;
use Illuminate\Support\Str;
use PragmaRX\Health\Support\LocallyProtected;
use PragmaRX\Health\Support\Result;

class ServerVars extends Base
{
    protected $response;
    protected $errors;

    /**
     * Check resource.
     *
     * @return Result
     */
    public function check()
    {
        $this->requestServerVars();

        collect($this->target->config['vars'])->each(function ($var) {
            $this->checkVar($var);
        });

        return blank($this->errors)
            ? $this->makeHealthyResult()
            : $this->makeResult(false, sprintf($this->target->resource->errorMessage, implode('; ', $this->errors)));
    }

    public function requestServerVars()
    {
        $url = $this->makeUrl();

        $bearer = (new LocallyProtected())->protect($this->target->config['cache_timeout'] ?? 60);

        $guzze = new Guzzle($this->getAuthorization());

        $response = $guzze->request('GET', $url, [
            'headers' => ['API-Token' => $bearer],
        ]);

        if (($code = $response->getStatusCode()) !== 200) {
            throw new \Exception("Request to {$url} returned a status code {$code}");
        }

        $this->response = json_decode((string) $response->getBody(), true);
    }

    public function checkVar($var)
    {
        if (blank($this->response[$var['name']] ?? null)) {
            if ($var['mandatory']) {
                $this->errors[] = "{$var['name']} is empty";
            }

            return;
        }

        $got = $this->response[$var['name']];

        $expected = $var['value'];

        if (! $this->compare($var, $expected, $got)) {
            $this->errors[] = "{$var['name']}: expected '{$expected}' but got '{$got}'";
        }
    }

    public function compare($var, $expected, $got)
    {
        $operator = $var['operator'] ?? 'equals';

        $strict = $var['strict'] ?? true;

        if ($operator === 'equals') {
            return $strict ? $expected === $got : $expected == $got;
        }

        if ($operator === 'contains') {
            return Str::contains($got, $expected);
        }

        throw new \Exception("Operator '$operator' is not supported.");
    }

    public function makeUrl()
    {
        $url = route($this->target->config['route']);

        if ($queryString = $this->target->config['query_string']) {
            $url .= "?$queryString";
        }

        return $url;
    }

    public function getAuthorization()
    {
        if (blank($auth = $this->target->config['auth'] ?? null)) {
            return [];
        }

        return ['auth' => [$auth['username'], $auth['password']]];
    }
}
