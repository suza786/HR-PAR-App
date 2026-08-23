<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use SoftDeletes;

    // protected $connection = 'sna_lms';
    // protected $table = 'locations';

    protected $fillable = [
        'location_name',
        'short_name',
        'address',
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class, 'location_id', 'id');
    }
}
