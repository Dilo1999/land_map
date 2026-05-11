<?php

namespace App\Http\Controllers;

use App\Models\PlanPlot;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $payload = PlanPlot::query()
            ->whereNotIn('plot_number', PlanPlot::REMOVED_PLOT_NUMBERS)
            ->orderBy('plot_number')
            ->get(['plot_number', 'status', 'owner_name', 'road_no', 'details'])
            ->mapWithKeys(fn (PlanPlot $p) => [
                (string) $p->plot_number => [
                    'status' => $p->status ?? PlanPlot::STATUS_UNFINISHED,
                    'owner_name' => $p->owner_name,
                    'road_no' => $p->road_no,
                    'details' => $p->details,
                ],
            ])
            ->all();

        return view('home', [
            'planPlotsPayload' => $payload,
        ]);
    }
}
