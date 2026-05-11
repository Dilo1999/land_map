<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanPlot extends Model
{
    public const STATUS_DONE = 'done';

    public const STATUS_UNDER_CONSTRUCTION = 'under_construction';

    public const STATUS_UNFINISHED = 'unfinished';

    public const REMOVED_PLOT_NUMBERS = [1, 2, 3, 4];

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
            self::STATUS_DONE => 'Cleared',
            self::STATUS_UNDER_CONSTRUCTION => 'In Process',
            self::STATUS_UNFINISHED => 'Unknown',
        ];
    }
}
