<?php

namespace Stephenjude\FilamentFeatureFlag\Models;

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

        if (in_array($scope->{$this->scope}, $this->values, true)) {
            return $this->active;
        }

        return $defaultState;
    }

    public function title(): Attribute
    {
        return Attribute::get(fn () => class_exists($this->feature) ? $this->feature::title() : '(Feature Deleted)');
    }

    public function description(): Attribute
    {
        return Attribute::get(fn () => sprintf(
            '%s %s for customers who have any of these %s — %s.',
            $this->title,
            $this->active ? 'activated' : 'deactivated',
            str($this->scope)->plural(),
            implode(', ', $this->values)
        ));
    }

    public static function allFeatures(): array
    {
        return collect(Feature::all())
            ->map(fn ($value, $key) => [
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
        return collect(config('filament-feature-flags.segments'))
            ->pluck('column')
            ->mapWithKeys(fn ($segment) => [$segment => str($segment)->plural()->title()->toString()])
            ->toArray();
    }
}
