<?php

namespace Stephenjude\FeaturePlugin\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class FeatureSegmentFactory extends Factory
{
    protected $model = YourModel::class;

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
