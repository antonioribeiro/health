<?php

namespace PragmaRX\Health\Tests\PhpUnit\Service;

use Illuminate\Support\Collection;
use PragmaRX\Health\Facade as Health;
use PragmaRX\Health\Tests\PhpUnit\TestCase;

class ServiceTest extends TestCase
{
    /**
     * @var \PragmaRX\Health\Service $service
     */
    private $service;

    /**
     * @var \Illuminate\Support\Collection $resources
     */
    private $resources;

    private function getConfig()
    {
        $config = require __DIR__.'/config.php';

        return $config;
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('health', $this->getConfig());
    }

    public function setUp()
    {
        parent::setUp();

        $this->service = app('pragmarx.health');

        $this->resources = $this->service->checkResources();
    }

    public function testInstantiation()
    {
        $this->assertInstanceOf(Collection::class, $this->resources);
    }

    public function testConfigWasLoadedProperly()
    {
        $this->assertEquals($this->resources['health']['error_message'], 'This is a test only error message.');
    }

    public function testResourcesHasTheCorrectCount()
    {
        $this->assertCount(9, $this->resources->toArray());
    }

    public function testResourcesItemsMatchConfig()
    {
        $this->assertEquals(
            [
                'health',
                'cache',
                'cloud_storage',
                'database',
                'filesystem',
                'framework',
                'http',
                'https',
                'mail',
            ],
            $this->resources->keys()->toArray()
        );
    }

    public function testResourcesWhereChecked()
    {
        $healthCount = $this->resources->reduce(function ($carry, $item) {
            return $carry + (isset($item['health']['healthy'])
                    ? 1
                    : 0);
        }, 0);
        $this->assertEquals(9, $healthCount);
    }
}
