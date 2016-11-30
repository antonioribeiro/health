<?php

namespace PragmaRX\Health;

use Event;
use Artisan;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Routing\Router;
use PragmaRX\Health\Events\RaiseHealthIssue;
use PragmaRX\Health\Listeners\NotifyHealthIssue;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

class ServiceProvider extends IlluminateServiceProvider
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * @var
     */
    private $healthService;

    /**
     * @var
     */
    private $commands;

    /**
     * @var
     */
    private $router;

    /**
     * Configure package paths.
     */
    private function configurePaths()
    {
        $this->publishes([
            __DIR__.'/config/config.php' => config_path('health.php'),
        ]);

        $this->publishes([
            __DIR__.'/views/' => resource_path('views/vendor/pragmarx/health/'),
        ]);
    }

    /**
     * Configure package folder views.
     */
    private function configureViews()
    {
        $this->loadViewsFrom(realpath(__DIR__.'/views'), 'pragmarx/health');
    }

    /**
     * Return the health service.
     *
     * @return mixed
     */
    public function getHealthService()
    {
        return $this->healthService;
    }

    /**
     * @return mixed
     */
    private function getRouter()
    {
        if (! $this->router) {
            $this->router = $this->app->router;

            if (! $this->router instanceof Router) {
                $this->router = app()->router;
            }
        }

        return $this->router;
    }

    /**
     * @return \Illuminate\Foundation\Application|mixed
     */
    private function instantiateCommands()
    {
        return $this->commands = app(Commands::class, [$this->healthService]);
    }

    /**
     * @return \Illuminate\Foundation\Application|mixed
     */
    private function instantiateService()
    {
        return $this->healthService = app(Service::class);
    }

    /**
     * Merge configuration.
     */
    private function mergeConfig()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/config.php', 'health'
        );
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfig();

        $this->configureViews();

        $this->configurePaths();

        $this->registerService();

        $this->registerRoutes();

        $this->registerTasks();

        $this->registerEventListeners();

        $this->registerConsoleCommands();
    }

    /**
     * Register console commands.
     */
    private function registerConsoleCommands()
    {
        $commands = $this->commands;

        Artisan::command('health:panel', function () use ($commands) {
            $commands->panel($this);
        })->describe('Show all resources and their current health states.');

        Artisan::command('health:check', function () use ($commands) {
            $commands->check($this);
        })->describe('Check resources health and send error notifications.');
    }

    /**
     * Register event listeners.
     */
    private function registerEventListeners()
    {
        Event::listen(RaiseHealthIssue::class, NotifyHealthIssue::class);
    }

    /**
     * Register routes.
     */
    private function registerRoutes()
    {
        $this->getRouter()->get(config('health.routes.prefix').config('health.routes.suffixes.panel'), [
            'as' => 'pragmarx.health.panel',
            'uses' => config('health.actions.panel'),
        ]);

        $this->getRouter()->get(config('health.routes.prefix').config('health.routes.suffixes.check'), [
            'as' => 'pragmarx.health.check',
            'uses' => config('health.actions.check'),
        ]);

        $this->getRouter()->get(config('health.routes.prefix').config('health.routes.suffixes.string'), [
            'as' => 'pragmarx.health.string',
            'uses' => config('health.actions.string'),
        ]);

        $this->getRouter()->get(config('health.routes.prefix').config('health.routes.suffixes.resource').'/{name}', [
            'as' => 'pragmarx.health.resource',
            'uses' => config('health.actions.resource'),
        ]);
    }

    /**
     * Register service.
     */
    private function registerService()
    {
        $this->app->singleton('pragmarx.health', $this->instantiateService());

        $this->app->singleton('pragmarx.health.commands', $this->instantiateCommands());
    }

    /**
     * Register scheduled tasks.
     */
    private function registerTasks()
    {
        if (config('health.scheduler.enabled') &&
            ($frequency = config('health.scheduler.frequency')) &&
            config('health.notifications.enabled')
        ) {
            $scheduler = app(Schedule::class);

            $scheduler->call($this->healthService->getSilentChecker())->{$frequency}();
        }
    }
}
