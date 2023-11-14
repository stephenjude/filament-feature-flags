<?php

namespace App\Filament\Resources\FeatureResource\Pages;

use App\Enums\FeatureSegmentScope;
use App\Filament\Resources\FeatureResource\FeatureSegmentResource;
use App\Jobs\AppFeatureJob;
use App\Models\FeatureSegment;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Laravel\Pennant\Feature;

class ManageFeatureSegments extends ManageRecords
{
    protected static string $resource = FeatureSegmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalWidth('md')
                ->label('Create Segment')
                ->after(
                    fn (FeatureSegment $record) => $this->applyAction($record->feature, $record->description())
                ),

            Actions\Action::make('activate_for_all')
                ->label('Activate For All Customers')
                ->modalWidth('md')
                ->modalHeading(fn ($record) => 'This action will activate feature for all customers.')
                ->form([
                    Select::make('feature')
                        ->required()
                        ->options(FeatureSegmentScope::featureOptions())
                        ->columnSpanFull(),
                ])
                ->modalSubmitActionLabel('Activate')
                ->action(function ($data) {
                    $feature = $data['feature'];

                    Feature::activateForEveryone($feature);

                    $this->applyAction($feature, "{$feature::title()} activated for all customers.");
                }),

            Actions\Action::make('deactivate_for_all')
                ->modalWidth('md')
                ->label('Deactivate For All Customers')
                ->modalHeading(fn ($record) => 'This action will deactivate this feature for all customers.')
                ->form([
                    Select::make('feature')
                        ->required()
                        ->options(FeatureSegmentScope::featureOptions())
                        ->columnSpanFull(),
                ])
                ->modalSubmitActionLabel('Deactivate')
                ->color('danger')
                ->action(function ($data) {
                    $feature = $data['feature'];

                    Feature::deactivateForEveryone($feature);

                    $this->applyAction($feature, "{$feature::title()} deactivated for all customers.");
                }),
        ];
    }

    private function applyAction(string $feature, string $description): void
    {
        AppFeatureJob::dispatch($feature, $description, Filament::auth()->user())->onQueue('system');

        Notification::make()->success()->title('Done!')->body($description)->send();
    }
}
