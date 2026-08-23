<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveRequested extends Model
{
    protected $connection = 'sna_lms';

    protected $table = 'leave_requested';

    public function epmloyee()
    {
        return $this->belongsTo(Employee::class, 'emp_id', 'emp_id');
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id', 'leave_type_id');
    }
}