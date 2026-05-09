<?php

namespace App\Filament\Resources\PlanPlotResource\Pages;

use App\Filament\Resources\PlanPlotResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPlanPlot extends EditRecord
{
    protected static string $resource = PlanPlotResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
