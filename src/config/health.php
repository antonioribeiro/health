<?php

return [
    'title' => 'Laravel Health Check Panel',

    'resources' => [
        'path' => config_path('health/resources'),

        'enabled' => PragmaRX\Health\Support\Constants::RESOURCES_ENABLED_ALL,
    ],

    'columns' => [
        'default_size' => 2,
    ],

    'sort_by' => 'slug',

    'cache' => [
        'minutes' => config('app.debug') ? false : true, // false = disabled

        'key' => 'health-resources',
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

        'action_message' =>
            "The '%s' service is in trouble and needs attention%s",

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

        'channels' => [
            'mail' => [
                'enabled' => true,
                'sender' => PragmaRX\Health\Notifications\Channels\Mail::class,
            ],

            'slack' => [
                'enabled' => true,
                'sender' => PragmaRX\Health\Notifications\Channels\Slack::class,
            ],

            'facebook' => [
                'enabled' => false,
            ],
        ],

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
        'button_lines' => 'multi', // multi or single

        'multiplier' => 0.4,
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

    'actions' => [
        'panel' =>
            $action_panel = 'PragmaRX\Health\Http\Controllers\Health@panel',
        'check' =>
            $action_check = 'PragmaRX\Health\Http\Controllers\Health@check',
        'string' =>
            $action_string = 'PragmaRX\Health\Http\Controllers\Health@string',
        'resource' =>
            $action_resource =
                'PragmaRX\Health\Http\Controllers\Health@resource',
    ],

    'routes' => [
        'prefix' => $route_prefix = '/health',

        'suffixes' => [
            'panel' => $route_suffix_panel = '/panel',
            'check' => $route_suffix_check = '/check',
            'string' => $route_suffix_string = '/string',
            'resource' => $route_suffix_resource = '/resource',
        ],

        'notification' => 'pragmarx.health.panel',

        'list' => [
            [
                'uri' => $route_prefix . $route_suffix_panel,
                'name' => 'pragmarx.health.panel',
                'action' => $action_panel,
                'middleware' => [
                    /*'auth.basic'*/
                ],
            ],

            [
                'uri' => $route_prefix . $route_suffix_check,
                'name' => 'pragmarx.health.check',
                'action' => $action_check,
                'middleware' => [],
            ],

            [
                'uri' => $route_prefix . $route_suffix_string,
                'name' => 'pragmarx.health.string',
                'action' => $action_string,
                'middleware' => [],
            ],

            [
                'uri' => "{$route_prefix}{$route_suffix_resource}/{slug}",
                'name' => 'pragmarx.health.resource',
                'action' => $action_resource,
                'middleware' => [],
            ],
        ],
    ],

    'urls' => [
        'panel' => '/health/panel',
    ],
];
