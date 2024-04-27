<?php

namespace Secretwebmaster\LaravelOptionable\Tests;

use Illuminate\Contracts\Config\Repository;
use Orchestra\Testbench\TestCase as TestbenchTestCase;
use Secretwebmaster\LaravelOptionable\Providers\PackageServiceProvider;

class TestCase extends TestbenchTestCase
{
    /**
     * Automatically enables package discoveries.
     *
     * @var bool
     */
    // protected $enablesPackageDiscoveries = true;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        // Code before application created.

        $this->afterApplicationCreated(function () {
            // Code after application created.
        });

        $this->beforeApplicationDestroyed(function () {
            // Code before application destroyed.
        });

        parent::setUp();
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetup($app)
    {
        $app['config']->set('database.default', 'test_db');
        $app['config']->set('database.connections.test_db', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        // Setup queue database connections.
        $app['config']->set([
            'queue.batching.database' => 'test_db',
            'queue.failed.database' => 'test_db',
        ]);
    }


    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string<\Illuminate\Support\ServiceProvider>>
     */
    protected function getPackageProviders($app)
    {
        return [
            PackageServiceProvider::class,
        ];
    }
}
