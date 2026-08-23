<?php

namespace App\Livewire\MonthlyActivities;

use Livewire\Component;
use App\Models\MonthlyActivity;
use App\Models\ParCalendar;
use App\Models\Employee;
use Carbon\Carbon;

class Edit extends Component
{
    // ──────────────────────────────────────────────
    // Public state — PAR Calendar Section
    // ──────────────────────────────────────────────
    public int $selectedYear;
    public string $selectedMonth;
    public bool $calendarGenerated = false;
    public array $calendarWeeks = [];
    public string $calendarMessage = '';
    public array $userLeaves = [];
    public bool $userLeavesLoaded = false;
    public string $userLeavesMessage = '';

    // ──────────────────────────────────────────────
    // Public state — Monthly Activities Section
    // ──────────────────────────────────────────────
    public array $activities = [];
    public $employees = [];
    public $parCalendarIds = [];
    public $monthlyActivityId;
    public int|string $selectedEmployee = 0;

    // ──────────────────────────────────────────────
    // Lifecycle
    // ──────────────────────────────────────────────
    public function mount($id): void
    {
        $this->monthlyActivityId = $id;
        $this->employees = Employee::all();

        // Load the monthly activity and related par_calendar
        $activity = MonthlyActivity::findOrFail($id);
        $parCalendar = $activity->parCalendar;

        // dd($activity, $parCalendar);

        $this->selectedEmployee = ($activity->emp_id ?? ($this->employees->first()?->id ?? 0));

        $this->selectedYear = (int) $parCalendar->year;
        $this->selectedMonth = $parCalendar->month;

        // Load calendar data
        $this->showCalendarData();

        // Load existing activities for this par_calendar
        $this->loadExistingActivities($parCalendar->id);
    }

    // ──────────────────────────────────────────────
    // Show Calendar Data
    // ──────────────────────────────────────────────
    public function showCalendarData(): void
    {
        $this->calendarGenerated = false;
        $this->calendarWeeks = [];
        $this->calendarMessage = '';
        $this->userLeaves = [];
        $this->userLeavesLoaded = false;
        $this->userLeavesMessage = '';

        // Fetch data from ParCalendar table
        $parCalendars = ParCalendar::where('year', $this->selectedYear)
            ->where('month', $this->selectedMonth)
            ->orderBy('week')
            ->get();

        if ($parCalendars->isEmpty()) {
            $this->calendarMessage = 'Monthly breakdown is not available.';
            $this->loadUserLeaves();
            return;
        }

        // Format the weeks
        foreach ($parCalendars as $calendar) {
            $this->calendarWeeks[] = [
                'id' => $calendar->id,
                'week' => $calendar->week,
                'weekStart' => Carbon::parse($calendar->weekStart)->format('d-M-Y'),
                'weekEnd' => Carbon::parse($calendar->weekEnd)->format('d-M-Y'),
                'totalDays' => $calendar->total_days,
                'workingDays' => $calendar->working_days,
                'officeHolidays' => (int) ($calendar->office_holidays ?? 0),
            ];
            $this->parCalendarIds[] = $calendar->id;
        }

        $this->loadUserLeaves();
        $this->calendarGenerated = true;
    }

