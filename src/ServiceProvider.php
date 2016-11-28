<?php

namespace PragmaRX\Health;

use Event;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use PragmaRX\Health\Events\RaiseHealthIssue;
use PragmaRX\Health\Listeners\NotifyHealthIssue;

class ServiceProvider extends IlluminateServiceProvider
{
    /**
     * Configure package paths.
     *
     */
    private function configurePaths()
    {
        $this->publishes([
            __DIR__ . '/config/config.php' => config_path('health.php')
        ]);

        $this->publishes([
            __DIR__ . '/views/' => resource_path('views/vendor/pragmarx/health/')
        ]);
    }

    /**
     * Configure package folder views.
     *
     */
    private function configureViews()
    {
        $this->loadViewsFrom(realpath(__DIR__ . '/views'), 'pragmarx/health');
    }

    /**
     * Merge configuration.
     *
     */
    private function mergeConfig()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/config.php', 'health'
        );
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->configureViews();

        $this->mergeConfig();

        $this->configurePaths();

        $this->registerRoutes();

        $this->registerEventListeners();
    }

    /**
     *
     */
    private function registerEventListeners()
    {
        Event::listen(RaiseHealthIssue::class, NotifyHealthIssue::class);
    }

    /**
     * Register routes.
     *
     */
    private function registerRoutes()
    {
        $this->app->router->get(config('health.routes.prefix').config('health.routes.suffixes.panel'), [
            'as' => 'pragmarx.health.panel',
            'uses' => config('health.actions.panel')
        ]);

        $this->app->router->get(config('health.routes.prefix').config('health.routes.suffixes.check'), [
            'as' => 'pragmarx.health.check',
            'uses' => config('health.actions.check')
        ]);

        $this->app->router->get(config('health.routes.prefix').config('health.routes.suffixes.string'), [
            'as' => 'pragmarx.health.string',
            'uses' => config('health.actions.string')
        ]);

        $this->app->router->get(config('health.routes.prefix').config('health.routes.suffixes.resource').'/{name}', [
            'as' => 'pragmarx.health.resource',
            'uses' => config('health.actions.resource')
        ]);
    }
}
