<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Donor extends Model
{
    use SoftDeletes;

    // protected $guarded = [];

    protected $fillable = [
        'name',
        'country',
    ];

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
