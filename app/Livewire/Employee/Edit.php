<?php

namespace App\Livewire\Employee;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Employee;
use App\Models\EmpType;
use App\Models\Location;
use App\Models\Department;
use App\Models\Role;

class Edit extends Component
{
    use WithFileUploads;

    public $employee_id, $emp_id, $emp_type_id, $supervisor_id, $name, $password, $password_confirmation, $gender, $email, $birth_date, $join_date, $location_id, $department_id, $position, $mobile, $user_status, $separation_date, $photo, $role_ids = [];

    protected $rules = [
        'emp_id' => 'required|string|max:255',
        'emp_type_id' => 'required|integer|exists:emp_types,id',
        'supervisor_id' => 'nullable|integer|exists:employees,id',
        'name' => 'required|string|max:255',
        'password' => 'nullable|string|min:6|confirmed',
        'gender' => 'required|string|max:10',
        'email' => 'required|email|max:255',
        'birth_date' => 'required|date',
        'join_date' => 'required|date',
        'location_id' => 'required|integer|exists:locations,id',
        'department_id' => 'required|integer|exists:departments,id',
        'position' => 'nullable|string|max:255',
        'mobile' => 'nullable|string|max:20',
        'user_status' => 'nullable|string|max:50',
        'separation_date' => 'nullable|date',
        'photo' => 'nullable|image|max:2048',
        'role_ids' => 'array',
        'role_ids.*' => 'integer|exists:roles,id',
    ];

    public function mount($id)
    {
        $employee = Employee::with('roles')->findOrFail($id);
        $this->employee_id = $employee->id;
        $this->emp_id = $employee->emp_id;
        $this->emp_type_id = $employee->emp_type_id;
        $this->supervisor_id = $employee->supervisor_id;
        $this->name = $employee->name;
        $this->password = '';
        $this->password_confirmation = '';
        $this->gender = $employee->gender;
        $this->email = $employee->email;
        $this->birth_date = $employee->birth_date;
        $this->join_date = $employee->join_date;
        $this->location_id = $employee->location_id;
        $this->department_id = $employee->department_id;
        $this->position = $employee->position;
        $this->mobile = $employee->mobile;
        $this->user_status = $employee->user_status;
        $this->separation_date = $employee->separation_date;
        $this->photo = null;
        $this->role_ids = $employee->roles->pluck('id')->toArray();
    }

    public function submit()
    {
        $this->validate();
        $employee = Employee::findOrFail($this->employee_id);
        $employee->update([
            'emp_id' => $this->emp_id,
            'emp_type_id' => $this->emp_type_id,
            'supervisor_id' => $this->supervisor_id,
            'name' => $this->name,
            'password' => $this->password ? bcrypt($this->password) : $employee->password,
            'gender' => $this->gender,
            'email' => $this->email,
            'birth_date' => $this->birth_date,
            'join_date' => $this->join_date,
            'location_id' => $this->location_id,
            'department_id' => $this->department_id,
            'position' => $this->position,
            'mobile' => $this->mobile,
            'user_status' => $this->user_status,
            'separation_date' => $this->separation_date,
            'photo' => $this->photo ? $this->photo->store('photos', 'public') : $employee->photo,
        ]);
        $employee->roles()->sync($this->role_ids);
        return redirect()->route('employees.index');
    }

    public function render()
    {
        return view('livewire.employee.edit', [
            'empTypes' => EmpType::all(),
            'supervisors' => Employee::all(),
            'locations' => Location::all(),
            'departments' => Department::all(),
            'rolesList' => Role::all(),
        ]);
    }
}
