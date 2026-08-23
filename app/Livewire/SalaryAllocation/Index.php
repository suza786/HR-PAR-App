<?php

namespace App\Livewire\SalaryAllocation;

use Livewire\Component;
use App\Models\SalaryAllocation;
use App\Models\Employee;
use Auth;
use Carbon\Carbon;

class Index extends Component
{
    public $salaryAllocations = [];
    public $employees = [];
    public $selectedYear;
    public $selectedMonth;
    public $selectedEmployee = '';
    public bool $showSalaryAllocations = false;
    public string $allocationMessage = '';

    public function mount()
    {
        $this->selectedYear = Carbon::now()->year;
        $this->selectedMonth = Carbon::now()->format('F');

        // Load employees if user is HR
        if ($this->isHr()) {
            $this->employees = Employee::where('user_status', 'enable')->get();
            return;
        }
        // if (Auth::user()->user_type === 'HR') {
        //     $this->employees = Employee::whereUserStatus('enable')->get()->toArray();
        // } else {
        //     $this->employees = Employee::whereUserStatus('enable')->whereSupervisor(Auth::user()->emp_id)->orWhere('emp_id', Auth::user()->emp_id)->get()->toArray();
        // }  

        $this->selectedEmployee = Auth::user()->emp_id;
        $this->loadSalaryAllocations(Auth::user()->emp_id, $this->selectedYear);
    }

    public function showSalaryAllocationData(): void
    {
        if (empty($this->selectedEmployee)) {
            $this->salaryAllocations = [];
            $this->showSalaryAllocations = true;
            $this->allocationMessage = 'Please select an employee.';
            return;
        }

        $this->loadSalaryAllocations($this->selectedEmployee, $this->selectedYear, $this->selectedMonth);
    }

    private function loadSalaryAllocations(string $empId, int $year, ?string $month = null): void
    {
        $this->salaryAllocations = SalaryAllocation::with(['employee', 'project'])
            ->where('emp_id', $empId)
            ->when($month === null, function ($query) use ($year) {
                $query->where('curr_year', $year);
            })
            ->when($month !== null, function ($query) use ($year, $month) {
                $monthStart = Carbon::createFromFormat('Y-F-d', $year . '-' . $month . '-01')->startOfMonth();
                $query->whereDate('start_month', '<=', $monthStart->copy()->endOfMonth())
                    ->whereDate('end_month', '>=', $monthStart);
            })
            ->latest()
            ->get();
        $this->showSalaryAllocations = true;
        $this->allocationMessage = $this->salaryAllocations->isEmpty()
            ? 'Salary allocation information is not available for the selected period and employee: ' . $this->selectedEmployee
            : '';
    }

    private function isHr(): bool
    {
        return Auth::user()?->user_type === 'HR';
    }

    public function render()
    {
        return view('livewire.salary-allocation.index');
    }
}