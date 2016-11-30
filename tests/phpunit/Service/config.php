<?php

return [

    'title' => 'Laravel Health Check Panel',

    'sort_by' => 'slug',

    'cache' => [
        'minutes' => 1, // false = disabled
        'key' => 'health-resources',
    ],

    'notifications' => [
        'enabled' => false,

        'notify_on' => [
            'panel' => false,
            'check' => true,
            'string' => true,
            'resource' => false,
        ],

        'action-title' => 'View App Health',

        'action-message' => "The '%s' service is in trouble and needs attention%s",

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

            'emails' => [
                'admin@mydomain.com',
            ],
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
            'message' => 'Everything is fine with this resource',
            'type' => 'success',
        ],

        'error' => [
            'type' => 'error',
        ],
    ],

    'style' => [
        'button_lines' => 'multi', // multi or single

        'multiplier' => 0.7,
    ],

    'views' => [
        'panel' => 'pragmarx/health::default.panel',

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
        'panel' => 'PragmaRX\Health\Http\Controllers\Health@panel',
        'check' => 'PragmaRX\Health\Http\Controllers\Health@check',
        'string' => 'PragmaRX\Health\Http\Controllers\Health@string',
        'resource' => 'PragmaRX\Health\Http\Controllers\Health@resource',
    ],

    'routes' => [
        'prefix' => '/health',

        'suffixes' => [
            'panel' => '/panel',
            'check' => '/check',
            'string' => '/string',
            'resource' => '/resource',
        ],

        'notification' => 'pragmarx.health.panel',
    ],

    'urls' => [
        'panel' => '/health/panel',
    ],

    'resources' => [
        'health' => [
            'abbreviation' => 'hlth',
            'columnSize' => '12',
            'checker' => PragmaRX\Health\Checkers\HealthChecker::class,
            'is_global' => true,
            'notify' => false,
            'error_message' => 'At least one resource failed the health check.',
        ],

        'database' => [
            'abbreviation' => 'db',
            'columnSize' => '6',
            'checker' => PragmaRX\Health\Checkers\DatabaseChecker::class,
            'notify' => true,
            'models' => [
                App\User::class,
            ],
        ],

        'cache' => [
            'abbreviation' => 'csh',
            'columnSize' => '6',
            'checker' => PragmaRX\Health\Checkers\CacheChecker::class,
            'notify' => true,
            'error_message' => 'Cache is not returning cached values.',
            'key' => 'health-cache-test',
            'minutes' => 1,
        ],

        'framework' => [
            'abbreviation' => 'frmwrk',
            'columnSize' => '6',
            'checker' => PragmaRX\Health\Checkers\FrameworkChecker::class,
            'notify' => true,
        ],

        'https' => [
            'abbreviation' => 'https',
            'columnSize' => '6',
            'checker' => PragmaRX\Health\Checkers\HttpsChecker::class,
            'notify' => true,
            'url' => config('app.url'),
        ],

        'http' => [
            'abbreviation' => 'http',
            'columnSize' => '6',
            'checker' => PragmaRX\Health\Checkers\HttpChecker::class,
            'notify' => true,
            'url' => config('app.url'),
        ],

        'mail' => [
            'abbreviation' => 'ml',
            'columnSize' => '6',
            'checker' => PragmaRX\Health\Checkers\MailChecker::class,
            'notify' => true,
            'view' => 'pragmarx/health::default.email',
            'config' => [
                'driver' => 'log',

                'host' => env('MAIL_HOST', 'smtp.mailgun.org'),

                'port' => env('MAIL_PORT', 587),

                'from' => [
                    'address' => 'health@example.com',
                    'name' => 'Health Checker',
                ],

                'encryption' => env('MAIL_ENCRYPTION', 'tls'),

                'username' => env('MAIL_USERNAME'),

                'password' => env('MAIL_PASSWORD'),

                'sendmail' => '/usr/sbin/sendmail -bs',
            ],
            'to' => 'you-know-who@sink.sendgrid.net',
            'subject' => 'Health Test mail',
        ],

        'filesystem' => [
            'abbreviation' => 'flstm',
            'columnSize' => '6',
            'checker' => PragmaRX\Health\Checkers\FilesystemChecker::class,
            'notify' => true,
            'error-message' => 'Unable to create temp file: %s.',
        ],

        'cloud_storage' => [
            'abbreviation' => 'cld',
            'columnSize' => '6',
            'checker' => PragmaRX\Health\Checkers\CloudStorageChecker::class,
            'notify' => true,
            'driver' => 'local',
            'file' => 'testfile-'.Illuminate\Support\Str::random(32).'.txt',
            'contents' => Illuminate\Support\Str::random(1024),
            'error_message' => 'Cloud storage is not retrieving files correctly.',
        ],
    ],

];
