<?php

namespace PragmaRX\Health\Tests\PhpUnit\Service;

use PragmaRX\Health\Commands;
use Illuminate\Support\Collection;
use PragmaRX\Health\Facade as Health;
use PragmaRX\Health\Support\ResourceLoader;
use PragmaRX\Health\Support\Yaml;
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

    private function getResources($force = false)
    {
        if ($force || ! $this->resources) {
            $this->resources = $this->service->checkResources($force);
        }

        return $this->resources;
    }

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
    }

    public function testResourcesWhereChecked()
    {
        $this->assertCheckedResources($this->getResources());
    }

    public function test_cache_flush()
    {
        $this->assertCheckedResources(
            $this->getResources(true)
        );
    }

    public function test_load_array()
    {
        $this->app['config']->set('health.resources_location.type', \PragmaRX\Health\Support\Constants::RESOURCES_TYPE_ARRAY);

        $this->assertCheckedResources(
            $this->getResources(true)
        );
    }

    public function test_load_files()
    {
        $this->app['config']->set('health.resources_location.type', \PragmaRX\Health\Support\Constants::RESOURCES_TYPE_FILES);

        $this->assertCheckedResources(
            $this->getResources(true)
        );
    }

    public function test_unsorted()
    {
        $this->app['config']->set('health.sort_by', null);

        $this->assertCheckedResources(
            $this->getResources(true)
        );
    }

    public function test_invalid_enabled_resources()
    {
        $this->expectException(\DomainException::class);

        $this->app['config']->set('health.resources_enabled', 'invalid');

        (new ResourceLoader(new Yaml()))->load();

        $this->getResources(true);
    }

    public function test_invalid_load_one_resource()
    {
        $this->app['config']->set('health.resources_enabled', ['Database']);

        $resource = (new ResourceLoader(new Yaml()))->load();

        $this->assertTrue($resource->first()['name'] == 'Database');
    }

    public function assertCheckedResources($resources)
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
        $this->assertInstanceOf(Collection::class, $this->getResources());
    }

    public function testConfigWasLoadedProperly()
    {
        $resources = $this->getResources();

        $this->assertEquals(
            $resources['Health']['error_message'],
            'At least one resource failed the health check.'
        );
    }

    public function testResourcesHasTheCorrectCount()
    {
        $this->assertCount(count(static::ALL_RESOURCES), $this->getResources()->toArray());
    }

    public function testResourcesItemsMatchConfig()
    {
        $this->assertEquals(
            static::ALL_RESOURCES,
            $this->getResources()->keys()->map(function ($value) {
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

        $this->assertCheckedResources(collect(json_decode($controller->check()->getContent(), true)));

        foreach ($this->getResources() as $key => $resource) {
            $this->assertEquals($controller->resource($key)['name'], $key);
        }

        $string = $controller->string()->getContent();

        $this->assertTrue(strpos($string, config('health.string.ok').'-') !== false);

        $this->assertTrue(strpos($string, config('health.string.fail').'-') !== false);

        $this->assertTrue(strpos($controller->panel()->getContent(), '<title>'.config('health.title').'</title>') !== false);
    }
}
