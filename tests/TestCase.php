<?php

namespace Stephenjude\FilamentFeatureFlag\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Laravel\Pennant\PennantServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Stephenjude\FilamentFeatureFlag\FeatureFlagPluginServiceProvider;
use Stephenjude\FilamentFeatureFlag\Traits\WithFeatureResolver;

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
            PennantServiceProvider::class,
            FeatureFlagPluginServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        config()->set('pennant.default', 'array');

        $migration = include __DIR__.'/../database/migrations/create_filament_feature_flags_table.php.stub';
        $migration->up();
    }
}

/**
 * @class dummy feature for testing
 */
class TestFeature
{
    use WithFeatureResolver;
}
