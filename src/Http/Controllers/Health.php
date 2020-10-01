<?php

namespace PragmaRX\Health\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;
use PragmaRX\Health\Service;

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

        return response($this->healthService->health());
    }

    /**
     * Check and get one resource.
     *
     * @param $slug
     * @return mixed
     * @throws \Exception
     */
    public function getResource($slug)
    {
        $this->healthService->setAction('resource');

        return $this->healthService->resource($slug);
    }

    /**
     * Get all resources.
     *
     * @return mixed
     * @throws \Exception
     */
    public function allResources()
    {
        return $this->healthService->getResources();
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function string()
    {
        $this->healthService->setAction('string');

        return response(
            $this->healthService->string()
        );
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function panel()
    {
        $this->healthService->setAction('panel');

        return response((string) view(config('health.views.panel'))->with('laravel', ['health' => config('health')]));
    }

    public function assetAppJs()
    {
        $file = File::get(config('health.assets.js'));

        $response = response()->make($file);

        $response->header('Content-Type', 'text/javascript');

        return $response;
    }

    public function assetAppCss()
    {
        $file = File::get(config('health.assets.css'));

        $response = response()->make($file);

        $response->header('Content-Type', 'text/css');

        return $response;
    }

    public function config()
    {
        return config('health');
    }
}
