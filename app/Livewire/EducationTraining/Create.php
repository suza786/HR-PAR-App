<?php

namespace App\Livewire\EducationTraining;

use Livewire\Component;
use App\Models\EducationTraining;
use App\Models\Employee;

class Create extends Component
{
    public $employee_id, $degree, $board_university_institute, $session_duration, $result_marks, $year;
    public $employees = [];

    protected $rules = [
        'employee_id' => 'required|exists:employees,id',
        'degree' => 'required|string',
        'board_university_institute' => 'required|string',
        'session_duration' => 'nullable|string',
        'result_marks' => 'nullable|string',
        'year' => 'nullable|integer',
    ];

    public function mount()
    {
        $this->employees = Employee::all();
    }

    public function save()
    {
        $this->validate();
        EducationTraining::create([
            'employee_id' => $this->employee_id,
            'degree' => $this->degree,
            'board_university_institute' => $this->board_university_institute,
            'session_duration' => $this->session_duration,
            'result_marks' => $this->result_marks,
            'year' => $this->year,
        ]);
        session()->flash('success', 'Education/Training entry added successfully.');
        return redirect()->route('education-trainings.index');
    }

    public function render()
    {
        return view('livewire.education-training.create');
    }
}
