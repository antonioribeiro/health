<?php

namespace PragmaRX\Health\Http\Controllers;

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
     */
    public function check()
    {
        $this->healthService->setAction('check');

        $health = $this->healthService->health();

        $code = $this->healthService->isHealthy() ? 200 : 500;

        return response($health, $code);
    }

    /**
     * @return mixed
     */
    public function resource($name)
    {
        $this->healthService->setAction('resource');

        return $this->healthService->resource($name);
    }

    /**
     * @return mixed
     */
    public function string()
    {
        $this->healthService->setAction('string');

        return $this->healthService->string();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function panel()
    {
        $this->healthService->setAction('panel');

        return view(config('health.views.panel'), [
            'health' => $this->healthService->panel(),
        ]);
    }
}
