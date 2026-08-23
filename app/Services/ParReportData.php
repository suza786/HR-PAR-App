<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\EmployeeLocation;
use App\Models\HolidayList;
use App\Models\LeaveRequested;
use App\Models\MonthlyActivity;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ParReportData
{
    public function build(int $year, string $month, string $empId): ?array
    {
        $monthlyActivities = MonthlyActivity::query()
            ->with([
                'parCalendar',
                'activityAllocations.project',
                'activityAllocations.activity',
            ])
            ->where('emp_id', $empId)
            ->whereHas('parCalendar', fn ($query) => $query
                ->where('year', $year)
                ->where('month', $month))
            ->get()
            ->filter(fn (MonthlyActivity $activity) => $activity->parCalendar !== null)
            ->sortBy(fn (MonthlyActivity $activity) => $activity->parCalendar->week)
            ->values();

        if ($monthlyActivities->isEmpty()
            || $monthlyActivities->every(fn (MonthlyActivity $activity) => $activity->activityAllocations->isEmpty())) {
            return null;
        }

        $employee = Employee::query()->where('emp_id', $empId)->first();

        $location = $employee?->location_id
            ? EmployeeLocation::query()->find($employee->location_id)
            : null;

        $supervisorCode = $employee?->getRawOriginal('supervisor');
        $supervisor = $supervisorCode
            ? Employee::query()->where('emp_id', $supervisorCode)->first()
            : null;

        $weeks = $this->buildWeeks($monthlyActivities);
        $rows = $this->buildRows($monthlyActivities, $weeks);
        $totalAllocatedHours = array_sum(array_column($rows, 'total_hours'));

        foreach ($rows as &$row) {
            $row['percentage'] = $totalAllocatedHours > 0
                ? ($row['total_hours'] / $totalAllocatedHours) * 100
                : 0;
        }
        unset($row);

        $weeklyTotals = [];
        $weeklyLeaveHours = [];

        foreach ($weeks as $week) {
            $weekNumber = $week['number'];
            $weeklyTotals[$weekNumber] = array_sum(array_map(
                fn (array $row) => (float) ($row['hours'][$weekNumber] ?? 0),
                $rows
            ));
            $weeklyLeaveHours[$weekNumber] = (float) $monthlyActivities
                ->filter(fn (MonthlyActivity $activity) => (int) $activity->parCalendar->week === $weekNumber)
                ->sum('leave_hrs');
        }

        return [
            'year' => $year,
            'month' => $month,
            'employee' => [
                'emp_id' => $empId,
                'name' => $employee?->name ?? $empId,
                'position' => $employee?->position ?? 'N/A',
                'location' => $location?->location_name
                    ?? $location?->name
                    ?? 'N/A',
                'supervisor' => $supervisor?->name
                    ?? $employee?->reporting_supervisor
                    ?? 'N/A',
            ],
            'weeks' => array_values($weeks),
            'rows' => $rows,
            'weekly_totals' => $weeklyTotals,
            'weekly_leave_hours' => $weeklyLeaveHours,
            'total_allocated_hours' => $totalAllocatedHours,
            'total_leave_hours' => array_sum($weeklyLeaveHours),
            'status' => $this->resolveStatus($monthlyActivities),
            'holidays' => $this->loadHolidays($year, $month),
            'leaves' => $this->loadLeaves($year, $month, $empId),
        ];
    }

    private function buildWeeks(Collection $monthlyActivities): array
    {
        $weeks = [];

        foreach ($monthlyActivities as $activity) {
            $calendar = $activity->parCalendar;
            $weekNumber = (int) $calendar->week;

            if (isset($weeks[$weekNumber])) {
                continue;
            }

            $weeks[$weekNumber] = [
                'number' => $weekNumber,
                'label' => 'Week '.$weekNumber,
                'start' => Carbon::parse($calendar->weekStart)->format('d-M-y'),
                'end' => Carbon::parse($calendar->weekEnd)->format('d-M-y'),
            ];
        }

        ksort($weeks);

        return $weeks;
    }

    private function buildRows(Collection $monthlyActivities, array $weeks): array
    {
        $rows = [];
        $weekNumbers = array_keys($weeks);

        foreach ($monthlyActivities as $monthlyActivity) {
            $weekNumber = (int) $monthlyActivity->parCalendar->week;

            foreach ($monthlyActivity->activityAllocations as $allocation) {
                $activityName = $allocation->activity?->name
                    ?? $allocation->activity_description
                    ?? 'N/A';
                $projectName = $allocation->project?->short_name
                    ?? $allocation->project?->project_name
                    ?? 'N/A';
                $key = implode('|', [
                    $allocation->project_id,
                    $allocation->activity_id ?? $activityName,
                    $activityName,
                ]);

                if (! isset($rows[$key])) {
                    $rows[$key] = [
                        'activity' => $activityName,
                        'project' => $projectName,
                        'project_name' => $allocation->project?->project_name ?? $projectName,
                        'hours' => array_fill_keys($weekNumbers, 0.0),
                        'total_hours' => 0.0,
                        'percentage' => 0.0,
                    ];
                }

                $hours = (float) $allocation->working_hrs;
                $rows[$key]['hours'][$weekNumber] = ($rows[$key]['hours'][$weekNumber] ?? 0) + $hours;
                $rows[$key]['total_hours'] += $hours;
            }
        }

        return array_values($rows);
    }

    private function resolveStatus(Collection $monthlyActivities): string
    {
        $statuses = $monthlyActivities
            ->pluck('status')
            ->filter()
            ->map(fn ($status) => strtolower((string) $status))
            ->unique();

        foreach (['rejected', 'pending', 'requested', 'approved'] as $status) {
            if ($statuses->contains($status)) {
                return ucfirst($status);
            }
        }

        return 'N/A';
    }

    private function loadHolidays(int $year, string $month): array
    {
        try {
            [$startDate, $endDate] = $this->monthBounds($year, $month);

            return HolidayList::query()
                ->whereBetween('date_', [$startDate->toDateString(), $endDate->toDateString()])
                ->orderBy('date_')
                ->get()
                ->map(fn (HolidayList $holiday) => [
                    'name' => $holiday->name ?? 'Holiday',
                    'date' => Carbon::parse($holiday->date_)->format('d-M-y'),
                ])
                ->all();
        } catch (\Throwable) {
            return [];
        }
    }

    private function loadLeaves(int $year, string $month, string $empId): array
    {
        try {
            [$startDate, $endDate] = $this->monthBounds($year, $month);

            return LeaveRequested::query()
                ->with('leaveType')
                ->where('emp_id', $empId)
                ->where('status', 'Approved')
                ->whereDate('start_date', '<=', $endDate->toDateString())
                ->whereDate('end_date', '>=', $startDate->toDateString())
                ->orderBy('start_date')
                ->get()
                ->map(fn (LeaveRequested $leave) => [
                    'start' => Carbon::parse($leave->start_date)->format('d-M-y'),
                    'end' => Carbon::parse($leave->end_date)->format('d-M-y'),
                    'type' => $leave->leaveType?->leave_type
                        ?? $leave->leave_type
                        ?? $leave->leave_type_name
                        ?? 'Leave',
                    'days' => $this->leaveDays($leave),
                ])
                ->all();
        } catch (\Throwable) {
            return [];
        }
    }

    private function leaveDays(LeaveRequested $leave): float
    {
        foreach (['num_of_days', 'days', 'no_of_days'] as $field) {
            if ($leave->{$field} !== null) {
                return (float) $leave->{$field};
            }
        }

        return (float) (Carbon::parse($leave->start_date)
            ->diffInDays(Carbon::parse($leave->end_date)) + 1);
    }

    private function monthBounds(int $year, string $month): array
    {
        $startDate = Carbon::createFromFormat('Y-F-d', "{$year}-{$month}-01")->startOfDay();

        return [$startDate, $startDate->copy()->endOfMonth()];
    }
}
