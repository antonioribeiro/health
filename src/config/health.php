<?php

return [
    'title' => 'Laravel Health Check Panel',

    'resources' => [
        'path' => config_path('health/resources'),

        'enabled' => PragmaRX\Health\Support\Constants::RESOURCES_ENABLED_ALL,
    ],

    'sort_by' => 'slug',

    'cache' => [
        'minutes' => config('app.debug') ? false : true, // false = disabled

        'key' => 'health-resources',
    ],

    'database' => [
        'enabled' => false,

        'graphs' => [
            'enabled' => true,

            'height' => 90,
        ],

        'max_records' => 30,
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
        'enabled' => true,

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

        'name_prefix' => $name_prefix = 'pragmarx.health',

        'notification' => 'pragmarx.health.panel',

        'list' => [
            [
                'uri' => "{$route_prefix}/panel",
                'name' => "{$name_prefix}.panel",
                'action' => "{$namespace}@panel",
                'middleware' => [
                    /*'auth.basic'*/
                ],
            ],

            [
                'uri' => "{$route_prefix}/check",
                'name' => "{$name_prefix}.check",
                'action' => "{$namespace}@check",
                'middleware' => [],
            ],

            [
                'uri' => "{$route_prefix}/string",
                'name' => "{$name_prefix}.string",
                'action' => "{$namespace}@string",
                'middleware' => [],
            ],

            [
                'uri' => "{$route_prefix}/resources",
                'name' => "{$name_prefix}.resources.all",
                'action' => "{$namespace}@allResources",
                'middleware' => [],
            ],

            [
                'uri' => "{$route_prefix}/resources/{slug}",
                'name' => "{$name_prefix}.resources.get",
                'action' => "{$namespace}@getResource",
                'middleware' => [],
            ],

            [
                'uri' => "{$route_prefix}/assets/css/app.css",
                'name' => "{$name_prefix}.assets.css",
                'action' => "{$namespace}@assetAppCss",
                'middleware' => [],
            ],

            [
                'uri' => "{$route_prefix}/assets/js/app.js",
                'name' => "{$name_prefix}.assets.js",
                'action' => "{$namespace}@assetAppJs",
                'middleware' => [],
            ],

            [
                'uri' => "{$route_prefix}/config",
                'name' => "{$name_prefix}.config",
                'action' => "{$namespace}@config",
                'middleware' => [],
            ],
        ],
    ],

    'urls' => [
        'panel' => '/health/panel',
    ],
];
