<?php

namespace App\Livewire\EducationTraining;

use Livewire\Component;
use App\Models\EducationTraining;

class Index extends Component
{
    public $educationTrainings;

    public function mount()
    {
        $this->educationTrainings = EducationTraining::with('employee')->latest()->get();
    }

    public function render()
    {
        return view('livewire.education-training.index');
    }
}
