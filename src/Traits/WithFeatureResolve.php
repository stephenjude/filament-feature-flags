<?php

namespace Stephenjude\FilamentFeatureFlag\Traits;

use Laravel\Pennant\Feature;
use Stephenjude\FilamentFeatureFlag\Models\FeatureSegment;

trait WithFeatureResolve
{
    /**
     * Resolve the feature's initial value.
     */
    public function resolve(mixed $scope): bool
    {
        $defaultState = config('filament-feature-flags.default');

        if (!is_a($scope, config('filament-feature-flags.scope'))) {
            return $defaultState;
        }

        $segments = FeatureSegment::where('feature', get_class($this))->cursor();

        if ($segments->count()) {
            return $defaultState;
        }

        return $segments->contains(fn(FeatureSegment $segment) => $segment->resolve($scope));
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
