<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalaryAllocation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'emp_id',
        'project_id',
        'allocation_ratio',
        'start_month',
        'end_month',
        'curr_year',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_id', 'emp_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
}
