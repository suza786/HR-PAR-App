<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EducationTraining extends Model
{
    use SoftDeletes;

    protected $table = "education_trainings";

    // protected $guarded = [];

    protected $fillable = [
        'employee_id',
        'degree',
        'board_university_institute',
        'session_duration',
        'result_marks',
        'year',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
