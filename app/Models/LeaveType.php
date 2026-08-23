<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    protected $connection = 'sna_lms';

    protected $table = 'leave_type';

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequested::class, 'leave_type_id', 'leave_type_id');
    }
}