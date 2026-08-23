<?php

namespace App\Livewire\SalaryAllocation;

use Livewire\Component;
use App\Models\SalaryAllocation;
use App\Models\Employee;
use App\Models\Project;
use Carbon\Carbon;
use Auth;

class Edit extends Component
{
    public $salary_allocation_id, $emp_id, $project_id, $allocation_ratio, $start_month, $end_month, $curr_year;
    public $employees = [];
    public $projects = [];
    public $currentYear;

    protected function rules()
    {
        return [
            'emp_id' => 'required',
            'project_id' => 'required|exists:projects,id',
            'allocation_ratio' => 'required|numeric|min:0|max:1',
            'start_month' => 'required|date|after_or_equal:' . $this->currentYear . '-01-01|before_or_equal:' . $this->currentYear . '-12-31',
            'end_month' => 'required|date|after_or_equal:start_month|after_or_equal:' . $this->currentYear . '-01-01|before_or_equal:' . $this->currentYear . '-12-31',
        ];
    }

    public function mount($id)
    {
        abort_unless(Auth::user()?->user_type === 'HR', 403);

        $allocation = SalaryAllocation::findOrFail($id);
        $this->salary_allocation_id = $allocation->id;
        $this->emp_id = $allocation->emp_id;
        $this->project_id = $allocation->project_id;
        $this->allocation_ratio = $allocation->allocation_ratio;
        $this->start_month = Carbon::parse($allocation->start_month)->format('Y-m-d');
        $this->end_month = Carbon::parse($allocation->end_month)->format('Y-m-d');
        $this->curr_year = $allocation->curr_year;

        $this->employees = Employee::where('user_status', 'enable')->get();
        $this->projects = Project::all();
        $this->currentYear = Carbon::now()->year;
    }

    public function update()
    {
        abort_unless(Auth::user()?->user_type === 'HR', 403);

        $allocation = SalaryAllocation::findOrFail($this->salary_allocation_id);
        $this->emp_id = $allocation->emp_id;
        $this->validate();

        // Additional validation for project end date
        $project = Project::find($this->project_id);
        if ($project && $project->end_date && Carbon::parse($this->end_month)->gt(Carbon::parse($project->end_date))) {
            $this->addError('end_month', 'End month cannot exceed project end date');
            return;
        }

        $startMonth = Carbon::parse($this->start_month)->startOfMonth();
        $endMonth = Carbon::parse($this->end_month)->endOfMonth();
        $overlapExists = SalaryAllocation::where('emp_id', $this->emp_id)
            ->where('id', '!=', $this->salary_allocation_id)
            ->whereDate('start_month', '<=', $endMonth)
            ->whereDate('end_month', '>=', $startMonth)
            ->exists();

        if ($overlapExists) {
            $this->addError('start_month', 'The selected period overlaps an existing salary allocation for this employee.');
            return;
        }

        $allocation->update([
            'emp_id' => $this->emp_id,
            'project_id' => $this->project_id,
            'allocation_ratio' => $this->allocation_ratio,
            'start_month' => $startMonth,
            'end_month' => $endMonth,
        ]);

        session()->flash('success', 'Salary allocation updated successfully.');
        return redirect()->route('salary-allocations.index');
    }

    public function render()
    {
        return view('livewire.salary-allocation.edit');
    }
}