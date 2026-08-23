<?php

namespace App\Livewire\ActivityAllocation;

use App\Models\Activity;
use App\Models\ActivityAllocation;
use App\Models\Employee;
use App\Models\MonthlyActivity;
use App\Models\Project;
use App\Models\SalaryAllocation;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Create extends Component
{
    public int $selectedYear;

    public string $selectedMonth;

    public string $selectedEmployeeId = '';

    public bool $monthlyActivitiesLoaded = false;

    public bool $salaryAllocationsLoaded = false;

    public bool $activityAllocationsAlreadyCreated = false;

    public ?string $monthlyActivitiesMessage = null;

    public ?string $salaryAllocationsMessage = null;

    public ?string $activityAllocationsMessage = null;

    public ?string $weeklyHoursError = null;

    public $employees = [];

    public $monthlyActivities = [];

    public $salaryAllocations = [];

    public $activityAllocations = [];

    public $weeklyTotals = [];

    public $weeklyTotalsExpected = [];

    public $projects = [];

    public $activities = [];

    public array $months = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December',
    ];

    public function mount(): void
    {
        $this->selectedYear = (int) now()->format('Y');
        $this->selectedMonth = now()->format('F');
        $this->projects = Project::all();
        $this->activities = Activity::orderBy('name')->get();

        // Load employees if user is HR
        if (Auth::user()->user_type === 'HR') {
            $this->employees = Employee::whereUserStatus('enable')->get()->toArray();
            $this->selectedEmployeeId = Auth::user()->emp_id;
        } else {
            $this->employees = Employee::whereUserStatus('enable')->whereSupervisor(Auth::user()->emp_id)->orWhere('emp_id', Auth::user()->emp_id)->get()->toArray();
            $this->selectedEmployeeId = Auth::user()->emp_id;
        }
    }

    // public function getSelectedEmployeeCodeProperty(): ?string
    // {
    //     return optional(($this->selectedEmployeeId));
    //     // return optional(Employee::find($this->selectedEmployeeId))->emp_id;
    // }

    public function getSelectedMonthNumberProperty(): int
    {
        return array_search($this->selectedMonth, $this->months, true) + 1;
    }

    protected function makeRowKey(): string
    {
        return str_replace('.', '_', uniqid('activity_', true));
    }

    public function updatedSelectedEmployeeId(): void
    {
        $this->resetSectionStates();
    }

    public function updatedSelectedMonth(): void
    {
        $this->resetSectionStates();
    }

    public function updatedSelectedYear(): void
    {
        $this->resetSectionStates();
    }

    public function resetSectionStates(): void
    {
        $this->monthlyActivitiesLoaded = false;
        $this->salaryAllocationsLoaded = false;
        $this->activityAllocationsAlreadyCreated = false;
        $this->monthlyActivitiesMessage = null;
        $this->salaryAllocationsMessage = null;
        $this->activityAllocationsMessage = null;
        $this->weeklyHoursError = null;
        $this->monthlyActivities = [];
        $this->salaryAllocations = [];
        $this->activityAllocations = [];
        $this->weeklyTotals = [];
        $this->weeklyTotalsExpected = [];
    }

    public function showMonthlyActivities(): void
    {
        $this->resetSectionStates();

        if (! $this->selectedEmployeeId) {
            $this->monthlyActivitiesMessage = 'Please select an employee.';

            return;
        }

        // $employeeCode = $this->selectedEmployeeCode;
        $employeeCode = $this->selectedEmployeeId;

        if (! $employeeCode) {
            $this->monthlyActivitiesMessage = 'Selected employee is invalid.';

            return;
        }

        $this->monthlyActivities = MonthlyActivity::with(['parCalendar', 'employee'])
            ->where('emp_id', $employeeCode)
            ->whereHas('parCalendar', function ($query) {
                $query->where('year', $this->selectedYear)
                    ->where('month', $this->selectedMonth);
            })
            ->get();

        $this->monthlyActivitiesLoaded = true;

        if ($this->monthlyActivities->isEmpty()) {
            $this->monthlyActivitiesMessage = 'Monthly Activity information not updated for the employee';

            return;
        }

        if ($this->hasExistingActivityAllocations()) {
            $this->activityAllocationsAlreadyCreated = true;
            $this->activityAllocationsMessage = 'Activity allocations have already been created for this employee and selected period.';

            return;
        }

        $this->loadSalaryAllocations();
    }

    protected function hasExistingActivityAllocations(): bool
    {
        return ActivityAllocation::whereHas('monthlyActivity', function ($query) {
            $query->where('emp_id', $this->selectedEmployeeId)
                ->whereHas('parCalendar', function ($calendarQuery) {
                    $calendarQuery->where('year', $this->selectedYear)
                        ->where('month', $this->selectedMonth);
                });
        })->exists();
    }

    public function loadSalaryAllocations(): void
    {
        $employeeCode = $this->selectedEmployeeId;

        if (! $employeeCode) {
            $this->salaryAllocationsMessage = 'Salary Allocations information not updated for the employee. Please update the information before proceed';

            return;
        }

        $monthStart = Carbon::createFromFormat('!Y-m-d', sprintf('%s-%02d-01', $this->selectedYear, $this->selectedMonthNumber))->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();

        $this->salaryAllocations = SalaryAllocation::with('project')
            ->where('emp_id', $employeeCode)
            ->where('curr_year', $this->selectedYear)
            ->whereDate('start_month', '<=', $monthEnd)
            ->whereDate('end_month', '>=', $monthStart)
            ->get();

        $this->salaryAllocationsLoaded = $this->salaryAllocations->isNotEmpty();

        if (! $this->salaryAllocationsLoaded) {
            $this->salaryAllocationsMessage = 'Salary Allocations information not updated for the employee. Please update the information before proceed';

            return;
        }

        $this->loadActivityAllocations();
    }

    public function loadActivityAllocations(): void
    {
        $weeklyActivities = $this->monthlyActivities->sortBy(fn ($activity) => $activity->parCalendar->week)->values();

        if ($weeklyActivities->isEmpty()) {
            return;
        }

        $this->weeklyTotalsExpected = $weeklyActivities->mapWithKeys(function ($activity) {
            return [$activity->id => (float) $activity->working_hrs];
        })->toArray();

        $rows = [];

        foreach ($this->salaryAllocations as $allocation) {
            $weeklyHrs = $weeklyActivities->mapWithKeys(function ($activity) use ($allocation) {
                return [$activity->id => number_format((float) $activity->working_hrs * (float) $allocation->allocation_ratio, 2, '.', '')];
            })->toArray();

            $rowKey = $this->makeRowKey();
            $rows[$rowKey] = [
                'row_key' => $rowKey,
                'project_id' => $allocation->project_id,
                'project_name' => $allocation->project->project_name ?? 'N/A',
                'activity_id' => '',
                'weekly_hrs' => $weeklyHrs,
                'is_added' => false,
            ];
        }

        $this->activityAllocations = $rows;

        $this->computeWeeklyTotals();
    }

    public function updatedActivityAllocations(): void
    {
        $this->computeWeeklyTotals();
    }

    protected function computeWeeklyTotals(): void
    {
        $this->weeklyTotals = [];

        foreach ($this->weeklyTotalsExpected as $monthlyActivityId => $expected) {
            $this->weeklyTotals[$monthlyActivityId] = number_format(
                collect($this->activityAllocations)->sum(fn ($row) => (float) ($row['weekly_hrs'][$monthlyActivityId] ?? 0)),
                2,
                '.',
                ''
            );
        }
    }

    public function addActivity(string $rowKey): void
    {
        if (! isset($this->activityAllocations[$rowKey])) {
            return;
        }

        $sourceRow = $this->activityAllocations[$rowKey];
        $newRowKey = $this->makeRowKey();
        $newRow = [
            'row_key' => $newRowKey,
            'project_id' => $sourceRow['project_id'],
            'project_name' => $sourceRow['project_name'],
            'activity_id' => '',
            'weekly_hrs' => array_map(
                fn ($hours) => '0.00',
                $sourceRow['weekly_hrs'] ?? []
            ),
            'is_added' => true,
        ];

        $rows = [];
        foreach ($this->activityAllocations as $key => $row) {
            $rows[$key] = $row;

            if ($key === $rowKey) {
                $rows[$newRowKey] = $newRow;
            }
        }

        $this->activityAllocations = $rows;
        $this->resetValidation();
        $this->computeWeeklyTotals();
    }

    public function removeActivity(string $rowKey): void
    {
        if (! ($this->activityAllocations[$rowKey]['is_added'] ?? false)) {
            return;
        }

        unset($this->activityAllocations[$rowKey]);
        $this->resetValidation();
        $this->computeWeeklyTotals();
    }

    public function save()
    {
        $employee = new Employee;
        $employeeTable = $employee->getConnectionName().'.'.$employee->getTable();

        $this->validate([
            'selectedEmployeeId' => ['required', Rule::exists($employeeTable, 'emp_id')],
            'activityAllocations' => 'required|array|min:1',
        ], [], [
            'selectedEmployeeId' => 'employee',
        ]);

        if ($this->hasExistingActivityAllocations()) {
            $this->activityAllocationsAlreadyCreated = true;
            $this->activityAllocationsMessage = 'Activity allocations have already been created for this employee and selected period.';

            return;
        }

        foreach ($this->activityAllocations as $index => $row) {
            $this->validate([
                "activityAllocations.{$index}.project_id" => 'required|exists:projects,id',
                "activityAllocations.{$index}.activity_id" => 'required|exists:activities,id',
            ]);

            foreach ($row['weekly_hrs'] as $monthlyActivityId => $hours) {
                $this->validate([
                    "activityAllocations.{$index}.weekly_hrs.{$monthlyActivityId}" => 'required|numeric|min:0',
                ]);
            }
        }

        foreach ($this->weeklyTotalsExpected as $monthlyActivityId => $expected) {
            $sum = collect($this->activityAllocations)->sum(fn ($row) => (float) ($row['weekly_hrs'][$monthlyActivityId] ?? 0));
            $weekNumber = array_search($monthlyActivityId, array_keys($this->weeklyTotalsExpected), true);
            $weekLabel = $weekNumber !== false ? $weekNumber + 1 : '?';

            if (abs($sum - $expected) > 0.001) {
                $this->weeklyHoursError = "Total allocated hours for Week {$weekLabel} must equal ".number_format($expected, 2).'.';

                return;
            }
        }

        $this->weeklyHoursError = null;

        foreach ($this->activityAllocations as $row) {
            foreach ($row['weekly_hrs'] as $monthlyActivityId => $hours) {
                $allocationData = [
                    'monthly_activity_id' => (int) $monthlyActivityId,
                    'project_id' => (int) $row['project_id'],
                    'activity_id' => (int) $row['activity_id'],
                    'working_hrs' => round((float) $hours, 2),
                ];

                if (Schema::hasColumn('activity_allocations', 'activity_description')) {
                    $allocationData['activity_description'] = '';
                }

                ActivityAllocation::create($allocationData);
            }
        }

        session()->flash('success', 'Activity allocations saved successfully.');

        return redirect()->route('activity-allocations.index');
    }

    public function render()
    {
        return view('livewire.activity-allocations.create');
    }
}
