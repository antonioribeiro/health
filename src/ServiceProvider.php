<?php

namespace PragmaRX\Health;

use Event;
use Artisan;
use PragmaRX\Yaml\Package\Yaml;
use PragmaRX\Health\Support\Cache;
use Illuminate\Console\Scheduling\Schedule;
use PragmaRX\Health\Support\ResourceLoader;
use PragmaRX\Health\Support\Traits\Routing;
use PragmaRX\Health\Events\RaiseHealthIssue;
use PragmaRX\Health\Support\ResourceChecker;
use PragmaRX\Health\Listeners\NotifyHealthIssue;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

class ServiceProvider extends IlluminateServiceProvider
{
    use Routing;

    /**
     * The application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * Resource loader instance.
     *
     * @var
     */
    protected $resourceLoader;

    /**
     * The health service.
     *
     * @var
     */
    private $healthService;

    /**
     * All artisan commands.
     *
     * @var
     */
    private $commands;

    /**
     * The router.
     *
     * @var
     */
    private $router;

    /**
     * Cache closure.
     *
     * @var
     */
    private $cacheClosure;

    /**
     * Resource checker closure.
     *
     * @var
     */
    private $resourceCheckerClosure;

    /**
     * Health service closure.
     *
     * @var
     */
    private $healthServiceClosure;

    /**
     * Configure package paths.
     */
    private function configurePaths()
    {
        $this->publishes(
            [
                __DIR__ . '/config/health.php' => config_path(
                    'health/config.php'
                ),
                __DIR__ . '/config/resources/' => config_path(
                    'health/resources/'
                ),
            ],
            'config'
        );

        $this->publishes(
            [
                __DIR__ . '/resources/views/' => resource_path(
                    'views/vendor/pragmarx/health/'
                ),
            ],
            'views'
        );

        $this->publishes(
            [
                __DIR__ . '/database/migrations/' => database_path(
                    'migrations'
                ),
            ],
            'migrations'
        );
    }

    /**
     * Configure package folder views.
     */
    private function configureViews()
    {
        $this->loadViewsFrom(
            realpath(__DIR__ . '/resources/views'),
            'pragmarx/health'
        );
    }

    /**
     * Create health service.
     */
    private function createHealthService()
    {
        $resourceChecker = call_user_func($this->resourceCheckerClosure);

        $cache = call_user_func($this->cacheClosure);

        $this->healthServiceClosure = function () use (
            $resourceChecker,
            $cache
        ) {
            return $this->instantiateService($resourceChecker, $cache);
        };

        $this->healthService = call_user_func($this->healthServiceClosure);
    }

    /**
     * Create resource checker.
     */
    private function createResourceChecker()
    {
        $this->resourceLoader = new ResourceLoader(new Yaml());

        $this->cacheClosure = $this->getCacheClosure();

        $this->resourceCheckerClosure = $this->getResourceCheckerClosure(
            $this->resourceLoader,
            call_user_func($this->cacheClosure)
        );
    }

    /**
     * Get the cache closure for instantiation.
     *
     * @return \Closure
     */
    private function getCacheClosure()
    {
        $cacheClosure = function () {
            return new Cache();
        };

        return $cacheClosure;
    }

    /**
     * Get the resource checker closure for instantiation.
     *
     * @param $resourceLoader
     * @param $cache
     * @return \Closure
     */
    private function getResourceCheckerClosure($resourceLoader, $cache)
    {
        $resourceCheckerInstance = function () use ($resourceLoader, $cache) {
            return new ResourceChecker($resourceLoader, $cache);
        };

        return $resourceCheckerInstance;
    }

    /**
     * Get the list of routes.
     *
     * @return array
     */
    private function getRoutes()
    {
        return config('health.routes.list');
    }

    /**
     * Instantiate commands.
     *
     * @return \Illuminate\Foundation\Application|mixed
     */
    private function instantiateCommands()
    {
        return $this->commands = instantiate(Commands::class, [
            $this->healthService,
        ]);
    }

    /**
     * Instantiate the main service.
     *
     * @param $resourceChecker
     * @param $cache
     * @return Service
     */
    private function instantiateService($resourceChecker, $cache)
    {
        return $this->healthService = new Service($resourceChecker, $cache);
    }

    /**
     * Merge configuration.
     */
    private function mergeConfig()
    {
        if (file_exists(config_path('/health/config.php'))) {
            $this->mergeConfigFrom(config_path('/health/config.php'), 'health');
        }

        $this->mergeConfigFrom(__DIR__ . '/config/health.php', 'health');
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

        $this->registerServices();

        $this->registerRoutes();

        $this->registerTasks();

        $this->registerEventListeners();

        $this->registerConsoleCommands();
    }

    private function registerResourcesRoutes()
    {
        collect($this->resourceLoader->getResources())->each(function ($item) {
            if (isset($item['routes'])) {
                collect($item['routes'])->each(function ($route, $key) {
                    $this->registerRoute($route, $key);
                });
            }
        });
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
        collect($routes = $this->getRoutes())->each(function ($route) {
            $this->registerRoute($route);
        });

        $this->registerResourcesRoutes();
    }

    /**
     * Register service.
     */
    private function registerServices()
    {
        $this->createServices();

        $this->app->singleton('pragmarx.health.cache', $this->cacheClosure);

        $this->app->singleton(
            'pragmarx.health.resource.checker',
            $this->resourceCheckerClosure
        );

        $this->app->singleton('pragmarx.health', $this->healthServiceClosure);

        $this->app->singleton(
            'pragmarx.health.commands',
            $this->instantiateCommands()
        );
    }

    /**
     * Create services.
     */
    public function createServices()
    {
        $this->createResourceChecker();

        $this->createHealthService();
    }

    /**
     * Register scheduled tasks.
     */
    private function registerTasks()
    {
        if (
            config('health.scheduler.enabled') &&
            ($frequency = config('health.scheduler.frequency')) &&
            config('health.notifications.enabled')
        ) {
            $scheduler = instantiate(Schedule::class);

            $scheduler
                ->call($this->healthService->getSilentChecker())
                ->$frequency();
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'pragmarx.health.cache',
            'pragmarx.health.resource.checker',
            'pragmarx.health',
            'pragmarx.health.commands',
        ];
    }
}
