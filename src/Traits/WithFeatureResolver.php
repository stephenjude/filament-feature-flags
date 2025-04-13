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
            return $this->resolveDefaultValue($scope);
        }

        /*
        * This resolution loop iterates through all segmentations associated with this feature
        * by checking deactivated segments first, then checking activated segments;
        * and if any resolve is True, this feature resolution will be True.
        */
        return FeatureSegment::where('feature', get_class($this))
            ->get()
            ->whenEmpty(
                fn () => $this->resolveDefaultValue($scope),
                fn ($segments) => $segments->sortBy('active')
                    ->map(fn (FeatureSegment $segment) => $segment->resolve($scope))
                    ->contains(true)
            );
    }

    /**
     * Resolve the default value from the feature flag if possible.
     */
    protected function resolveDefaultValue(mixed $scope): bool
    {
        if (property_exists($this, 'defaultValue')) {
            return (bool) $this->defaultValue;
        }

        if (method_exists($this, 'defaultValue')) {
            return (bool) $this->defaultValue($scope);
        }

        return config('filament-feature-flags.default');
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
