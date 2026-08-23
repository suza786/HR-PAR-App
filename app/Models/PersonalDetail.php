<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PersonalDetail extends Model
{
    use SoftDeletes;


    protected $fillable = [
        'employee_id',
        'father_name',
        'mother_name',
        'spouse_name',
        'spouse_mobile',
        'personal_email',
        'present_address',
        'permanent_address',
        'national_id',
        'tin_id',
        'passport_num',
        'blood_group',
        'religion',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

}
