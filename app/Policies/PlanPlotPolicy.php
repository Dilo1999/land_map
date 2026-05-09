<?php

namespace App\Policies;

use App\Models\PlanPlot;
use App\Models\User;

class PlanPlotPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isEditor() || $user->isViewer();
    }

    public function view(User $user, PlanPlot $planPlot): bool
    {
        return $user->isAdmin() || $user->isEditor() || $user->isViewer();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isEditor();
    }

    public function update(User $user, PlanPlot $planPlot): bool
    {
        return $user->isAdmin() || $user->isEditor();
    }

    public function delete(User $user, PlanPlot $planPlot): bool
    {
        return $user->isAdmin() || $user->isEditor();
    }
}
