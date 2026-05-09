<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlanPlotResource\Pages;
use App\Models\PlanPlot;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class PlanPlotResource extends Resource
{
    protected static ?string $model = PlanPlot::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?string $navigationLabel = 'Plan plots';

    protected static ?string $modelLabel = 'Plan plot';

    protected static ?string $pluralModelLabel = 'Plan plots';

    protected static ?string $recordTitleAttribute = 'plot_number';

    protected static ?string $slug = 'plan-plots';

    protected static ?string $navigationGroup = 'Interactive plan';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Plot details')
                    ->schema([
                        TextInput::make('plot_number')
                            ->label('Plot number')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->unique(ignoreRecord: true)
                            ->helperText(
                                'Must match this plot’s position on the map (1 = first clickable green/blue shape in the SVG).'
                            ),
                        Select::make('status')
                            ->label('Map status')
                            ->options(PlanPlot::statusOptions())
                            ->required()
                            ->default(PlanPlot::STATUS_UNFINISHED)
                            ->helperText(
                                'Done = green, Under construction = yellow, Unfinished = red on the public map.'
                            ),
                        TextInput::make('owner_name')
                            ->label('Owner / title')
                            ->maxLength(255)
                            ->helperText(
                                'Used for the sidebar button label and in plot details. Leave blank to use “Owner” plus the plot number.'
                            ),
                        Forms\Components\Section::make('Road no.')
                            ->schema([
                                TextInput::make('road_no')
                                    ->label('Road number')
                                    ->maxLength(255)
                                    ->helperText(
                                        'Optional. Shown when visitors open plot details on the public map.'
                                    ),
                            ])
                            ->columns(1),
                        Textarea::make('details')
                            ->label('Details')
                            ->rows(8)
                            ->helperText('Shown when visitors tap “View” on the map. Plain text; line breaks are preserved.'),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('plot_number')
                    ->label('#')
                    ->sortable(),
                TextColumn::make('status')
                    ->enum(PlanPlot::statusOptions())
                    ->sortable(),
                TextColumn::make('owner_name')
                    ->label('Owner / title')
                    ->placeholder('—')
                    ->searchable(),
                TextColumn::make('road_no')
                    ->label('Road no.')
                    ->placeholder('—')
                    ->searchable(),
                TextColumn::make('details')
                    ->limit(48),
            ])
            ->defaultSort('plot_number')
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlanPlots::route('/'),
            'create' => Pages\CreatePlanPlot::route('/create'),
            'edit' => Pages\EditPlanPlot::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}
