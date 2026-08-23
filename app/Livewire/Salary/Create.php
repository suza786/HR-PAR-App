<?php

namespace App\Livewire\Salary;

use Livewire\Component;
use App\Models\Salary;
use App\Models\Employee;

class Create extends Component
{
    public $employee_id, $basic, $perquisites, $pf_organization, $gratuity, $bonus, $income_tax, $efective_date, $salary_year;
    public $employees = [];

    protected $rules = [
        'employee_id' => 'required|exists:employees,id',
        'basic' => 'nullable|numeric',
        'perquisites' => 'nullable|numeric',
        'pf_organization' => 'nullable|numeric',
        'gratuity' => 'nullable|numeric',
        'bonus' => 'nullable|numeric',
        'income_tax' => 'nullable|numeric',
        'efective_date' => 'nullable|date',
    ];

    public function mount()
    {
        $this->employees = Employee::all();
        $this->salary_year = now()->year;
    }

    public function save()
    {
        $this->validate();
        Salary::create([
            'employee_id' => $this->employee_id,
            'basic' => $this->basic,
            'perquisites' => $this->perquisites,
            'pf_organization' => $this->pf_organization,
            'gratuity' => $this->gratuity,
            'bonus' => $this->bonus,
            'income_tax' => $this->income_tax,
            'efective_date' => $this->efective_date,
            'salary_year' => $this->salary_year,
        ]);
        session()->flash('success', 'Salary entry added successfully.');
        return redirect()->route('salaries.index');
    }

    public function render()
    {
        return view('livewire.salary.create');
    }
}
