<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeLocation extends Model
{
    protected $connection = 'sna_lms';

    protected $table = 'emp_location';

    protected $guarded = [];
}
