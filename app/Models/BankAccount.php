<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankAccount extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employee_id',
        'account_title',
        'ac_number',
        'bank_name',
        'branch',
        'swift_code',
        'routing_num',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
