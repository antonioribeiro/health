<?php

namespace PragmaRX\Health\Tests\PhpUnit\Service;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use PragmaRX\Health\Commands;
use PragmaRX\Health\Http\Controllers\Health as HealthController;
use PragmaRX\Health\Support\ResourceLoader;
use PragmaRX\Health\Tests\PhpUnit\TestCase;
use PragmaRX\Yaml\Package\Yaml;

class ServiceTest extends TestCase
{
    private $service;

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

        $this->app['config']->set(
            'health.resources_location.path',
            package_resources_dir()
        );
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->service = app('pragmarx.health');
    }

    private function sortChars($string)
    {
        $stringParts = str_split($string);

        sort($stringParts);

        return implode('', $stringParts);
    }

    public function testResourcesTimeIsCorrectlySet()
    {
        $this->assertGreaterThan(
            0,
            $this->getResources()['AppKey']->targets[0]->result->elapsedTime
        );
    }

    public function testResourcesWhereChecked()
    {
        $this->assertCheckedResources($this->getResources());
    }

    public function testCacheFlush()
    {
        $this->assertCheckedResources($this->getResources(true));
    }

    public function testUnsorted()
    {
        $this->app['config']->set('health.sort_by', null);

        $this->assertCheckedResources($this->getResources(true));
    }

    public function testInvalidEnabledResources()
    {
        $this->expectException(\DomainException::class);

        $this->app['config']->set('health.resources.enabled', 'invalid');

        (new ResourceLoader(new Yaml()))->load();

        $this->getResources(true);
    }

    public function testInvalidLoadOneResource()
    {
        $this->app['config']->set('health.resources.enabled', ['Database']);

        $resource = (new ResourceLoader(new Yaml()))->load();

        $this->assertTrue($resource->first()['name'] == 'Database');
    }

    public function assertCheckedResources($resources)
    {
        $healthy = $resources->filter(function ($resource) {
            return $resource->isHealthy();
        })->keys();

        $failing = $resources->filter(function ($resource) {
            return ! $resource->isHealthy();
        })->keys();

        $this->assertGreaterThanOrEqual(self::RESOURCES_HEALTHY_EVERYWHERE, $failing->count());

        $this->assertGreaterThanOrEqual($failing->count(), count(static::ALL_RESOURCES) - self::RESOURCES_HEALTHY_EVERYWHERE);

        $this->assertTrue($this->isSubset($healthy, static::ALL_RESOURCES));

        $this->assertTrue($this->isSubset($failing, static::ALL_RESOURCES));
    }

    public function testInstantiation()
    {
        $this->assertInstanceOf(Collection::class, $this->getResources());
    }

    public function testResourcesHasTheCorrectCount()
    {
        $this->assertCount(
            count(static::ALL_RESOURCES),
            $this->getResources()->toArray()
        );
    }

    public function testResourcesItemsMatchConfig()
    {
        $this->assertEquals(
            collect(static::ALL_RESOURCES)
                ->map(function ($value) {
                    return strtolower($value);
                })
                ->sort()
                ->values()
                ->toArray(),
            $this->getResources()
                ->keys()
                ->map(function ($value) {
                    return strtolower($value);
                })
                ->sort()
                ->values()
                ->toArray()
        );
    }

    public function testArtisanCommands()
    {
        $commands = ['panel', 'check'];

        foreach ($commands as $command) {
            (new Commands($this->service))->$command();
        }

        $this->assertFalse(! true);
    }

    public function testController()
    {
        $controller = new HealthController($this->service);

        $request = new \Illuminate\Http\Request();

        $request = $request->createFromBase(
            \Symfony\Component\HttpFoundation\Request::create(
                '/health/panel',
                'GET'
            )
        );

        $this->assertEquals(
            collect(
                json_decode($controller->check()->getContent(), true)
            )->count(),
            count(static::ALL_RESOURCES)
        );

        $this->assertTrue(
            Str::startsWith(
                $controller->panel()->getContent(),
                '<!DOCTYPE html>'
            )
        );

        $this->assertTrue(count($controller->config()) > 10);

        $this->assertTrue(
            $controller->getResource('app-key', $request)->name == 'App Key'
        );

        $this->assertTrue(
            $controller->allResources()->count() == count(static::ALL_RESOURCES)
        );
    }

    public function isSubset($subset, $array): bool
    {
        $array = collect($array);

        if ($subset->isEmpty() || $array->isEmpty()) {
            return false;
        }

        foreach ($subset as $value) {
            if (! $array->contains($value)) {
                return false;
            }
        }

        return true;
    }
}
