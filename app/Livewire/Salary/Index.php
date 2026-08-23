<?php

namespace App\Livewire\Salary;

use Livewire\Component;
use App\Models\Salary;
use App\Models\Employee;

class Index extends Component
{
    public $salaries;

    public function mount()
    {
        $this->salaries = Salary::with('employee')->latest()->get();
    }

    public function render()
    {
        return view('livewire.salary.index');
    }
}
