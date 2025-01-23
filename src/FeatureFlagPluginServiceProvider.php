<?php

namespace Stephenjude\FilamentFeatureFlag;

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
        $finder = new Finder();
        $finder->directories()->name('Feature')->in(app_path());
        /**
        * `Feature` folder are required by the `laravel-pennant` if using a `discovery` method
         * Boot only the Feature::discovers() if the Feature folder in app folder of laravel is existing.
         */
        if($finder->hasResults()) {
            Feature::discover();
        }
    }
}
