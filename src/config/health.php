<?php

return [
    'title' => 'Laravel Health Check Panel',

    'resources' => [
        'path' => config_path('health/resources'),

        'enabled' => PragmaRX\Health\Support\Constants::RESOURCES_ENABLED_ALL,
    ],

    'sort_by' => 'slug',

    'cache' => [
        // forever = 0
        // false = disabled
        // default = 1 minute
        // in debug mode defautls to "disabled"
        'minutes' => config('app.debug') === true ? false : 1,

        'key' => 'health-resources',
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

        'action-title' => 'View App Health',

        'action_message' => "The '%s' service is in trouble and needs attention%s",

        'from' => [
            'name' => 'Laravel Health Checker',

            'address' => 'healthchecker@mydomain.com',

            'icon_emoji' => ':anger:',
        ],

        'scheduler' => [
            'enabled' => true,

            'frequency' => 'everyMinute', // most methods on -- https://laravel.com/docs/5.3/scheduling#defining-schedules
        ],

        'users' => [
            'model' => App\User::class,

            'emails' => ['admin@mydomain.com'],
        ],

        'channels' => ['mail', 'slack'], // mail, slack

        'notifier' => 'PragmaRX\Health\Notifications',
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
        'fail' => 'FAIL',
    ],

    'routes' => [
        'prefix' => $route_prefix = '/health',

        'namespace' => $namespace = 'PragmaRX\Health\Http\Controllers\Health',

        'notification' => 'pragmarx.health.panel',

        'list' => [
            [
                'uri' => "{$route_prefix}/panel",
                'name' => "pragmarx.health.panel",
                'action' => "{$namespace}@panel",
                'middleware' => [
                    /*'auth.basic'*/
                ],
            ],

            [
                'uri' => "{$route_prefix}/check",
                'name' => "pragmarx.health.check",
                'action' => "{$namespace}@check",
                'middleware' => [],
            ],

            [
                'uri' => "{$route_prefix}/string",
                'name' => "pragmarx.health.string",
                'action' => "{$namespace}@string",
                'middleware' => [],
            ],

            [
                'uri' => "{$route_prefix}/resources",
                'name' => "pragmarx.health.resources.all",
                'action' => "{$namespace}@allResources",
                'middleware' => [],
            ],

            [
                'uri' => "{$route_prefix}/resources/{slug}",
                'name' => "pragmarx.health.resources.get",
                'action' => "{$namespace}@getResource",
                'middleware' => [],
            ],

            [
                'uri' => "{$route_prefix}/assets/css/app.css",
                'name' => "pragmarx.health.assets.css",
                'action' => "{$namespace}@assetAppCss",
                'middleware' => [],
            ],

            [
                'uri' => "{$route_prefix}/assets/js/app.js",
                'name' => "pragmarx.health.assets.js",
                'action' => "{$namespace}@assetAppJs",
                'middleware' => [],
            ],

            [
                'uri' => "{$route_prefix}/config",
                'name' => "pragmarx.health.config",
                'action' => "{$namespace}@config",
                'middleware' => [],
            ],
        ],
    ],

    'urls' => [
        'panel' => '/health/panel',
    ],
];
