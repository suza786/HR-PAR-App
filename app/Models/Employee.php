<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class Employee extends Authenticatable
{
    protected $connection = 'sna_lms';
    protected $table = 'employees';

    protected $hidden = ['password'];
    protected $guarded = [];

    //use SoftDeletes;

    protected function password(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => Hash::needsRehash($value) ? Hash::make($value) : $value,
        );
    }

    /**
     * Get the employee's initials.
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->filter()
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }
    
    public function empType()
    {
        return $this->belongsTo(EmpType::class, 'emp_type_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(Employee::class, 'supervisor_id');
    }

    public function dependents()
    {
        return $this->hasMany(Dependent::class);
    }

    public function educationTrainings()
    {
        return $this->hasMany(EducationTraining::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
    
    public function projects()
    {
        return $this->hasMany(Project::class);
    }
    
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'employee_role', 'employee_id', 'role_id');
    }

    public function salaries()
    {
        return $this->hasMany(Salary::class);
    }

    public function scopeActive($query)
    {
        return $query->where('user_status', 'active');
    }

    public function scopeWithRoles($query)
    {
        return $query->with('roles');
    }   

    public function personalDetails()
    {
        return $this->hasOne(PersonalDetail::class);
    }

    public function personalDetail()
    {
        return $this->hasOne(PersonalDetail::class);
    }

    public function nominees()
    {
        return $this->hasMany(Nominee::class);
    }

    public function bankAccounts()
    {
        return $this->hasMany(BankAccount::class);
    }
    
    public function salaryAllocations()
    {
        return $this->hasMany(SalaryAllocation::class, 'emp_id');
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequested::class, 'emp_id');
    }
}
