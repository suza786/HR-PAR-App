<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use SoftDeletes;

    //protected $connection = 'sna_lms';
    //protected $table = 'departments';

    protected $guarded = [];

    // protected $fillable = [
    //     'name',
    //     'description',
    //     'parent_dept_id'
    // ];

    public function parentDepartment()
    {
        return $this->belongsTo(Department::class, 'parent_dept_id');
    }
    
    protected $attributes = [
        'parent_dept_id' => 0, // Default value
    ];
}
