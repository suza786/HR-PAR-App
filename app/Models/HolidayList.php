<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HolidayList extends Model
{
    protected $connection = 'sna_lms';

    protected $table = 'holiday_list';
}