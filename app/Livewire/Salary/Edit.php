<?php

namespace App\Livewire\Salary;

use Livewire\Component;
use App\Models\Salary;
use App\Models\Employee;

class Edit extends Component
{
    public $salary_id, $employee_id, $basic, $perquisites, $pf_organization, $gratuity, $bonus, $income_tax, $efective_date, $salary_year;
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

    public function mount($id)
    {
        $salary = Salary::findOrFail($id);
        $this->salary_id = $salary->id;
        $this->employee_id = $salary->employee_id;
        $this->basic = $salary->basic;
        $this->perquisites = $salary->perquisites;
        $this->pf_organization = $salary->pf_organization;
        $this->gratuity = $salary->gratuity;
        $this->bonus = $salary->bonus;
        $this->income_tax = $salary->income_tax;
        $this->efective_date = $salary->efective_date;
        $this->salary_year = $salary->salary_year;
        $this->employees = Employee::all();
    }

    public function update()
    {
        $this->validate();
        $salary = Salary::findOrFail($this->salary_id);
        $salary->update([
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
        session()->flash('success', 'Salary entry updated successfully.');
        return redirect()->route('salaries.index');
    }

    public function render()
    {
        return view('livewire.salary.edit');
    }
}
