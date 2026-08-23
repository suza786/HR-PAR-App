<?php

namespace App\Livewire\MonthlyActivities;

use App\Models\Employee;
use App\Models\HolidayList;
use App\Models\LeaveRequested;
use App\Models\MonthlyActivity;
use App\Models\ParCalendar;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Create extends Component
{
    public int $selectedYear;

    public string $selectedMonth;

    public string $selectedEmployee = '';

    public array $employees = [];

    public bool $calendarGenerated = false;

    public array $calendarWeeks = [];

    public float $dailyWorkingHours = 8.0;

    public string $calendarMessage = '';

    public string $duplicateMessage = '';

    public array $userLeaves = [];

    public array $holidayList = [];

    public bool $userLeavesLoaded = false;

    public bool $holidayLoaded = false;

    public string $userLeavesMessage = '';

    public string $holidayMessage = '';

    public array $activities = [];

    public array $parCalendarIds = [];

    public function mount(): void
    {
        $this->selectedYear = (int) now()->format('Y');
        $this->selectedMonth = now()->format('F');
        $this->selectedEmployee = (string) Auth::user()->emp_id;
        $this->loadEmployees();
    }

    public function showCalendarData(): void
    {
        $this->clearGeneratedData();

        if (! $this->employeeIsAllowed($this->selectedEmployee)) {
            $this->calendarMessage = 'Please select a valid employee.';

            return;
        }

        $parCalendars = ParCalendar::query()
            ->where('year', $this->selectedYear)
            ->where('month', $this->selectedMonth)
            ->orderBy('week')
            ->get();

        if ($parCalendars->isEmpty()) {
            $this->calendarMessage = "Monthly breakdown is not available for {$this->selectedMonth} {$this->selectedYear}. Please update the PAR Calendar or contact HR.";

            return;
        }

        $this->parCalendarIds = $parCalendars->pluck('id')->map(fn ($id): int => (int) $id)->all();

        if ($this->monthlyActivitiesExist($this->parCalendarIds)) {
            $this->showDuplicateWarning();

            return;
        }

        foreach ($parCalendars as $calendar) {
            $weekStart = Carbon::parse($calendar->weekStart);
            $weekEnd = Carbon::parse($calendar->weekEnd);

            $week = [
                'id' => $calendar->id,
                'week' => $calendar->week,
                'weekStart' => $weekStart->format('d-M-Y'),
                'weekEnd' => $weekEnd->format('d-M-Y'),
                'weekStartDate' => $weekStart->toDateString(),
                'weekEndDate' => $weekEnd->toDateString(),
                'totalDays' => $calendar->total_days,
                'workingDays' => $calendar->working_days,
                'officeHolidays' => (int) ($calendar->office_holidays ?? 0),
            ];

            $this->calendarWeeks[] = $week;
            $this->activities[] = [
                'week_number' => $week['week'],
                'emp_id' => $this->selectedEmployee,
                'par_calendar_id' => $week['id'],
                'leave_hrs' => 0,
                'working_hrs' => $week['workingDays'] * $this->dailyWorkingHours,
                'note' => '',
            ];
        }

        $this->loadUserLeaves();
        $this->loadHolidays();
        $this->calendarGenerated = true;
    }

    public function saveActivities(): void
    {
        if (! $this->employeeIsAllowed($this->selectedEmployee)) {
            $this->addError('selectedEmployee', 'Please select a valid employee.');

            return;
        }

        $this->validate([
            'activities' => ['required', 'array', 'min:1'],
            'activities.*.par_calendar_id' => ['required', 'integer', 'exists:par_calendars,id'],
            'activities.*.leave_hrs' => ['required', 'integer', 'min:0', 'max:255'],
            'activities.*.working_hrs' => ['required', 'integer', 'min:0', 'max:255'],
            'activities.*.note' => ['nullable', 'string', 'max:255'],
        ]);

        $calendarIds = collect($this->activities)
            ->pluck('par_calendar_id')
            ->map(fn ($id): int => (int) $id)
            ->unique()
            ->values()
            ->all();

        if ($this->monthlyActivitiesExist($calendarIds)) {
            $this->showDuplicateWarning();

            return;
        }

        DB::transaction(function (): void {
            foreach ($this->activities as $activity) {
                MonthlyActivity::create([
                    'par_calendar_id' => (int) $activity['par_calendar_id'],
                    'emp_id' => $this->selectedEmployee,
                    'leave_hrs' => (int) $activity['leave_hrs'],
                    'working_hrs' => (int) $activity['working_hrs'],
                    'note' => $activity['note'] ?: null,
                ]);
            }
        });

        session()->flash('success', 'Monthly activities saved successfully.');
        $this->redirect(route('monthly-activities.index'));
    }

    public function resetForm(): void
    {
        $this->selectedEmployee = (string) Auth::user()->emp_id;
        $this->dailyWorkingHours = 8.0;
        $this->clearGeneratedData();
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

        $this->employees = $query->orderBy('emp_id')->get()->toArray();
    }

    private function employeeIsAllowed(string $empId): bool
    {
        return $empId !== '' && collect($this->employees)->contains(
            fn (array $employee): bool => (string) $employee['emp_id'] === $empId
        );
    }

    private function monthlyActivitiesExist(array $calendarIds): bool
    {
        return $calendarIds !== [] && MonthlyActivity::query()
            ->where('emp_id', $this->selectedEmployee)
            ->whereIn('par_calendar_id', $calendarIds)
            ->exists();
    }

    private function showDuplicateWarning(): void
    {
        $this->clearSupplementaryData();
        $this->calendarGenerated = false;
        $this->calendarWeeks = [];
        $this->activities = [];
        $this->duplicateMessage = 'Monthly Activities already created for the user: '.$this->selectedEmployeeLabel();
    }

    private function selectedEmployeeLabel(): string
    {
        $employee = collect($this->employees)->first(
            fn (array $employee): bool => (string) $employee['emp_id'] === $this->selectedEmployee
        );

        return $employee
            ? $employee['emp_id'].' - '.$employee['name']
            : $this->selectedEmployee;
    }

    private function clearGeneratedData(): void
    {
        $this->resetErrorBag();
        $this->calendarGenerated = false;
        $this->calendarWeeks = [];
        $this->activities = [];
        $this->parCalendarIds = [];
        $this->calendarMessage = '';
        $this->duplicateMessage = '';
        $this->clearSupplementaryData();
    }

    private function clearSupplementaryData(): void
    {
        $this->userLeaves = [];
        $this->holidayList = [];
        $this->userLeavesLoaded = false;
        $this->holidayLoaded = false;
        $this->userLeavesMessage = '';
        $this->holidayMessage = '';
    }

    private function loadUserLeaves(): void
    {
        $monthNumber = Carbon::parse("{$this->selectedMonth} 1")->month;
        $startDate = Carbon::create($this->selectedYear, $monthNumber, 1)->startOfDay();
        $endDate = $startDate->copy()->endOfMonth()->endOfDay();

        $leaveRequests = LeaveRequested::query()
            ->with('leaveType')
            ->where('emp_id', $this->selectedEmployee)
            ->where('status', 'Approved')
            ->whereDate('start_date', '<=', $endDate->toDateString())
            ->whereDate('end_date', '>=', $startDate->toDateString())
            ->get();

        foreach ($leaveRequests as $leaveRequest) {
            $this->userLeaves[] = [
                'startDate' => Carbon::parse($leaveRequest->start_date)->format('d-M-Y'),
                'endDate' => Carbon::parse($leaveRequest->end_date)->format('d-M-Y'),
                'leaveType' => $this->resolveLeaveType($leaveRequest),
                'days' => $this->resolveLeaveDays($leaveRequest),
            ];
        }

        $this->userLeavesLoaded = true;
        $this->userLeavesMessage = $leaveRequests->isEmpty() ? $this->emptyLeaveMessage() : '';
    }

    private function loadHolidays(): void
    {
        $monthNumber = Carbon::parse("{$this->selectedMonth} 1")->month;
        $startDate = Carbon::create($this->selectedYear, $monthNumber, 1)->startOfDay();
        $endDate = $startDate->copy()->endOfMonth()->endOfDay();

        $holidays = HolidayList::query()->whereBetween('date_', [$startDate, $endDate])->get();

        foreach ($holidays as $holiday) {
            $this->holidayList[] = [
                'date' => Carbon::parse($holiday->date_)->format('d-M-Y'),
                'title' => $holiday->name ?? 'Holiday',
            ];
        }

        $this->holidayLoaded = true;
        $this->holidayMessage = $holidays->isEmpty() ? $this->emptyHolidayMessage() : '';
    }

    private function resolveLeaveType($leaveRequest): string
    {
        if (! empty($leaveRequest->leaveType?->leave_type)) {
            return (string) $leaveRequest->leaveType->leave_type;
        }

        foreach (['leave_type', 'leave_type_name', 'type', 'leave_name', 'leaveType'] as $field) {
            if (! empty($leaveRequest->{$field})) {
                return (string) $leaveRequest->{$field};
            }
        }

        return 'Leave';
    }

    private function resolveLeaveDays($leaveRequest): float
    {
        foreach (['num_of_days', 'days', 'no_of_days'] as $field) {
            if (! empty($leaveRequest->{$field})) {
                return (float) $leaveRequest->{$field};
            }
        }

        if (! empty($leaveRequest->start_date) && ! empty($leaveRequest->end_date)) {
            return (float) (Carbon::parse($leaveRequest->start_date)->diffInDays(Carbon::parse($leaveRequest->end_date)) + 1);
        }

        return 0.0;
    }

    private function emptyLeaveMessage(): string
    {
        return "You don't have any leave entry for this month. Please check and update the leave information in the Leave System.";
    }

    private function emptyHolidayMessage(): string
    {
        return "You don't have any holidays for this month. Please check the yearly holiday list.";
    }

    public function render()
    {
        return view('livewire.monthly-activities.create');
    }
}
