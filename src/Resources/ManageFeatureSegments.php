<?php

namespace Stephenjude\FilamentFeatureFlag\Resources;

use Filament\Actions;
use Filament\Actions\Action;
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
                ->modalHeading(__('filament-feature-flags::messages.actions.create_segment'))
                ->label(__('filament-feature-flags::messages.actions.segment_feature'))
                ->after(fn (FeatureSegment $record) => $this->afterCreate($record)),

            Action::make('activate_for_all')
                ->label(__('filament-feature-flags::messages.actions.activate'))
                ->modalWidth('md')
                ->modalDescription(fn ($record) => __('filament-feature-flags::messages.actions.activate_description'))
                ->schema([
                    Select::make('feature')
                        ->label(__('filament-feature-flags::messages.form.feature'))
                        ->required()
                        ->options(FeatureSegment::featureOptionsList())
                        ->columnSpanFull(),
                ])
                ->modalSubmitActionLabel(__('filament-feature-flags::messages.actions.activate'))
                ->action(fn ($data) => $this->activateForAll($data['feature'])),

            Action::make('deactivate_for_all')
                ->label(__('filament-feature-flags::messages.actions.deactivate'))
                ->modalWidth('md')
                ->modalDescription(fn ($record) => __('filament-feature-flags::messages.actions.deactivate_description'))
                ->schema([
                    Select::make('feature')
                        ->label(__('filament-feature-flags::messages.form.feature'))
                        ->required()
                        ->options(FeatureSegment::featureOptionsList())
                        ->columnSpanFull(),
                ])
                ->modalSubmitActionLabel(__('filament-feature-flags::messages.actions.deactivate'))
                ->color('danger')
                ->action(fn ($data) => $this->deactivateForAll($data['feature'])),

            Action::make('purge_features')
                ->modalWidth('md')
                ->label(__('filament-feature-flags::messages.actions.purge'))
                ->modalDescription(fn ($record) => __('filament-feature-flags::messages.actions.purge_description'))
                ->schema([
                    Select::make('feature')
                        ->label(__('filament-feature-flags::messages.form.feature'))
                        ->selectablePlaceholder(false)
                        ->options(array_merge([null => __('filament-feature-flags::messages.actions.all_features')], FeatureSegment::featureOptionsList()))
                        ->columnSpanFull(),
                ])
                ->modalSubmitActionLabel(__('filament-feature-flags::messages.actions.purge'))
                ->color('danger')
                ->action(fn ($data) => $this->purgeFeatures($data['feature'])),
        ];
    }

    private function activateForAll(string $feature): void
    {
        Feature::activateForEveryone($feature);

        Notification::make()->success()->title(__('filament-feature-flags::messages.notifications.done'))
            ->body(__('filament-feature-flags::messages.notifications.activated_for_all', ['feature' => $feature::title()]))->send();

        FeatureActivatedForAll::dispatch($feature, Filament::auth()->user());
    }

    private function deactivateForAll(string $feature): void
    {
        Feature::deactivateForEveryone($feature);

        Notification::make()->success()->title(__('filament-feature-flags::messages.notifications.done'))
            ->body(__('filament-feature-flags::messages.notifications.deactivated_for_all', ['feature' => $feature::title()]))
            ->send();

        FeatureDeactivatedForAll::dispatch($feature, Filament::auth()->user());
    }

    private function purgeFeatures(?string $feature): void
    {
        Feature::purge($feature);

        $featureTitle = is_null($feature)
            ? __('filament-feature-flags::messages.notifications.all_features_purged')
            : __('filament-feature-flags::messages.notifications.feature_purged', ['feature' => $feature::title()]);

        Notification::make()->success()->title(__('filament-feature-flags::messages.notifications.done'))
            ->body($featureTitle)
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
