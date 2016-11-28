<?php

return [

    'title' => 'Laravel Health Check Panel',

    'unhealthy_message' => 'At least one resource failed the health check.',

    'sort_by' => 'slug',

    'cache' => false, // minutes / false = do not cache / 0 = forever

    'notifications' => [
        'enabled' => true,

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
            'type' => 'success',
        ]
    ],

    'style' => [
        'button_lines' => 'multi', // multi or single

        'multiplier' => 1,
    ],

    'views' => [
        'panel' => 'pragmarx/health::default.panel',

        'partials' => [
            'well' => 'pragmarx/health::default.partials.well',
        ],

        'email' => 'pragmarx/health::default.email',
    ],

    'string' => [
        'glue' => '-',
        'ok' => 'OK',
        'fail' => 'FAIL',
    ],

    'error-messages' => [
        'tempfile' => 'Unable to create temp file: %s.'
    ],

    'database' => [
        'models' => [
            App\User::class,
        ],
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
        'http' => config('app.url'),
        'https' => config('app.url'),
        'panel' => '/health/panel',
    ],

    'mail' => [
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

	'resources' => [
        'health' => [
            'abbreviation' => 'hlth',
            'columnSize' => '12',
            'checker' => PragmaRX\Health\Checkers\HealthChecker::class,
            'is_global' => true,
            'notify' => false,
        ],

        'database' => [
            'abbreviation' => 'db',
            'columnSize' => '6',
            'checker' => PragmaRX\Health\Checkers\DatabaseChecker::class,
            'notify' => true,
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
        ],

        'http' => [
            'abbreviation' => 'http',
            'columnSize' => '6',
            'checker' => PragmaRX\Health\Checkers\HttpChecker::class,
            'notify' => true,
        ],

        'mail' => [
            'abbreviation' => 'mail',
            'columnSize' => '6',
            'checker' => PragmaRX\Health\Checkers\MailChecker::class,
            'notify' => true,
        ],

        'filesystem' => [
            'abbreviation' => 'filesystem',
            'columnSize' => '6',
            'checker' => PragmaRX\Health\Checkers\FilesystemChecker::class,
            'notify' => true,
        ],

    ],

];