    private function loadUserLeaves(): void
    {
        $this->userLeaves = [];
        $this->userLeavesLoaded = false;
        $this->userLeavesMessage = '';

        $empId = $this->selectedEmployee;

        if (empty($empId) || $empId == 0) {
            $this->userLeavesLoaded = true;
            $this->userLeavesMessage = $this->emptyLeaveMessage();
            return;
        }

        $monthNumber = Carbon::parse("{$this->selectedMonth} 1")->month;
        $startDate = Carbon::create((int) $this->selectedYear, $monthNumber, 1)->startOfDay();
        $endDate = $startDate->copy()->endOfMonth()->endOfDay();

        $leaveRequests = \App\Models\LeaveRequested::query()
            ->with('leaveType')
            ->where('emp_id', $empId)
            ->where(function ($query) {
                $query->where('status', 'Approved')
                    ->orWhere('status', 'approved')
                    ->orWhere('status', 'APPROVED');
            })
            ->whereDate('start_date', '<=', $endDate->toDateString())
            ->whereDate('end_date', '>=', $startDate->toDateString())
            ->get();

        foreach ($leaveRequests as $leaveRequest) {
            $this->userLeaves[] = [
                'startDate' => Carbon::parse($leaveRequest->start_date)->format('Y-m-d'),
                'endDate' => Carbon::parse($leaveRequest->end_date)->format('Y-m-d'),
                'leaveType' => $this->resolveLeaveType($leaveRequest),
                'days' => $this->resolveLeaveDays($leaveRequest),
            ];
        }

        $this->userLeavesLoaded = true;

        if (empty($this->userLeaves)) {
            $this->userLeavesMessage = $this->emptyLeaveMessage();
        }
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

    private function emptyLeaveMessage(): string
    {
        return "You don't have ant leave entry for the month {$this->selectedMonth} ....";
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

    // ──────────────────────────────────────────────
    // Load Existing Activities
    // ──────────────────────────────────────────────
    private function loadExistingActivities($parCalendarId): void
    {
        $this->activities = [];

        // Get all activities for this par_calendar
        $existingActivities = MonthlyActivity::where('par_calendar_id', $parCalendarId)->get();
        
        // foreach ($this->calendarWeeks as $week) {
        //     $existing = $existingActivities->firstWhere('par_calendar_id', $week['id']);

        //     $this->activities[] = [
        //         'id' => $existing?->id,
        //         'par_calendar_id' => $week['id'],
        //         'week_number' => $week['week'],
        //         'emp_id' => $existing?->emp_id ?? $this->selectedEmployee,
        //         'leave_hrs' => $existing?->leave_hrs ?? '',
        //         'working_hrs' => $existing?->working_hrs ?? '',
        //         'note' => $existing?->note ?? '',
        //     ];
        // }

        // dd($existingActivities->toArray('par_calendar_id'));
        
         foreach ($existingActivities as $activity) {
            $this->activities[] = [
                'id' => $activity->id,
                'par_calendar_id' => $activity->par_calendar_id,
                'week_number' => optional($activity->parCalendar)->week ?? '',
                'emp_id' => $activity->emp_id,
                'leave_hrs' => $activity->leave_hrs,
                'working_hrs' => $activity->working_hrs,
                'note' => $activity->note,
            ];
        }
        
    }

    // ──────────────────────────────────────────────
    // Update Activities
    // ──────────────────────────────────────────────
    public function updateActivities(): void
    {
        if(Auth::user()->user_type !== 'HR') {
            session()->flash('error', 'You are not authorized to update activities.');
            return;
        }
        
        foreach ($this->activities as $activity) {
            if (!empty($activity['par_calendar_id']) && !empty($activity['emp_id'])) {
                if (!empty($activity['id'])) {
                    // Update existing
                    $existing = MonthlyActivity::find($activity['id']);
                    if ($existing) {
                        $existing->update([
                            'emp_id' => $this->selectedEmployee,
                            'leave_hrs' => (int) ($activity['leave_hrs'] ?? 0),
                            'working_hrs' => (int) ($activity['working_hrs'] ?? 0),
                            'note' => $activity['note'] ?? '',
                        ]);
                    }
                } 
                // else {
                //     // Create new
                //     MonthlyActivity::create([
                //         'par_calendar_id' => (int) $activity['par_calendar_id'],
                //         'emp_id' => $this->selectedEmployee,
                //         'leave_hrs' => (int) ($activity['leave_hrs'] ?? 0),
                //         'working_hrs' => (int) ($activity['working_hrs'] ?? 0),
                //         'note' => $activity['note'] ?? '',
                //     ]);
                // }
            }
        }

        session()->flash('success', 'Monthly activities updated successfully.');
        $this->redirect(route('monthly-activities.index'));
    }

    public function updatedSelectedEmployee($value): void
    {
        for ($i = 0; $i < count($this->activities); $i++) {
            $this->activities[$i]['emp_id'] = $value;
        }
    }

    public function render()
    {
        return view('livewire.monthly-activities.edit');
    }
}
