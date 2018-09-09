<?php

namespace PragmaRX\Health\Checkers;

use DomainException;
use DocuSign\eSign\ApiClient;
use DocuSign\eSign\Configuration;
use PragmaRX\Health\Support\Result;
use DocuSign\eSign\Api\AuthenticationApi;
use DocuSign\eSign\Api\AuthenticationApi\LoginOptions;

class Docusign extends Base
{
    /**
     * @return Result
     */
    public function check()
    {
        if ($this->docusignIsNotInstalled()) {
            return $this->makeResult(
                false,
                $this->target->not_installed_message
            );
        }

        if (! $this->login()) {
            throw new DomainException(
                'Unable to authenticate to the DocuSign Api'
            );
        }

        return $this->makeHealthyResult();
    }

    private function docusignIsNotInstalled()
    {
        return ! class_exists(ApiClient::class);
    }

    private function getAccountIdFromLogin($login)
    {
        return $login->getLoginAccounts()[0]->getAccountId();
    }

    /**
     * @param $config
     * @return ApiClient
     */
    protected function getApiClient($config)
    {
        return new ApiClient($config);
    }

    /**
     * @param $config
     * @return AuthenticationApi
     */
    protected function getAuthApi($config)
    {
        return new AuthenticationApi($this->getApiClient($config));
    }

    /**
     * @return ApiClient
     */
    protected function getConfig()
    {
        return (new Configuration())
            ->setDebug($this->target->debug)
            ->setDebugFile($this->makeFileName($this->target->debug_file))
            ->setHost($this->target->api_host)
            ->addDefaultHeader(
                'X-DocuSign-Authentication',
                json_encode([
                    'Username' => $this->target->username,
                    'Password' => $this->target->password,
                    'IntegratorKey' => $this->target->integrator_key,
                ])
            );
    }

    /**
     * @param $config
     * @return \DocuSign\eSign\Model\LoginInformation
     */
    protected function getLoginInformation($config)
    {
        return $this->getAuthApi($config)->login($this->getLoginOptions());
    }

    /**
     * @return LoginOptions
     */
    protected function getLoginOptions()
    {
        return new LoginOptions();
    }

    /**
     * @return mixed
     */
    protected function login()
    {
        return $this->getAccountIdFromLogin(
            $this->getLoginInformation($this->getConfig())
        );
    }

    private function makeFileName($file)
    {
        if (is_absolute_path($file)) {
            return $file;
        }

        return base_path($file);
    }
}
