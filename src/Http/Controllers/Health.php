<?php

namespace PragmaRX\Health\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use PragmaRX\Health\Service;
use Illuminate\Routing\Controller;

class Health extends Controller
{
    /**
     * @var Service
     */
    private $healthService;

    /**
     * Health constructor.
     *
     * @param Service $healthService
     */
    public function __construct(Service $healthService)
    {
        $this->healthService = $healthService;
    }

    /**
     * Check all resources.
     *
     * @return array
     * @throws \Exception
     */
    public function check()
    {
        $this->healthService->setAction('check');

        return response(
            $this->healthService->health(),
            $this->getReponseCode()
        );
    }

    /**
     * @return int
     * @throws \Exception
     */
    private function getReponseCode()
    {
        $code = $this->healthService->isHealthy() ? 200 : 500;

        return $code;
    }

    /**
     * @param $slug
     * @return mixed
     * @throws \Exception
     */
    public function resource($slug)
    {
        $this->healthService->setAction('resource');

        return $this->healthService->resource($slug);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function string()
    {
        $this->healthService->setAction('string');

        return response(
            $this->healthService->string(),
            $this->getReponseCode()
        );
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function panel()
    {
        $this->healthService->setAction('panel');

        $view = view(
            config(
                ($health = $this->healthService->health())->isEmpty()
                    ? 'health.views.empty-panel'
                    : 'health.views.panel'
            ),
            [
                'health' => $health,
            ]
        );

        return response((string) $view, $this->getReponseCode());
    }

    public function assetAppJs()
    {
        $file = File::get(config('health.assets.js'));

        $response = response()->make($file);

        $response->header('Content-Type', "text/css");

        return $response;
    }

    public function assetAppCss()
    {
        $file = File::get(config('health.assets.css'));

        $response = response()->make($file);

        $response->header('Content-Type', "text/css");

        return $response;
    }
}
