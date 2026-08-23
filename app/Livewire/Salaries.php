<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Salary;
use App\Models\Employee;
use Illuminate\Support\Carbon;

class Salaries extends Component
{
    public $salary_id, $employee_id, $basic, $perquisites, $pf_organization, $gratuity;
    public $bonus, $income_tax, $efective_date, $salary_year;

    public $employees = [];
    public $salaries = [];
    public $isEdit = false;
    public $showCreateModal = false;
    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
    }

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
        $this->salaries = Salary::with('employee')->latest()->get();
    }

    public function render()
    {
        return view('livewire.salaries');
    }

    public function resetForm()
    {
        $this->salary_id = null;
        $this->employee_id = '';
        $this->basic = '';
        $this->perquisites = '';
        $this->pf_organization = '';
        $this->gratuity = '';
        $this->bonus = '';
        $this->income_tax = '';
        $this->efective_date = '';
        $this->salary_year = now()->year;
        $this->isEdit = false;
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
        $this->resetForm();
        $this->salaries = Salary::with('employee')->latest()->get();
        session()->flash('success', 'Salary entry added successfully.');
    }

    public function edit($id)
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
        $this->isEdit = true;
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
        $this->resetForm();
        $this->salaries = Salary::with('employee')->latest()->get();
        session()->flash('success', 'Salary entry updated successfully.');
    }

    public function delete($id)
    {
        Salary::findOrFail($id)->delete();
        $this->salaries = Salary::with('employee')->latest()->get();
        session()->flash('success', 'Salary entry deleted successfully.');
    }
}
