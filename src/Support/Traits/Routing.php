<?php

namespace PragmaRX\Health\Support\Traits;

trait Routing
{
    /**
     * @param $route
     * @param null $name
     */
    protected function registerRoute($route, $name = null)
    {
        $action = isset($route['controller'])
            ? "{$route['controller']}@{$route['action']}"
            : $route['action'];

        $router = $this->getRouter()->get($route['uri'], [
            'as' => $name ?: $route['name'],
            'uses' => $action,
        ]);

        if (isset($route['middleware'])) {
            $router->middleware($route['middleware']);
        }
    }

    /**
     * Get the current router.
     *
     * @return mixed
     */
    protected function getRouter()
    {
        return app()->router;
    }
}
