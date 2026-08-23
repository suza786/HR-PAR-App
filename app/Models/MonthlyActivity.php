<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MonthlyActivity extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'par_calendar_id',
        'emp_id',
        'leave_hrs',
        'working_hrs',
        'note',
        'status',
    ];

    public function parCalendar()
    {
        return $this->belongsTo(ParCalendar::class, 'par_calendar_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_id', 'emp_id');
    }

    public function activityAllocations()
    {
        return $this->hasMany(ActivityAllocation::class, 'monthly_activity_id');
    }
    
    // // ──────────────────────────────────────────────
    // // Generate Calendar Weeks
    // // ──────────────────────────────────────────────
    // public static function generateCalendarWeeks(int $year, string $month): array
    // {        $date = Carbon::createFromFormat('Y-F-d', "$year-$month-01");
    //     $weeks = [];
    //     $currentWeek = [];      
    //     while ($date->month === Carbon::createFromFormat('Y-F-d', "$year-$month-01")->month) {
    //         $currentWeek[] = $date->copy();
    //         if ($date->isSunday()) {
    //             $weeks[] = $currentWeek;
    //             $currentWeek = [];
    //         }
    //         $date->addDay();
    //     }
    //     return $weeks;
    // }
}
