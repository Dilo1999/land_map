<?php

namespace App\Filament\Resources\PlanPlotResource\Pages;

use App\Filament\Resources\PlanPlotResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPlanPlots extends ListRecords
{
    protected static string $resource = PlanPlotResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
