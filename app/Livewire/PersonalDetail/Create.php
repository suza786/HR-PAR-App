<?php

namespace App\Livewire\PersonalDetail;

use Livewire\Component;
use App\Models\PersonalDetail;
use App\Models\Employee;

class Create extends Component
{
    public $employee_id, $father_name, $mother_name, $spouse_name, $spouse_mobile, $personal_email, $present_address, $permanent_address, $national_id, $tin_id, $passport_num, $blood_group, $religion;
    public $employees = [];

    protected $rules = [
        'employee_id' => 'required|exists:employees,id',
        'father_name' => 'nullable|string',
        'mother_name' => 'nullable|string',
        'spouse_name' => 'nullable|string',
        'spouse_mobile' => 'nullable|string',
        'personal_email' => 'nullable|email',
        'present_address' => 'nullable|string',
        'permanent_address' => 'nullable|string',
        'national_id' => 'nullable|string',
        'tin_id' => 'nullable|string',
        'passport_num' => 'nullable|string|max:20',
        'blood_group' => 'nullable|string',
        'religion' => 'nullable|string',
    ];

    public function mount()
    {
        $this->employees = Employee::all();
    }

    public function save()
    {
        $this->validate();
        PersonalDetail::create([
            'employee_id' => $this->employee_id,
            'father_name' => $this->father_name,
            'mother_name' => $this->mother_name,
            'spouse_name' => $this->spouse_name,
            'spouse_mobile' => $this->spouse_mobile,
            'personal_email' => $this->personal_email,
            'present_address' => $this->present_address,
            'permanent_address' => $this->permanent_address,
            'national_id' => $this->national_id,
            'tin_id' => $this->tin_id,
            'passport_num' => $this->passport_num,
            'blood_group' => $this->blood_group,
            'religion' => $this->religion,
        ]);
        session()->flash('success', 'Personal detail added successfully.');
        return redirect()->route('personal-details.index');
    }

    public function render()
    {
        return view('livewire.personal-detail.create');
    }
}
