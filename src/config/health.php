<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Health Monitor Title
    |--------------------------------------------------------------------------
    |
    | This is the title of the health check panel, that shows up at the top-left
    | corner of the window. Feel free to edit this value to suit your needs.
    |
    */
    'title' => 'Laravel Health Check Panel',

    /*
    |--------------------------------------------------------------------------
    | Health Monitor Resources
    |--------------------------------------------------------------------------
    |
    | Below is the list of resources the health checker will look into.
    | And the path to where the resources yaml files are located.
    |
    */
    'resources' => [

        /*
        |--------------------------------------------------------------------------
        | Health Monitor Resources Path
        |--------------------------------------------------------------------------
        |
        | This value determines the path to where the resources yaml files are
        | located. By default, all resources are in config/health/resources
        |
        */
        'path' => config_path('health/resources'),

        /*
        |--------------------------------------------------------------------------
        | Health Monitor Enabled Resources
        |--------------------------------------------------------------------------
        |
        | Below is the list of resources currently enabled for your laravel application.
        | The default enabled resources are picked for the common use-case. However,
        | you are free to uncomment certain resource or add your own as you wish.
        |
        */
        'enabled' => [
            'API',
            'AppKey',
            // 'Adyen',
            // 'Broadcasting',
            'Cache',
            // config'Certificate',
            // 'CheckoutCom',
            'ConfigurationCached',
            'Database',
            'DebugMode',
            'DirectoryPermissions',
            'DiskSpace',
            // 'Dynamics',
            // 'DocuSign',
            // 'ElasticsearchConnectable',
            'EnvExists',
            'Extensions',
            'Filesystem',
            'Framework',
            // 'HealthPanel',
            // 'Horizon',
            // 'Http',
            'Https',
            'LaravelServices',
            'Latency',
            'LocalStorage',
            'Mail',
            // 'MailgunConnectable',
            // 'MemcachedConnectable',
            'MigrationsUpToDate',
            'MySql',
            'MySqlConnectable',
            // 'NewrelicDeamon',
            'NginxServer',
            // 'PackagesUpToDate',
            'Php',
            // 'PostgreSqlConnectable',
            // 'PostgreSqlServer',
            'Queue',
            'QueueWorkers',
            'RebootRequired',
            'Redis',
            'RedisConnectable',
            'RedisServer',
            'RoutesCached',
            // 'S3',
            'SecurityChecker',
            // 'SeeTickets',
            // 'Sendinblue',
            'ServerLoad',
            // 'ServerVars', ----------- You also need to enable the server-vars route
            'ServerUptime',
            // 'Sshd',
            'Supervisor',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Health Monitor Sort Key
    |--------------------------------------------------------------------------
    |
    | This value determines how the resources cards in your panel is sorted. By
    | default, we sort by slug, but you may use other supported values below
    |
    | Options: 'abbreviation', 'slug', 'name'
    */
    'sort_by' => 'slug',

    /*
    |--------------------------------------------------------------------------
    | Health Monitor Caching
    |--------------------------------------------------------------------------
    |
    | Below is the list of configurations for health monitor caching mechanism
    |
    */
    'cache' => [
        /*
        |--------------------------------------------------------------------------
        | Health Monitor Caching Key
        |--------------------------------------------------------------------------
        |
        | This value determines the key to use for caching the results of health
        | monitor. Please feel free to update this to suit your own convention
        |
        */
        'key' => 'health-resources',

        /*
        |--------------------------------------------------------------------------
        | Health Monitor Caching Duration
        |--------------------------------------------------------------------------
        |
        | This determines how long the results of each check should stay cached in
        | your application. When your application is in "debug" mode caching is
        | automatically disabled, otherwise we default to caching every minute
        |
        | Options:
        |   0 = Cache Forever
        |   false = Disables caching
        |   30 = (integer) Seconds to cache
        */
        'seconds' => config('app.debug') === true ? false : 60,
    ],

    'database' => [
        'enabled' => false,

        'graphs' => [
            'enabled' => true,

            'height' => 90,
        ],

        'max_records' => 30,

        'model' => PragmaRX\Health\Data\Models\HealthCheck::class,
    ],

    'services' => [
        'ping' => [
            'bin' => env('HEALTH_PING_BIN', '/sbin/ping'),
        ],

        'composer' => [
            'bin' => env('HEALTH_COMPOSER_BIN', 'composer'),
        ],
    ],

    'assets' => [
        'css' => base_path(
            'vendor/pragmarx/health/src/resources/dist/css/app.css'
        ),

        'js' => base_path(
            'vendor/pragmarx/health/src/resources/dist/js/app.js'
        ),
    ],

    'cache_files_base_path' => $path = 'app/pragmarx/health',

    'notifications' => [
        'enabled' => false,

        'notify_on' => [
            'panel' => false,
            'check' => true,
            'string' => true,
            'resource' => false,
        ],

        'notify_from' => [
            'web' => false,
            'console' => true,
        ],

        'subject' => 'Health Status',

        'action-title' => 'View App Health',

        'action_message' => "The '%s' service is in trouble and needs attention%s",

        'from' => [
            'name' => 'Laravel Health Checker',

            'address' => 'healthchecker@mydomain.com',

            'icon_emoji' => ':anger:',
        ],

        'scheduler' => [
            'enabled' => false,

            'frequency' => 'everyFiveMinutes', // most methods on -- https://laravel.com/docs/8.x/scheduling#schedule-frequency-options
        ],

        'users' => [
            'model' => App\Models\User::class,

            'emails' => ['admin@mydomain.com'],
        ],

        'channels' => ['mail', 'slack'], // mail, slack

        'notifier' => 'PragmaRX\Health\Notifications\HealthStatus',
    ],

    'alert' => [
        'success' => [
            'type' => 'success',
            'message' => 'Everything is fine with this resource',
        ],

        'error' => [
            'type' => 'error',
            'message' => 'We are having trouble with this resource',
        ],
    ],

    'style' => [
        'columnSize' => 2,

        'button_lines' => 'multi', // multi or single

        'multiplier' => 0.4,

        'opacity' => [
            'healthy' => '0.4',

            'failing' => '1',
        ],
    ],

    'views' => [
        'panel' => 'pragmarx/health::default.panel',

        'empty-panel' => 'pragmarx/health::default.empty-panel',

        'partials' => [
            'well' => 'pragmarx/health::default.partials.well',
        ],
    ],

    'string' => [
        'glue' => '-',
        'ok' => 'OK',
        'warning' => 'WARNING',
        'critical' => 'CRITICAL',
        'unknown' => 'UNKNOWN',
        'fail' => 'FAIL',
    ],

    'routes' => [
        'prefix' => $route_prefix = '/health',

        'namespace' => $namespace = 'PragmaRX\Health\Http\Controllers\Health',

        'notification' => 'pragmarx.health.panel',

        'list' => [
            [
                'uri' => "{$route_prefix}/panel",
                'name' => 'pragmarx.health.panel',
                'action' => "{$namespace}@panel",
                'middleware' => [
                    /*'auth.basic'*/
                ],
            ],

            [
                'uri' => "{$route_prefix}/check",
                'name' => 'pragmarx.health.check',
                'action' => "{$namespace}@check",
                'middleware' => [],
            ],

            [
                'uri' => "{$route_prefix}/string",
                'name' => 'pragmarx.health.string',
                'action' => "{$namespace}@string",
                'middleware' => [],
            ],

            [
                'uri' => "{$route_prefix}/resources",
                'name' => 'pragmarx.health.resources.all',
                'action' => "{$namespace}@allResources",
                'middleware' => [],
            ],

            [
                'uri' => "{$route_prefix}/resources/{slug}",
                'name' => 'pragmarx.health.resources.get',
                'action' => "{$namespace}@getResource",
                'middleware' => [],
            ],

            [
                'uri' => "{$route_prefix}/assets/css/app.css",
                'name' => 'pragmarx.health.assets.css',
                'action' => "{$namespace}@assetAppCss",
                'middleware' => [],
            ],

            [
                'uri' => "{$route_prefix}/assets/js/app.js",
                'name' => 'pragmarx.health.assets.js',
                'action' => "{$namespace}@assetAppJs",
                'middleware' => [],
            ],

            [
                'uri' => "{$route_prefix}/config",
                'name' => 'pragmarx.health.config',
                'action' => "{$namespace}@config",
                'middleware' => [],
            ],

            /// This is a dangerous route, when enabling, check if it is properly protected
            //[
            //    'uri' => "{$route_prefix}/server-vars",
            //    'name' => 'pragmarx.health.server-vars',
            //    'action' => "{$namespace}@serverVars",
            //    'middleware' => [\PragmaRX\Health\Http\Middleware\LocallyProtected::class],
            //],
        ],
    ],

    'urls' => [
        'panel' => '/health/panel',
    ],
];
