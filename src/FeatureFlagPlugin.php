<?php

namespace Stephenjude\FeaturePlugin;

use App\Filament\Resources\FeatureResource\FeatureSegmentResource;

class FeatureFlagPlugin implements Plugin
{
    public function getId(): string
    {
        return 'feature-flag';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                FeatureSegmentResource::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }
}
