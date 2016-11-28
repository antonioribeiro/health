<?php

return [

    'title' => 'Laravel Health Check Panel',

    'unhealthy_message' => 'At least one resource failed the health check.',

    'sort_by' => 'slug',

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

            'icon_url' => ':anger:',
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

    'style' => [
        'button_lines' => 'single', // multi or single

        'multiplier' => 0.33,
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
            'columnSize' => '3',
            'checker' => PragmaRX\Health\Checkers\DatabaseChecker::class,
            'notify' => true,
        ],

        'framework' => [
            'abbreviation' => 'frmwrk',
            'columnSize' => '3',
            'checker' => PragmaRX\Health\Checkers\FrameworkChecker::class,
            'notify' => true,
        ],

        'https' => [
            'abbreviation' => 'https',
            'columnSize' => '3',
            'checker' => PragmaRX\Health\Checkers\HttpsChecker::class,
            'notify' => true,
        ],

        'http' => [
            'abbreviation' => 'http',
            'columnSize' => '3',
            'checker' => PragmaRX\Health\Checkers\HttpChecker::class,
            'notify' => true,
        ],

        'mail' => [
            'abbreviation' => 'mail',
            'columnSize' => '3',
            'checker' => PragmaRX\Health\Checkers\MailChecker::class,
            'notify' => true,
        ],

        'filesystem' => [
            'abbreviation' => 'filesystem',
            'columnSize' => '3',
            'checker' => PragmaRX\Health\Checkers\FilesystemChecker::class,
            'notify' => true,
        ],

        // ----------------------------------------

//        'forge' => [
//            'abbreviation' => 'filesystem',
//            'columnSize' => '3',
//            'checker' => PragmaRX\Health\Checkers\FrameworkChecker::class,
//            'notify' => true,
//        ],
//
//        'envoyer' => [
//            'abbreviation' => 'filesystem',
//            'columnSize' => '3',
//            'checker' => PragmaRX\Health\Checkers\FrameworkChecker::class,
//            'notify' => true,
//        ],
//
//        'redis' => [
//            'abbreviation' => 'db',
//            'columnSize' => '3',
//            'checker' => PragmaRX\Health\Checkers\FrameworkChecker::class,
//            'notify' => true,
//        ],
//
//        'queue' => [
//            'abbreviation' => 'frmwrk',
//            'columnSize' => '3',
//            'checker' => PragmaRX\Health\Checkers\FrameworkChecker::class,
//            'notify' => true,
//        ],
//
//        'mongo' => [
//            'abbreviation' => 'https',
//            'columnSize' => '3',
//            'checker' => PragmaRX\Health\Checkers\FrameworkChecker::class,
//            'notify' => true,
//        ],
//
//        'mysql' => [
//            'abbreviation' => 'http',
//            'columnSize' => '3',
//            'checker' => PragmaRX\Health\Checkers\FrameworkChecker::class,
//            'notify' => true,
//        ],
//
//        'postgresql' => [
//            'abbreviation' => 'mail',
//            'columnSize' => '3',
//            'checker' => PragmaRX\Health\Checkers\FrameworkChecker::class,
//            'notify' => true,
//        ],
//
//        's3' => [
//            'abbreviation' => 'filesystem',
//            'columnSize' => '3',
//            'checker' => PragmaRX\Health\Checkers\FrameworkChecker::class,
//            'notify' => true,
//        ],
//
//        'sqs' => [
//            'abbreviation' => 'filesystem',
//            'columnSize' => '3',
//            'checker' => PragmaRX\Health\Checkers\FrameworkChecker::class,
//            'notify' => true,
//        ],
//
//        'slack' => [
//            'abbreviation' => 'filesystem',
//            'columnSize' => '3',
//            'checker' => PragmaRX\Health\Checkers\FrameworkChecker::class,
//            'notify' => true,
//        ],
//
//        'twilio' => [
//            'abbreviation' => 'filesystem',
//            'columnSize' => '3',
//            'checker' => PragmaRX\Health\Checkers\FrameworkChecker::class,
//            'notify' => true,
//        ],
//
//        'github' => [
//            'abbreviation' => 'filesystem',
//            'columnSize' => '3',
//            'checker' => PragmaRX\Health\Checkers\FrameworkChecker::class,
//            'notify' => true,
//        ],
//
//        'digital_ocean' => [
//            'abbreviation' => 'filesystem',
//            'columnSize' => '3',
//            'checker' => PragmaRX\Health\Checkers\FrameworkChecker::class,
//            'notify' => true,
//        ],
//
//        'jobs' => [
//            'abbreviation' => 'filesystem',
//            'columnSize' => '3',
//            'checker' => PragmaRX\Health\Checkers\FrameworkChecker::class,
//            'notify' => true,
//        ],
//
//        'loggly' => [
//            'abbreviation' => 'filesystem',
//            'columnSize' => '3',
//            'checker' => PragmaRX\Health\Checkers\FrameworkChecker::class,
//            'notify' => true,
//        ],
//
//        'zabbix' => [
//            'abbreviation' => 'filesystem',
//            'columnSize' => '3',
//            'checker' => PragmaRX\Health\Checkers\FrameworkChecker::class,
//            'notify' => true,
//        ],
//
//        'cassandra' => [
//            'abbreviation' => 'filesystem',
//            'columnSize' => '3',
//            'checker' => PragmaRX\Health\Checkers\FrameworkChecker::class,
//            'notify' => true,
//        ],
//
//        'heroku' => [
//            'abbreviation' => 'filesystem',
//            'columnSize' => '3',
//            'checker' => PragmaRX\Health\Checkers\FrameworkChecker::class,
//            'notify' => true,
//        ],
//
//        'dropbox' => [
//            'abbreviation' => 'filesystem',
//            'columnSize' => '3',
//            'checker' => PragmaRX\Health\Checkers\FrameworkChecker::class,
//            'notify' => true,
//        ],
//
//        'google_cloud_storage' => [
//            'abbreviation' => 'filesystem',
//            'columnSize' => '3',
//            'checker' => PragmaRX\Health\Checkers\FrameworkChecker::class,
//            'notify' => true,
//        ],
//
//        'bugsnag' => [
//            'abbreviation' => 'filesystem',
//            'columnSize' => '3',
//            'checker' => PragmaRX\Health\Checkers\FrameworkChecker::class,
//            'notify' => true,
//        ],
//
//        'papertrail' => [
//            'abbreviation' => 'filesystem',
//            'columnSize' => '3',
//            'checker' => PragmaRX\Health\Checkers\FrameworkChecker::class,
//            'notify' => true,
//        ],
//
//        'mailchimp' => [
//            'abbreviation' => 'filesystem',
//            'columnSize' => '3',
//            'checker' => PragmaRX\Health\Checkers\FrameworkChecker::class,
//            'notify' => true,
//        ],
//
//        'mailgun' => [
//            'abbreviation' => 'filesystem',
//            'columnSize' => '3',
//            'checker' => PragmaRX\Health\Checkers\FrameworkChecker::class,
//            'notify' => true,
//        ],
//
//        'newrelic' => [
//            'abbreviation' => 'filesystem',
//            'columnSize' => '3',
//            'checker' => PragmaRX\Health\Checkers\FrameworkChecker::class,
//            'notify' => true,
//        ],
//
//        'elastic_search' => [
//            'abbreviation' => 'filesystem',
//            'columnSize' => '3',
//            'checker' => PragmaRX\Health\Checkers\FrameworkChecker::class,
//            'notify' => true,
//        ],
//
//        'memory' => [
//            'abbreviation' => 'filesystem',
//            'columnSize' => '3',
//            'checker' => PragmaRX\Health\Checkers\FrameworkChecker::class,
//            'notify' => true,
//        ],
//
//        'disk' => [
//            'abbreviation' => 'filesystem',
//            'columnSize' => '3',
//            'checker' => PragmaRX\Health\Checkers\FrameworkChecker::class,
//            'notify' => true,
//        ],
    ],

];
