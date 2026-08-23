<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
    ];

    public function activityAllocations()
    {
        return $this->hasMany(ActivityAllocation::class);
    }
    
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'activity_allocations', 'activity_id', 'project_id');
    }
}
