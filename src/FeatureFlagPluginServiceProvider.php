<?php

namespace Stephenjude\FilamentFeatureFlag;

use Illuminate\Filesystem\Filesystem;
use Laravel\Pennant\Feature;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Symfony\Component\Finder\Finder;

class FeatureFlagPluginServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-feature-flags')
            ->hasConfigFile()
            ->hasMigration('create_filament_feature_flags_table');
    }

    public function bootingPackage()
    {

        /**
        * `Feature` folder are required by the `laravel-pennant` if using a `discovery` method
         * Boot only the `Feature::discover()` if the Feature folder in app folder of laravel is existing.
         */
        (new Filesystem)->ensureDirectoryExists(app_path('Feature'));
        Feature::discover();
    }
}
