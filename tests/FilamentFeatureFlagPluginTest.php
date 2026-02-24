<?php

use Stephenjude\FilamentFeatureFlag\Models\FeatureSegment;
use Stephenjude\FilamentFeatureFlag\Resources\FeatureSegmentResource;
use Stephenjude\FilamentFeatureFlag\Resources\ManageFeatureSegments;
use Stephenjude\FilamentFeatureFlag\Tests\TestFeature;

use function Pest\Laravel\get;
use function Pest\Livewire\livewire;

it('can render page', function () {
    get(FeatureSegmentResource::getUrl())->assertOk();
});

it('can list feature segments', function () {
    $segments = FeatureSegment::factory()->count(1)->create();

    livewire(ManageFeatureSegments::class)
        ->assertCanSeeTableRecords($segments);
});

it('can feature segment using email scope', function () {
    $segments = FeatureSegment::factory([

    ])->count(1)->create();

    livewire(ManageFeatureSegments::class)
        ->assertCanSeeTableRecords($segments);
});

it('can compare integers with strings', function (int $value, bool $expectedOutcome) {
    $featureSegment = FeatureSegment::factory([
        'feature' => TestFeature::class,
        'scope' => 'value',
        'active' => true,
        'values' => [
            $value,
        ],
    ])->create();

    $objectToTest = new class {
        public string $value = '123';
    };

    expect($featureSegment->resolve($objectToTest))->toBe($expectedOutcome);
})->with([
    [123, true],
    [2, false],
]);
