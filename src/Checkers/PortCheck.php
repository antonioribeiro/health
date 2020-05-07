<?php

namespace PragmaRX\Health\Checkers;

use PragmaRX\Health\Support\Result;

class PortCheck extends Base
{
    /**
     * Check the target.
     *
     * @return Result
     */
    public function check()
    {
        $this->prepareTargetData();

        if (
            $this->portIsNotConnectable(
                $ipAddress = ip_address_from_hostname($this->target->hostname)
            )
        ) {
            return $this->makeFinalResult($ipAddress);
        }

        return $this->makeHealthyResult();
    }

    /**
     * Get hostname and IP.
     *
     * @param $hostname
     * @return mixed
     */
    protected function hosnameAndIp($hostname, $ipAdress)
    {
        return $hostname.($hostname != $ipAdress ? " ({$ipAdress})" : '');
    }

    /**
     * Make the result.
     *
     * @param bool $ipAddress
     * @return Result
     */
    protected function makeFinalResult($ipAddress)
    {
        return $this->target->setResult(
            $this->makeResult(
                false,
                sprintf(
                    $this->target->getErrorMessage(),
                    $this->hosnameAndIp($this->target->hostname, $ipAddress),
                    $this->target->port
                )
            )
        )->getResult();
    }

    public function portCheck($ipAddress, $port, $timeout)
    {
        $fp = @fsockopen($ipAddress, $port, $errno, $errstr, $timeout);

        if (gettype($fp) !== 'resource') {
            return false;
        }

        fclose($fp);

        return true;
    }

    /**
     * @param $ipAddress
     * @return bool
     */
    protected function portIsNotConnectable($ipAddress)
    {
        return ! $this->portCheck(
            $ipAddress,
            $this->target->port,
            $this->target->timeout ?? 1
        );
    }

    private function prepareTargetData(): void
    {
        if (null === $this->target->port) {
            $url = parse_url($this->target->hostname);

            $this->target->hostname = $url['host'];
            $this->target->port = $url['port'];
        }
    }
}
