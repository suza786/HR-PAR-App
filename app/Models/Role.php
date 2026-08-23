<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Str;

class Role extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'role_name',
        'slug',
        'note',
    ];

    public function setRoleNameAttribute($value)
    {
        $this->attributes['role_name'] = ucfirst($value);
    }

    // public function setSlugAttribute()
    // {
    //     $this->attributes['slug'] = Str::slug($this->role_name);
    // }

    public function emloyees()
    {
        return $this->belongsToMany(Employee::class, 'employee_role', 'role_id', 'employee_id');
    }
}
