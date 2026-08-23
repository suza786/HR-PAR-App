<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'donor_id',
        'project_code',
        'project_name',
        'short_name',
        'start_date',
        'end_date',
        'currency',
        'total_budget',
        'total_revised_budget',
    ];

    public function donor()
    {
        return $this->belongsTo(Donor::class);
    }
}
