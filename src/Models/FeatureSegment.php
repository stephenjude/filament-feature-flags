<?php

namespace Stephenjude\FeaturePlugin\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Pennant\Feature;

class FeatureSegment extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = [
        'title',
        'description',
    ];

    protected $casts = [
        'values' => 'array',
    ];

    public function resolve(mixed $scope): bool
    {
        $defaultState = config('filament-feature-flags.default');

        if (!is_a($scope, config('filament-feature-flags.scope'))) {
            return $defaultState;
        }

        if (in_array($scope->{$this->scope}, $this->values, true)) {
            return $this->active;
        }

        return $defaultState;
    }

    public function title(): Attribute
    {
        return Attribute::get(fn() => $this->feature::title());
    }

    public function description(): Attribute
    {
        return Attribute::get(fn() => sprintf(
            '%s %s for customers who belong to this scope: %s — %s.',
            $this->title,
            $this->active ? 'activated' : 'deactivated',
            $this->scope,
            implode(', ', $this->values)
        ));
    }

    public static function allFeatures(): array
    {
        return collect(Feature::all())
            ->map(fn($value, $key) => [
                'id' => $key,
                'name' => $name = str(class_basename($key))->snake()->replace('_', ' ')->title()->toString(),
                'state' => $value,
                'description' => "This feature covers $name on the mobile app.",
            ])
            ->values()
            ->toArray();
    }

    public static function featureOptionsList(): array
    {
        return collect(self::allFeatures())->pluck('name', 'id')->toArray();
    }

    public static function segmentOptionsList(): array
    {
        $segments = config('filament-feature-flags.segments');

        return collect(array_combine($segments, $segments))
            ->map(fn($segment) => str($segment)->plural())
            ->toArray();
    }

}
