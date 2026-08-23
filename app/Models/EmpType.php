<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmpType extends Model
{
    use SoftDeletes;

    protected $table = "emp_types";

    protected $fillable = [
        'emp_type',
        'type_description',
    ];
}
