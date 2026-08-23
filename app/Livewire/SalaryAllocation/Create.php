<?php

namespace App\Livewire\SalaryAllocation;

use App\Models\Employee;
use App\Models\Project;
use App\Models\SalaryAllocation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Create extends Component
{
    public $emp_id;

    public $start_month;

    public $end_month;

    public $allocations = [['project_id' => '', 'allocation_ratio' => '']];

    public $employees = [];

    public $projects = [];

    public $currentYear;

    protected function rules()
    {
        $rules = [
            // Employee records live on the sna_lms connection. Passing the model
            // class makes the exists rule use that model's configured connection.
            'emp_id' => ['required', Rule::exists(Employee::class, 'emp_id')],
            'start_month' => 'required|date|after_or_equal:'.$this->currentYear.'-01-01|before_or_equal:'.$this->currentYear.'-12-31',
            'end_month' => 'required|date|after_or_equal:start_month|after_or_equal:'.$this->currentYear.'-01-01|before_or_equal:'.$this->currentYear.'-12-31',
            'allocations' => 'required|array|min:1',
        ];

        foreach ($this->allocations as $index => $allocation) {
            $rules['allocations.'.$index.'.project_id'] = 'required|exists:projects,id';
            $rules['allocations.'.$index.'.allocation_ratio'] = 'required|numeric|min:0|max:1';
        }

        return $rules;
    }

    protected function validationAttributes()
    {
        $attributes = [];

        foreach ($this->allocations as $index => $allocation) {
            $attributes['allocations.'.$index.'.project_id'] = 'Project '.($index + 1);
            $attributes['allocations.'.$index.'.allocation_ratio'] = 'Allocation Ratio '.($index + 1);
        }

        return $attributes;
    }

    public function mount()
    {
        // $this->employees = Employee::where('user_status', 'enable')->get();
        $this->projects = Project::all();
        $this->currentYear = Carbon::now()->year;

        // if(Auth::user()->user_type === 'HR') {
        //     $this->emp_id = null; // HR can select any employee
        // } else {
        //     $this->emp_id = Auth::user()->emp_id; // Non-HR users are assigned their own emp_id
        // }

        // Load employees if user is HR
        if (Auth::user()->user_type === 'HR') {
            $this->employees = Employee::whereUserStatus('enable')->get()->toArray();
        } else {
            $this->employees = Employee::whereUserStatus('enable')->whereSupervisor(Auth::user()->emp_id)->orWhere('emp_id', Auth::user()->emp_id)->get()->toArray();
        }
        $this->emp_id = Auth::user()->emp_id;
    }

    public function addRatio()
    {
        $this->allocations[] = ['project_id' => '', 'allocation_ratio' => ''];
    }

    public function removeRatio($index)
    {
        if (count($this->allocations) > 1) {
            unset($this->allocations[$index]);
            $this->allocations = array_values($this->allocations);
        }
    }

    public function getTotalRatioProperty()
    {
        return collect($this->allocations)->sum(function ($allocation) {
            return (float) ($allocation['allocation_ratio'] ?? 0);
        });
    }

    public function save()
    {
        $this->validate();

        // Additional validation for project end dates
        foreach ($this->allocations as $allocation) {
            $project = Project::find($allocation['project_id']);
            if ($project && $project->end_date && Carbon::parse($this->end_month)->gt(Carbon::parse($project->end_date))) {
                $this->addError('end_month', 'End month cannot exceed project end date for '.$project->project_name);

                return;
            }
        }

        // Check if total ratio is exactly 1.0 (100%)
        if ($this->totalRatio != 1.0) {
            $this->addError('allocations', 'Total allocation ratio must be exactly 100%');

            return;
        }

        $startMonth = Carbon::parse($this->start_month)->startOfMonth();
        $endMonth = Carbon::parse($this->end_month)->endOfMonth();
        $overlapExists = SalaryAllocation::where('emp_id', $this->emp_id)
            ->whereDate('start_month', '<=', $endMonth)
            ->whereDate('end_month', '>=', $startMonth)
            ->exists();

        if ($overlapExists) {
            $this->addError('start_month', 'The selected period overlaps an existing salary allocation for this employee.');

            return;
        }

        foreach ($this->allocations as $allocation) {
            SalaryAllocation::create([
                'emp_id' => $this->emp_id,
                'project_id' => $allocation['project_id'],
                'allocation_ratio' => $allocation['allocation_ratio'],
                'start_month' => $startMonth,
                'end_month' => $endMonth,
                'curr_year' => $this->currentYear,
            ]);
        }

        session()->flash('success', 'Salary allocations created successfully.');

        return redirect()->route('salary-allocations.index');
    }

    public function render()
    {
        return view('livewire.salary-allocation.create');
    }
}
