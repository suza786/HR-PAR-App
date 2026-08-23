<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Salary extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'salary';

    protected $fillable = [
        'employee_id',
        'basic',
        'perquisites',
        'pf_organization',
        'gratuity',
        'bonus',
        'income_tax',
        'efective_date',
        'salary_year',
    ];

    protected $dates = [
        'efective_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    
    // Relationships, accessors, mutators can be added here
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
