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
    protected function makeFinalResult($ipAddress): Result
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
    protected function portIsNotConnectable($ipAddress): bool
    {
        return ! $this->portCheck(
            $ipAddress,
            $this->target->port,
            $this->target->timeout ?? 1
        );
    }
}
