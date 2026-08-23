<?php

namespace App\Livewire\Employee;

use Livewire\Component;
use App\Models\Employee;

class Index extends Component
{
    public function render()
    {
        $employees = Employee::with(['department'])->get();
        return view('livewire.employee.index', compact('employees'));
    }
}
