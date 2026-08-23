<?php

namespace App\Livewire\EducationTraining;

use Livewire\Component;
use App\Models\EducationTraining;
use App\Models\Employee;

class Edit extends Component
{
    public $education_training_id, $employee_id, $degree, $board_university_institute, $session_duration, $result_marks, $year;
    public $employees = [];

    protected $rules = [
        'employee_id' => 'required|exists:employees,id',
        'degree' => 'required|string',
        'board_university_institute' => 'required|string',
        'session_duration' => 'nullable|string',
        'result_marks' => 'nullable|string',
        'year' => 'nullable|integer',
    ];

    public function mount($id)
    {
        $et = EducationTraining::findOrFail($id);
        $this->education_training_id = $et->id;
        $this->employee_id = $et->employee_id;
        $this->degree = $et->degree;
        $this->board_university_institute = $et->board_university_institute;
        $this->session_duration = $et->session_duration;
        $this->result_marks = $et->result_marks;
        $this->year = $et->year;
        $this->employees = Employee::all();
    }

    public function update()
    {
        $this->validate();
        $et = EducationTraining::findOrFail($this->education_training_id);
        $et->update([
            'employee_id' => $this->employee_id,
            'degree' => $this->degree,
            'board_university_institute' => $this->board_university_institute,
            'session_duration' => $this->session_duration,
            'result_marks' => $this->result_marks,
            'year' => $this->year,
        ]);
        session()->flash('success', 'Education/Training entry updated successfully.');
        return redirect()->route('education-trainings.index');
    }

    public function render()
    {
        return view('livewire.education-training.edit');
    }
}
