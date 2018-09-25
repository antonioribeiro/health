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
        $attributes = [
            'middleware' => isset($route['middleware']) ? $route['middleware'] : [],
        ];

        $this->getRouter()->group($attributes, function () use ($route, $name) {
            $action = isset($route['controller'])
                ? "{$route['controller']}@{$route['action']}"
                : $route['action'];

            $this->getRouter()->get($route['uri'], [
                'as' => $name ?: $route['name'],
                'uses' => $action,
            ]);
        });
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
