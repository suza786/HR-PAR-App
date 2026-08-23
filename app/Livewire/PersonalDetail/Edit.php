<?php

namespace App\Livewire\PersonalDetail;

use Livewire\Component;
use App\Models\PersonalDetail;
use App\Models\Employee;

class Edit extends Component
{
    public $personal_detail_id, $employee_id, $father_name, $mother_name, $spouse_name, $spouse_mobile, $personal_email, $present_address, $permanent_address, $national_id, $tin_id, $passport_num, $blood_group, $religion;
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
        'passport_num' => 'nullable|integer',
        'blood_group' => 'nullable|string',
        'religion' => 'nullable|string',
    ];

    public function mount($id)
    {
        $detail = PersonalDetail::findOrFail($id);
        $this->personal_detail_id = $detail->id;
        $this->employee_id = $detail->employee_id;
        $this->father_name = $detail->father_name;
        $this->mother_name = $detail->mother_name;
        $this->spouse_name = $detail->spouse_name;
        $this->spouse_mobile = $detail->spouse_mobile;
        $this->personal_email = $detail->personal_email;
        $this->present_address = $detail->present_address;
        $this->permanent_address = $detail->permanent_address;
        $this->national_id = $detail->national_id;
        $this->tin_id = $detail->tin_id;
        $this->passport_num = $detail->passport_num;
        $this->blood_group = $detail->blood_group;
        $this->religion = $detail->religion;
        $this->employees = Employee::all();
    }

    public function update()
    {
        $this->validate();
        $detail = PersonalDetail::findOrFail($this->personal_detail_id);
        $detail->update([
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
        session()->flash('success', 'Personal detail updated successfully.');
        return redirect()->route('personal-details.index');
    }

    public function render()
    {
        return view('livewire.personal-detail.edit');
    }
}
