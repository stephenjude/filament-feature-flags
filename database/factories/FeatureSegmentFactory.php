<?php

namespace Stephenjude\FilamentFeatureFlag\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Stephenjude\FilamentFeatureFlag\Models\FeatureSegment;

class FeatureSegmentFactory extends Factory
{
    protected $model = FeatureSegment::class;

    public function definition()
    {
        return [
            'feature' => 'App\\Features\\Wallet',
            'scope' => 'currencies',
            'values' => ['NGN', 'GHS', 'USD'],
            'active' => true,
        ];
    }
}
