<?php

namespace Secretwebmaster\LaravelOptionable\Tests;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Secretwebmaster\LaravelOptionable\Providers\PackageServiceProvider;
use Wncms\Translatable\TranslatableServiceProvider;

class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            PackageServiceProvider::class,
            TranslatableServiceProvider::class,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate', ['--database' => 'testbench'])->run();

        Schema::create('test_pages', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->timestamps();
        });
    }

    protected function defineEnvironment($app): void
    {
        tap($app['config'], function (Repository $config) {
            $config->set('database.default', 'testbench');
            $config->set('database.connections.testbench', [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
            ]);

            $config->set('queue.batching.database', 'testbench');
            $config->set('queue.failed.database', 'testbench');
        });
    }
}
