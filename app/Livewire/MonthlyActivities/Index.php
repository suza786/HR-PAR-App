<?php

namespace App\Livewire\MonthlyActivities;

use App\Models\Employee;
use App\Models\MonthlyActivity;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{
    public int $selectedYear;

    public string $selectedMonth;

    public string $selectedEmployee = '';

    public array $employees = [];

    public array $monthlyActivities = [];

    public bool $showMonthlyActivities = false;

    public string $activityMessage = '';

    public function mount(): void
    {
        $this->selectedYear = (int) now()->format('Y');
        $this->selectedMonth = now()->format('F');
        $this->selectedEmployee = (string) Auth::user()->emp_id;
        $this->loadEmployees();
    }

    public function showMonthlyActivityData(): void
    {
        $this->monthlyActivities = [];
        $this->activityMessage = '';
        $this->showMonthlyActivities = true;

        if (! $this->employeeIsAllowed($this->selectedEmployee)) {
            $this->activityMessage = 'Please select a valid employee.';

            return;
        }

        $activities = MonthlyActivity::query()
            ->with(['parCalendar', 'employee'])
            ->where('emp_id', $this->selectedEmployee)
            ->whereHas('parCalendar', function ($query): void {
                $query->where('year', $this->selectedYear)
                    ->where('month', $this->selectedMonth);
            })
            ->get();

        if ($activities->isEmpty()) {
            $this->activityMessage = "Monthly Activities have not been created for {$this->selectedMonth} {$this->selectedYear}.";

            return;
        }

        $this->monthlyActivities = $activities->toArray();
    }

    private function loadEmployees(): void
    {
        $authUser = Auth::user();

        $query = Employee::query()->whereUserStatus('enable');

        if ($authUser->user_type !== 'HR') {
            $query->where(function ($query) use ($authUser): void {
                $query->where('supervisor', $authUser->emp_id)
                    ->orWhere('emp_id', $authUser->emp_id);
            });
        }

        $this->employees = $query->orderBy('name')->get()->toArray();
    }

    private function employeeIsAllowed(string $empId): bool
    {
        return $empId !== '' && collect($this->employees)->contains(
            fn (array $employee): bool => (string) $employee['emp_id'] === $empId
        );
    }

    public function render()
    {
        return view('livewire.monthly-activities.index');
    }
}
