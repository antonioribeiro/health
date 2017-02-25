<?php

namespace PragmaRX\Health\Tests\PhpUnit\Service;

use PragmaRX\Health\Commands;
use Illuminate\Support\Collection;
use PragmaRX\Health\Facade as Health;
use PragmaRX\Health\Tests\PhpUnit\TestCase;
use PragmaRX\Health\Http\Controllers\Health as HealthController;

class ServiceTest extends TestCase
{
    const RESOURCES_HEALTHY_EVERYWHERE = 8;

    const ALL_RESOURCES = [
        'health',
        'broadcasting',
        'cache',
        'database',
        'docusign',
        'filesystem',
        'framework',
        'http',
        'https',
        'laravelservices',
        'localstorage',
        'mail',
        'mysql',
        'newrelicdeamon',
        'nginxserver',
        'php',
        'postgresqlserver',
        'queue',
        'queueworkers',
        'rebootrequired',
        'redis',
        'redisserver',
        's3',
        'serverload',
        'serveruptime',
        'sshd',
        'supervisor',
    ];

    const RESOURCES_FAILING = [
        'Health',
        'Broadcasting',
        'Database',
        'DocuSign',
        'Http',
        'Https',
        'NewrelicDeamon',
        'Redis',
        'S3',
        'NginxServer',
        'Php',
        'PostgreSqlServer',
        'ServerLoad',
    ];

    /**
     * @var \PragmaRX\Health\Service
     */
    private $service;

    /**
     * @var \Illuminate\Support\Collection
     */
    private $resources;

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $this->app = $app;

        $this->app['config']->set('health.resources_location.path', package_resources_dir());
    }

    public function setUp()
    {
        parent::setUp();

        $this->service = app('pragmarx.health');

        $this->resources = $this->service->checkResources();
    }

    public function testResourcesWhereChecked()
    {
        $this->checkedResources($this->resources);
    }

    public function checkedResources($resources)
    {
        $healthCount = $resources->reduce(function ($carry, $item) {
            return $carry + (isset($item['health']['healthy'])
                    ? 1
                    : 0);
        }, 0);

        $this->assertEquals(count(static::ALL_RESOURCES), $healthCount);

        $failing = $resources
            ->filter(function ($item) {
                return $item['health']['healthy'];
            });

        $this->assertGreaterThanOrEqual(static::RESOURCES_HEALTHY_EVERYWHERE, $failing->count());
    }

    public function testInstantiation()
    {
        $this->assertInstanceOf(Collection::class, $this->resources);
    }

    public function testConfigWasLoadedProperly()
    {
        $this->assertEquals(
            $this->resources['Health']['error_message'],
            'At least one resource failed the health check.'
        );
    }

    public function testResourcesHasTheCorrectCount()
    {
        $this->assertCount(count(static::ALL_RESOURCES), $this->resources->toArray());
    }

    public function testResourcesItemsMatchConfig()
    {
        $this->assertEquals(
            static::ALL_RESOURCES,
            $this->resources->keys()->map(function ($value) {
                return strtolower($value);
            })->toArray()
        );
    }

    public function test_artisan_commands()
    {
        $commands = [
            'panel',
            'check',
            'export',
            'publish',
        ];

        foreach ($commands as $command) {
            (new Commands($this->service))->$command();
        }
    }

    public function test_controller()
    {
        $controller = new HealthController($this->service);

        $this->checkedResources(collect(json_decode($controller->check()->getContent(), true)));

        foreach ($this->resources as $key => $resource) {
            $this->assertEquals($controller->resource($key)['name'], $key);
        }

        $string = $controller->string()->getContent();

        $this->assertTrue(strpos($string, config('health.string.ok').'-') !== false);

        $this->assertTrue(strpos($string, config('health.string.fail').'-') !== false);

        $this->assertTrue(strpos($controller->panel()->getContent(), '<title>'.config('health.title').'</title>') !== false);
    }
}
