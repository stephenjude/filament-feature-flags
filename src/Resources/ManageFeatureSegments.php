<?php

namespace Stephenjude\FilamentFeatureFlag\Resources;

use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Laravel\Pennant\Feature;
use Stephenjude\FilamentFeatureFlag\Events\FeatureActivatedForAll;
use Stephenjude\FilamentFeatureFlag\Events\FeatureDeactivatedForAll;
use Stephenjude\FilamentFeatureFlag\Events\FeatureSegmentCreated;
use Stephenjude\FilamentFeatureFlag\FeatureFlagPlugin;
use Stephenjude\FilamentFeatureFlag\Models\FeatureSegment;

class ManageFeatureSegments extends ManageRecords
{
    protected static string $resource = FeatureSegmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalWidth('md')
                ->modalHeading(__('Create Feature Segment'))
                ->label(__('Segment Feature'))
                ->after(fn (FeatureSegment $record) => $this->afterCreate($record)),

            Actions\Action::make('activate_for_all')
                ->label(__('Activate'))
                ->modalWidth('md')
                ->modalDescription(fn ($record) => __('This action will activate the selected feature for users.'))
                ->form([
                    Select::make('feature')
                        ->label(__('Feature'))
                        ->required()
                        ->options(FeatureSegment::featureOptionsList())
                        ->columnSpanFull(),
                ])
                ->modalSubmitActionLabel(__('Activate'))
                ->action(fn ($data) => $this->activateForAll($data['feature'])),

            Actions\Action::make('deactivate_for_all')
                ->label(__('Deactivate for All'))
                ->modalWidth('md')
                ->label(__('Deactivate'))
                ->modalDescription(fn ($record) => __('This action will deactivate this feature for users.'))
                ->form([
                    Select::make('feature')
                        ->label(__('Feature'))
                        ->required()
                        ->options(FeatureSegment::featureOptionsList())
                        ->columnSpanFull(),
                ])
                ->modalSubmitActionLabel(__('Deactivate'))
                ->color('danger')
                ->action(fn ($data) => $this->deactivateForAll($data['feature'])),

            Actions\Action::make('purge_features')
                ->modalWidth('md')
                ->label(__('Purge'))
                ->modalDescription(fn ($record) => __('This action will purge resolved features from storage.'))
                ->form([
                    Select::make('feature')
                        ->label(__('Feature'))
                        ->selectablePlaceholder(false)
                        ->options(array_merge([null => __('All Features')], FeatureSegment::featureOptionsList()))
                        ->columnSpanFull(),
                ])
                ->modalSubmitActionLabel(__('Purge'))
                ->color('danger')
                ->action(fn ($data) => $this->purgeFeatures($data['feature'])),
        ];
    }

    private function activateForAll(string $feature): void
    {
        Feature::activateForEveryone($feature);

        Notification::make()->success()->title(__('Done!'))
            ->body(__("{$feature::title()} activated for users."))->send();

        FeatureActivatedForAll::dispatch($feature, Filament::auth()->user());
    }

    private function deactivateForAll(string $feature): void
    {
        Feature::deactivateForEveryone($feature);

        Notification::make()->success()->title(__('Done!'))
            ->body(__("{$feature::title()} deactivated for users."))
            ->send();

        FeatureDeactivatedForAll::dispatch($feature, Filament::auth()->user());
    }

    private function purgeFeatures(?string $feature): void
    {
        Feature::purge($feature);

        $featureTitle = is_null($feature)
            ? __('All features')
            : $feature::title().__(' feature');

        Notification::make()->success()->title(__('Done!'))
            ->body(__("$featureTitle successfully purged from storage."))
            ->send();
    }

    private function afterCreate(FeatureSegment $featureSegment): void
    {
        Feature::purge($featureSegment->feature);

        FeatureSegmentCreated::dispatch($featureSegment, Filament::auth()->user());
    }

    public static function canAccess(array $parameters = []): bool
    {
        return FeatureFlagPlugin::get()->authorized();
    }
}
