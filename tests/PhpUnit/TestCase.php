<?php

namespace PragmaRX\Health\Tests\PhpUnit;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use PragmaRX\Health\ServiceProvider as HealthServiceProvider;

class TestCase extends OrchestraTestCase
{
    const RESOURCES_HEALTHY_EVERYWHERE = 8;

    const ALL_RESOURCES = [
        'API',
        'AppKey',
        'Adyen',
        'Broadcasting',
        'Cache',
        'Certificate',
        'CheckoutCom',
        'ConfigurationCached',
        'Database',
        'DebugMode',
        'DirectoryPermissions',
        'DiskSpace',
        'Dynamics',
        'DocuSign',
        'ElasticsearchConnectable',
        'EnvExists',
        'Filesystem',
        'Framework',
        'Horizon',
        'Http',
        'Https',
        'LaravelServices',
        'Latency',
        'LocalStorage',
        'Mail',
        'MailgunConnectable',
        'MemcachedConnectable',
        'MigrationsUpToDate',
        'MySql',
        'MySqlConnectable',
        'NewrelicDeamon',
        'NginxServer',
        'PackagesUpToDate',
        'Php',
        'PostgreSqlConnectable',
        'PostgreSqlServer',
        'Queue',
        'QueueWorkers',
        'RebootRequired',
        'Redis',
        'RedisConnectable',
        'RedisServer',
        'RoutesCached',
        'S3',
        'SecurityChecker',
        'SeeTickets',
        'Sendinblue',
        'ServerLoad',
        'ServerUptime',
        'Sshd',
        'Supervisor',
    ];

    protected function getPackageProviders($app)
    {
        $app['config']->set('health.resources.enabled', static::ALL_RESOURCES);

        return [HealthServiceProvider::class];
    }
}
