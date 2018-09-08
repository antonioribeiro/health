<?php

namespace PragmaRX\Health\Checkers;

class PortCheck extends Base
{
    /**
     * @return bool
     */
    public function check()
    {
        foreach ($this->resource['ports'] as $data) {
            $ipAddress = ip_address_from_hostname($data['hostname']);

            if (! $this->portCheck($ipAddress, $data['port'], $data['timeout'] ?? 1)) {
                return $this->makeResult(
                    false,
                    sprintf($this->resource['error_message'],
                    $this->hosnameAndIp($data['hostname'], $ipAddress), $data['port'])
                );
            }
        }

        return $this->makeHealthyResult();
    }

    /**
     * @param $hostname
     * @return mixed
     */
    protected function hosnameAndIp($hostname, $ipAdress)
    {
        return $hostname.($hostname != $ipAdress ? " ({$ipAdress})" : '');
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
}
