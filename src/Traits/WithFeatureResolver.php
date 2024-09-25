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
        if (! is_a($scope, config('filament-feature-flags.scope'))) {
            return config('filament-feature-flags.default');
        }

        /*
        * This resolution loop iterates through all segmentations associated with this feature
        * by checking deactivated segments first, then checking activated segments;
        * and if any resolve is True, this feature resolution will be True.
        */
        return FeatureSegment::where('feature', get_class($this))
            ->get()
            ->whenEmpty(
                fn () => config('filament-feature-flags.default'),
                fn ($segments) => $segments->sortBy('active')
                    ->map(fn (FeatureSegment $segment) => $segment->resolve($scope))
                    ->contains(true)
            );
    }

    public static function title(): string
    {
        return str(class_basename(self::class))->headline()->toString();
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
