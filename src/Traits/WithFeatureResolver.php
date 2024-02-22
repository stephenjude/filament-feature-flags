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
         * and ensures that all resolved segments are true. If any resolved segment returns
         * false, this feature resolution will be false.
         */
        return FeatureSegment::where('feature', get_class($this))
            ->get()
            ->whenEmpty(
                fn () => config('filament-feature-flags.default'),
                fn ($segments) => $segments->map(fn (FeatureSegment $segment) => $segment->resolve($scope))
                    ->doesntContain(
                        false
                    ) // Makes sure that multiple segmentations are all true, if not resolve as false.
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
