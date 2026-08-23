<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dependent extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'employee_id',
        'name',
        'relation',
        'gender',
        'dob'
    ];

    protected $dates = ['dob', 'deleted_at'];

    
    // Define any relationships or additional methods here
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

}
