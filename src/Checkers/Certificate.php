<?php

namespace PragmaRX\Health\Checkers;

use Illuminate\Support\Collection;
use Spatie\SslCertificate\SslCertificate;
use Illuminate\Support\Facades\Cache as IlluminateCache;
use PragmaRX\Health\Support\Result;

class Certificate extends Base
{
    /**
     * HTTP Checker.
     *
     * @return Result
     */
    public function check()
    {
        try {
            foreach ($this->getResourceUrlArray() as $url) {
                [$healthy, $message] = $this->checkCertificate($url);

                if (!$healthy) {
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
        $host = $this->getHost($url);

        return [
            SslCertificate::createForHostName($host)->isValid(),
            $this->getErrorMessage($host),
        ];
    }

    /**
     * Get the error message.
     *
     * @return string
     */
    protected function getErrorMessage($host)
    {
        return sprintf(
            $this->target->resource->errorMessage,
            $host
        );
    }

    /**
     * Get the error message.
     *
     * @return string
     */
    protected function getHost($url)
    {
        $parsed = parse_url($url);

        if (isset($parsed['host']))
        {
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
}
