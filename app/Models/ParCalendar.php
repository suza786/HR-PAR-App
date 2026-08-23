<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ParCalendar extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'month',
        'week',
        'weekStart',
        'weekEnd',
        'working_days',
        'office_holidays',
        'total_days',
        'year',
    ];

    public function monthlyActivities()
    {
        return $this->hasMany(MonthlyActivity::class, 'par_calendar_id');
    }
}
    
