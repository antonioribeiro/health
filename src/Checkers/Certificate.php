<?php

namespace PragmaRX\Health\Checkers;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use PragmaRX\Health\Support\Result;
use Spatie\SslCertificate\SslCertificate;

class Certificate extends Base
{
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
            $this->target->setDisplay("{$first}");
        }

        try {
            foreach ($resources as $url) {
                [$healthy, $message] = $this->checkCertificate($url);

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
     * HTTP Checker.
     *
     * @return Result
     */
    public function checkCertificate($url)
    {
        return $this->checkHostCertificate($this->getHost($url));
    }

    /**
     * Get the error message.
     *
     * @return string
     */
    protected function getErrorMessage($host)
    {
        return sprintf($this->target->resource->errorMessage, $host);
    }

    /**
     * Get the error message.
     *
     * @return string
     */
    protected function getHost($url)
    {
        $parsed = parse_url($url);

        if (isset($parsed['host'])) {
            return $parsed['host'];
        }

        return $url;
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

    public function checkHostCertificate($host)
    {
        $result = collect([
            'openssl' => $this->checkCertificateWithOpenSSL($host),

            'package' => [
                SslCertificate::createForHostName($host)->isValid(),
                'Invalid certificate',
            ],

            'php' => $this->checkCertificateWithPhp($host),
        ])
            ->filter(function ($result) { return $result[0] === false; })
            ->first();

        if ($result === null) {
            return [true, ''];
        }

        return $result;
    }

    public function checkCertificateWithOpenSSL($host)
    {
        exec($this->makeCommand($host), $output);

        $result = collect($output)
            ->filter(
                function ($line) {
                    return Str::contains(
                        $line,
                        $this->target->resource->verifyString
                    );
                }
            )
            ->first();

        if (blank($result)) {
            $output = blank($output) ? 'Unkown openssl error' : $output;

            return [false, json_encode($output)];
        }

        return [
            trim($result) == $this->target->resource->successString,
            $result,
        ];
    }

    public function checkCertificateWithPhp($host)
    {
        try {
            $get = stream_context_create([
                'ssl' => ['capture_peer_cert' => true],
            ]);

            $read = stream_socket_client(
                'ssl://'.$host.':443',
                $errno,
                $errstr,
                30,
                STREAM_CLIENT_CONNECT,
                $get
            );
        } catch (\Exception $exception) {
            return [false, $exception->getMessage()];
        }

        return [true, ''];
    }

    /**
     * @param $host
     * @return string|string[]
     */
    protected function makeCommand($host)
    {
        $command = $this->target->resource->command;

        $command = str_replace('{$options}', $this->target->options, $command);

        $command = str_replace('{$host}', $host, $command);

        return $command;
    }
}
