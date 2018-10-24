<?php

namespace PragmaRX\Health\Tests\PhpUnit;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use PragmaRX\Health\ServiceProvider as HealthServiceProvider;

class TestCase extends OrchestraTestCase
{
	const RESOURCES_HEALTHY_EVERYWHERE = 8;

    const ALL_RESOURCES = [
        'AppKey',
        'Broadcasting',
        'Cache',
        'ConfigurationCached',
        'Database',
        'DebugMode',
        'DirectoryPermissions',
        'DiskSpace',
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
        'ServerLoad',
        'ServerUptime',
        'Sshd',
        'Supervisor',
    ];

    const RESOURCES_HEALTHY = 18;

    const RESOURCES_FAILING = [
        'AppKey',
        'Broadcasting',
        'Cache',
        'ConfigurationCached',
        'Database',
        'DebugMode',
        'DirectoryPermissions',
        'DiskSpace',
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
        'ServerLoad',
        'ServerUptime',
        'Sshd',
        'Supervisor',
    ];

    const RESOURCES_STRING = 'appkeyFAIL-brdcFAIL-cshOK-cfgcchFAIL-dbFAIL-debugOK-dirpermOK-dskspcOK-dcsgnFAIL-redisconnFAIL-envexistsFAIL-flstmOK-frmwrkOK-httpFAIL-httpsFAIL-lvsOK-latencyFAIL-lclstrgOK-mlOK-redisconnOK-redisconnOK-debugFAIL-msqlOK-mysqlgrsqlsrvrconnOK-nwrlcdmnFAIL-ngnxsrvrFAIL-debugFAIL-pkgupdtdOK-phpOK-pstgrsqlsrvrconnOK-pstgrsqlsrvrFAIL-queueOK-qwrkrsOK-rbtrqrdOK-rdsOK-redisconnOK-rdssrvrOK-rtcchFAIL-s3FAIL-loadFAIL-uptmOK-sshdFAIL-sprvsrOK';

    protected function getPackageProviders($app)
    {
        $app['config']->set('health.resources.enabled', static::ALL_RESOURCES);

        return [HealthServiceProvider::class];
    }
}
