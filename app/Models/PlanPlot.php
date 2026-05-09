<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanPlot extends Model
{
    public const STATUS_DONE = 'done';

    public const STATUS_UNDER_CONSTRUCTION = 'under_construction';

    public const STATUS_UNFINISHED = 'unfinished';

    protected $fillable = [
        'plot_number',
        'status',
        'owner_name',
        'road_no',
        'details',
    ];

    protected $casts = [
        'plot_number' => 'integer',
    ];

    /**
     * @return array<string, string>
     */
    public static function statusOptions(): array
    {
        return [
            self::STATUS_DONE => 'Done',
            self::STATUS_UNDER_CONSTRUCTION => 'Under construction',
            self::STATUS_UNFINISHED => 'Unfinished',
        ];
    }
}
