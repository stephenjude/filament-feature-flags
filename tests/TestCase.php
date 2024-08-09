<?php

namespace Stephenjude\FilamentFeatureFlag\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use Stephenjude\FilamentFeatureFlag\FeatureFlagPluginServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Stephenjude\\FilamentFeatureFlag\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            FeatureFlagPluginServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        config()->set('pennant.default', 'array');

        /*
        $migration = include __DIR__.'/../database/migrations/create_filament-feature-flags_table.php.stub';
        $migration->up();
        */
    }
}
