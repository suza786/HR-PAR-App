<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeRole extends Model
{
    use SoftDeletes;

    protected $table = 'employee_roles';

    protected $fillable = [
        'emp_id',
        'role_id',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_id');
    }   

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

}
