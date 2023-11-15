<?php

namespace Stephenjude\FilamentFeatureFlag;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FeatureFlagPluginServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-feature-flags')
            ->hasConfigFile()
            ->hasMigration('create_filament_feature_flags_table');
    }
}
