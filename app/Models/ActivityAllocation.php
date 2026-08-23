<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityAllocation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'monthly_activity_id',
        'project_id',
        'activity_id',
        'activity_description',
        'working_hrs',
    ];

    protected $casts = [
        'working_hrs' => 'float',
    ];

    public function monthlyActivity()
    {
        return $this->belongsTo(MonthlyActivity::class, 'monthly_activity_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id');
    }
}
