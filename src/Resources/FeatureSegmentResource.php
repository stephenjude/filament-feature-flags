<?php

namespace Stephenjude\FilamentFeatureFlag\Resources;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Unique;
use Stephenjude\FilamentFeatureFlag\Events\FeatureSegmentModified;
use Stephenjude\FilamentFeatureFlag\Events\FeatureSegmentRemoved;
use Stephenjude\FilamentFeatureFlag\Events\RemovingFeatureSegment;
use Stephenjude\FilamentFeatureFlag\Models\FeatureSegment;

class FeatureSegmentResource extends Resource
{
    protected static ?string $model = FeatureSegment::class;

    public static function getNavigationGroup(): ?string
    {
        return __(config('filament-feature-flags.panel.group'));
    }

    public static function getNavigationLabel(): string
    {
        return __(config('filament-feature-flags.panel.label'));
    }

    public static function getModelLabel(): string
    {
        return __(config('filament-feature-flags.panel.title'));
    }

    public static function getNavigationIcon(): ?string
    {
        return config('filament-feature-flags.panel.icon');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('feature')
                    ->required()
                    ->options(FeatureSegment::featureOptionsList())
                    ->columnSpanFull(),

                Select::make('scope')
                    ->live()
                    ->afterStateUpdated(fn (Set $set) => $set('values', null))
                    ->required()
                    ->columnSpanFull()
                    ->options(FeatureSegment::segmentOptionsList()),

                ...static::createValuesFields(),

                Select::make('active')
                    ->label(__('filament-feature-flags::messages.form.status'))
                    ->options([true => __('filament-feature-flags::messages.form.activate'), false => __('filament-feature-flags::messages.form.deactivate')])
                    ->unique(
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule, Get $get) => $rule
                            ->where('feature', $get('feature'))
                            ->where('scope', $get('scope'))
                            ->where('active', $get('active'))
                    )
                    ->validationMessages([
                        'unique' => __('filament-feature-flags::messages.form.unique_validation'),
                    ])
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->sortable(['feature'])
                    ->searchable(['feature']),
                Tables\Columns\TextColumn::make('values')
                    ->label(__('filament-feature-flags::messages.table.segment'))
                    ->wrap()
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'ACTIVATED' => 'success',
                        'DEACTIVATED' => 'danger',
                    })
                    ->weight(FontWeight::ExtraBold)
                    ->getStateUsing(function (FeatureSegment $record) {
                        return $record->active ? 'ACTIVATED' : 'DEACTIVATED';
                    }),
            ])
            ->defaultSort('feature')
            ->filters([
                Tables\Filters\SelectFilter::make('feature')
                    ->options(FeatureSegment::featureOptionsList()),
                Tables\Filters\SelectFilter::make('scope')
                    ->options(FeatureSegment::segmentOptionsList()),
            ])
            ->recordActions([
                EditAction::make()
                    ->label(__('filament-feature-flags::messages.actions.modify'))
                    ->modalHeading(__('filament-feature-flags::messages.actions.modify_heading'))
                    ->after(fn (FeatureSegment $record) => FeatureSegmentModified::dispatch(
                        $record,
                        Filament::auth()->user()
                    )),

                DeleteAction::make()
                    ->label(__('filament-feature-flags::messages.actions.remove'))
                    ->modalHeading(__('filament-feature-flags::messages.actions.remove_heading'))
                    ->modalDescription(fn (FeatureSegment $record) => $record->description)
                    ->after(fn () => FeatureSegmentRemoved::dispatch(Filament::auth()->user()))
                    ->before(fn (FeatureSegment $record) => RemovingFeatureSegment::dispatch(
                        $record,
                        Filament::auth()->user()
                    )),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageFeatureSegments::route('/'),
        ];
    }

    private static function createValuesFields(): array
    {
        return collect(config('filament-feature-flags.segments'))
            ->map(
                function ($segment) {
                    $column = $segment['column'];
                    $model = $segment['source']['model'];
                    $value = $segment['source']['value'];
                    $key = $segment['source']['key'];

                    return Select::make('values')
                        ->preload()
                        ->multiple()
                        ->required()
                        ->searchable()
                        ->columnSpanFull()
                        ->label(str($column)->plural()->title())
                        ->hidden(fn (Get $get) => $get('scope') !== $column)
                        ->getOptionLabelsUsing(
                            fn (array $values): array => $model::query()
                                ->whereIn($value, $values)
                                ->pluck($value, $key)
                                ->all()
                        )
                        ->getSearchResultsUsing(
                            fn (string $search): array => $model::query()
                                ->where($value, 'like', "%{$search}%")
                                ->limit(50)->pluck($value, $key)
                                ->toArray()
                        );
                }
            )
            ->toArray();
    }
}
