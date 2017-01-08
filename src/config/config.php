<?php

return [

    'title' => 'Laravel Health Check Panel',

    'sort_by' => 'slug',

    'cache' => [
        'minutes' => 1, // false = disabled
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
//        'health' => [
//            'abbreviation' => 'hlth',
//            'columnSize' => '12',
//            'checker' => PragmaRX\Health\Checkers\HealthChecker::class,
//            'is_global' => true,
//            'notify' => false,
//            'error_message' => 'At least one resource failed the health check.',
//        ],
//
//        'database' => [
//            'abbreviation' => 'db',
//            'columnSize' => '6',
//            'checker' => PragmaRX\Health\Checkers\DatabaseChecker::class,
//            'notify' => true,
//            'models' => [
//                App\User::class,
//            ],
//        ],
//
//        'cache' => [
//            'abbreviation' => 'csh',
//            'columnSize' => '6',
//            'checker' => PragmaRX\Health\Checkers\CacheChecker::class,
//            'notify' => true,
//            'error_message' => 'Cache is not returning cached values.',
//            'key' => 'health-cache-test',
//            'minutes' => 1,
//        ],
//
//        'framework' => [
//            'abbreviation' => 'frmwrk',
//            'columnSize' => '6',
//            'checker' => PragmaRX\Health\Checkers\FrameworkChecker::class,
//            'notify' => true,
//        ],
//
//        'https' => [
//            'abbreviation' => 'https',
//            'columnSize' => '6',
//            'checker' => PragmaRX\Health\Checkers\HttpsChecker::class,
//            'notify' => true,
//            'url' => config('app.url'),
//        ],
//
//        'http' => [
//            'abbreviation' => 'http',
//            'columnSize' => '6',
//            'checker' => PragmaRX\Health\Checkers\HttpChecker::class,
//            'notify' => true,
//            'url' => config('app.url'),
//        ],
//
//        'mail' => [
//            'abbreviation' => 'ml',
//            'columnSize' => '6',
//            'checker' => PragmaRX\Health\Checkers\MailChecker::class,
//            'notify' => true,
//            'view' => 'pragmarx/health::default.email',
//            'config' => [
//                'driver' => 'log',
//
//                'host' => env('MAIL_HOST', 'smtp.mailgun.org'),
//
//                'port' => env('MAIL_PORT', 587),
//
//                'from' => [
//                    'address' => 'health@example.com',
//                    'name' => 'Health Checker',
//                ],
//
//                'encryption' => env('MAIL_ENCRYPTION', 'tls'),
//
//                'username' => env('MAIL_USERNAME'),
//
//                'password' => env('MAIL_PASSWORD'),
//
//                'sendmail' => '/usr/sbin/sendmail -bs',
//            ],
//            'to' => 'you-know-who@sink.sendgrid.net',
//            'subject' => 'Health Test mail',
//        ],
//
//        'filesystem' => [
//            'abbreviation' => 'flstm',
//            'columnSize' => '6',
//            'checker' => PragmaRX\Health\Checkers\FilesystemChecker::class,
//            'notify' => true,
//            'error-message' => 'Unable to create temp file: %s.',
//        ],
//
//        'cloud_storage' => [
//            'abbreviation' => 'cld',
//            'columnSize' => '6',
//            'checker' => PragmaRX\Health\Checkers\CloudStorageChecker::class,
//            'notify' => true,
//            'driver' => 'local',
//            'file' => 'testfile-'.Illuminate\Support\Str::random(32).'.txt',
//            'contents' => Illuminate\Support\Str::random(1024),
//            'error_message' => 'Cloud storage is not retrieving files correctly.',
//        ],
//
//        'queue' => [
//            'abbreviation' => 'queue',
//            'name' => 'health-queue',
//            'cache_instance' => 'cache',
//            'test_job' => PragmaRX\Health\Support\Jobs\TestJob::class,
//            'columnSize' => '6',
//            'checker' => PragmaRX\Health\Checkers\QueueChecker::class,
//            'notify' => true,
//            'connection' => '',
//            'error_message' => 'Queue system is not working properly.',
//        ],
//
//        'redis' => [
//            'abbreviation' => 'rds',
//            'key' => 'health:redis:key',
//            'columnSize' => '6',
//            'checker' => PragmaRX\Health\Checkers\RedisChecker::class,
//            'notify' => true,
//            'connection' => '',
//            'error_message' => 'Got a wrong value back from Redis.',
//        ],
//
//        'serverUptime' => [
//            'abbreviation' => 'uptm',
//            'columnSize' => '6',
//            'regex' => $uptimeRegex = '~(?<time_hour>\d{1,2}):(?<time_minute>\d{2})(?::(?<time_second>\d{2}))?\s+up\s+(?:(?<up_days>\d+)\s+days?,\s+)?\b(?:(?<up_hours>\d+):)?(?<up_minutes>\d+)(?:\s+(?:minute|minutes|min)?)?,\s+(?<users>\d+).+?(?<load_1>\d+.\d+),?\s+(?<load_5>\d+.\d+),?\s+(?<load_15>\d+.\d+)~',
//            'checker' => PragmaRX\Health\Checkers\ServerUptimeChecker::class,
//            'command' => 'uptime 2>&1',
//            'save_to' => $path.'/uptime.json',
//            'notify' => true,
//            'action_message' => 'Your server was rebooted (Uptime Checker)',
//            'error_message' => 'Looks like your server was recently rebooted, current uptime is now "%s" and it was "%s" before restart.',
//        ],
//
//        'serverLoad' => [
//            'abbreviation' => 'load',
//            'columnSize' => '6',
//            'regex' => $uptimeRegex,
//            'checker' => PragmaRX\Health\Checkers\ServerLoadChecker::class,
//            'command' => 'uptime 2>&1',
//            'max_load' => [
//                'load_1' => 2,
//                'load_5' => 1.5,
//                'load_15' => 1,
//            ],
//            'notify' => true,
//            'action_message' => 'Too much load! (Server Load Checker)',
//            'error_message' => 'Your server might be overloaded, current server load values are "%s, %s and %s", which are above the threshold values: "%s, %s and %s".',
//        ],

        'broadcasting' => [
            'abbreviation' => 'brdc',
            'columnSize' => '6',
            'channel' => 'pragmarx-health-broadcasting-channel',
            'checker' => PragmaRX\Health\Checkers\BroadcastingChecker::class,
            'route_name' => $routeName = 'pragmarx.health.broadcasting.callback',
            'secret' => str_random(),
            'timeout' => 30,
            'routes' => [
                $routeName => [
                    'uri' => '/health/broadcasting/callback/{secret}',
                    'controller' => PragmaRX\Health\Http\Controllers\Broadcasting::class,
                    'action' => 'callback',
                ],
            ],
            'save_to' => $path.'/broadcasting.json',
            'notify' => true,
            'error_message' => 'The broadcasting service did not respond in time, it may be in trouble.',
        ],

    ],

];
