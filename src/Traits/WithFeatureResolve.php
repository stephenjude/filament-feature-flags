<?php

namespace Stephenjude\FeaturePlugin\Traits;

use App\Models\User;
use Laravel\Pennant\Feature;
use Stephenjude\FeaturePlugin\Models\FeatureSegment;

trait WithFeatureResolve
{
    /**
     * Resolve the feature's initial value.
     */
    public function resolve(mixed $scope): bool
    {
        $segments = FeatureSegment::where('feature', get_class($this))->cursor();


        return $segments->contains(fn(FeatureSegment $segment) => $segment->resolve($scope));
    }

    public static function title(): string
    {
        return str(class_basename(self::class))->snake()->replace('_', ' ')->ucfirst()->toString();
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
