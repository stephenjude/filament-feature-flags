<?php

namespace Stephenjude\FilamentFeatureFlag\Traits;

use Laravel\Pennant\Feature;
use Stephenjude\FilamentFeatureFlag\Models\FeatureSegment;

trait WithFeatureResolver
{
    /**
     * Resolve the feature's initial value.
     */
    public function resolve(mixed $scope): bool
    {
        if (!is_a($scope, config('filament-feature-flags.scope'))) {
            return config('filament-feature-flags.default');
        }

        return FeatureSegment::where('feature', get_class($this))
            ->get()
            ->whenEmpty(fn() => config('filament-feature-flags.default'))
            ->whenNotEmpty(fn($segments) => $segments->map(fn(FeatureSegment $segment) => $segment->resolve($scope))
                ->doesntContain(false));
    }

    public static function title(): string
    {
        return str(class_basename(self::class))->snake()->replace('_', ' ')->title()->toString();
    }

    public static function description(): string
    {
        $name = self::title();

        return "This feature covers $name.";
    }

    public static function state(): bool
    {
        return Feature::active(self::class);
    }
}
