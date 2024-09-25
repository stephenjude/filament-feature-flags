<?php

namespace Stephenjude\FilamentFeatureFlag;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Stephenjude\FilamentFeatureFlag\Traits\HasAuthorization;

class FeatureFlagPlugin implements Plugin
{
    use HasAuthorization;

    public function getId(): string
    {
        return 'filament-feature-flag';
    }

    public function register(Panel $panel): void
    {
        $panel->resources(config('filament-feature-flags.resources'));
    }

    public function boot(Panel $panel): void
    {
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
