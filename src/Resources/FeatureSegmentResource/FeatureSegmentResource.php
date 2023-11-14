<?php

namespace App\Filament\Resources\FeatureResource;

use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Stephenjude\FeaturePlugin\Models\FeatureSegment;

class FeatureSegmentResource extends Resource
{
    protected static ?string $model = FeatureSegment::class;

    public static function getNavigationGroup(): ?string
    {
        return config('filament-feature-flags.panel.group');
    }

    public static function getNavigationLabel(): string
    {
        return config('filament-feature-flags.panel.label');
    }

    public static function getNavigationIcon(): ?string
    {
        return config('filament-feature-flags.panel.icon');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('feature')
                    ->required()
                    ->options(FeatureSegment::featureOptionsList())
                    ->columnSpanFull(),

                Select::make('scope')
                    ->live()
                    ->afterStateUpdated(fn(Set $set) => $set('values', null))
                    ->required()
                    ->columnSpanFull()
                    ->options(FeatureSegment::segmentOptionsList()),

                ...static::createValuesFields(),

                Select::make('active')
                    ->options([true => 'Activate', false => 'Deactivate'])
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('feature')
                    ->getStateUsing(function (FeatureSegment $record) {
                        return app($record->feature)->title();
                    }),
                Tables\Columns\TextColumn::make('values')
                    ->label('Segment')
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Activated' => 'success',
                        'Deactivated' => 'danger',
                    })
                    ->getStateUsing(function (FeatureSegment $record) {
                        return $record->active ? 'Activated' : 'Deactivated';
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('feature')
                    ->options(FeatureSegment::featureOptionsList()),
                Tables\Filters\SelectFilter::make('scope')
                    ->options(FeatureSegment::segmentOptionsList()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()->before(function (FeatureSegment $segment) {
                    $name = $segment->feature::title();

                    $action = $segment->active ? 'activated' : 'deactivated';

                    $description = "Removed Feature Segment: $name feature $action for customer with $segment->scope — ".
                        implode(', ', $segment->values);

                    AppFeatureJob::dispatch($segment, $description, Filament::auth()->user());
                }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageFeatureSegments::route('/'),
        ];
    }

    public static function createValuesFields(): array
    {
        return collect(config('filament-feature-flags.segments'))
            ->map(function ($segment) {
                $column = $segment['column'];
                $model = $segment['source']['model'];
                $value = $segment['source']['value'];
                $key = $segment['source']['key'];

                return Select::make('values')
                    ->label(str($column)->plural())
                    ->hidden(fn(Get $get) => $get('scope') !== $column)
                    ->required()
                    ->multiple()
                    ->searchable()
                    ->columnSpanFull()
                    ->getSearchResultsUsing(
                        fn(string $search): array => $model::where($value, 'like', "%{$search}%")
                            ->limit(50)->pluck($value, $key)->toArray()
                    );
            }
            )
            ->toArray();
    }
}
