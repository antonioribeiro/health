<?php

namespace PragmaRX\Health\Tests\PhpUnit;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use PragmaRX\Health\ServiceProvider as HealthServiceProvider;

class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [HealthServiceProvider::class];
    }
}
