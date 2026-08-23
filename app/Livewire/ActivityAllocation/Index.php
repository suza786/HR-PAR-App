<?php

namespace App\Livewire\ActivityAllocation;

use App\Models\ActivityAllocation;
use App\Models\Employee;
use App\Models\MonthlyActivity;
use App\Services\EmailSender;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{
    // ──────────────────────────────────────────────
    // Public state — User Selection Section
    // ──────────────────────────────────────────────
    public int $selectedYear;

    public string $selectedMonth;

    public string $selectedEmployee = '';

    public array $employees = [];

    public bool $showActivityAllocations = false;

    public array $activityAllocations = [];

    public array $formattedAllocations = [];

    public array $weeks = [];

    public string $allocationMessage = '';

    public string $submissionMessage = '';

    public string $emailMessage = '';

    public string $activityStatus = '';

    public bool $hasEditableAllocations = false;

    public ?int $editActivityAllocationId = null;

    public function mount(): void
    {
        $this->selectedYear = (int) now()->format('Y');
        $this->selectedMonth = now()->format('F');

        // Load employees if user is HR
        if (Auth::user()->user_type === 'HR') {
            $this->employees = Employee::whereUserStatus('enable')->get()->toArray();
            // $this->employees = Employee::whereSupervisor(Auth::user()->emp_id)->orWhere('emp_id', Auth::user()->emp_id)->get()->toArray();
        } else {
            $this->employees = Employee::whereUserStatus('enable')->whereSupervisor(Auth::user()->emp_id)->orWhere('emp_id', Auth::user()->emp_id)->get()->toArray();
        }
        $this->selectedEmployee = Auth::user()->emp_id;
    }

    public function showActivityAllocationData(): void
    {
        try {
            $this->showActivityAllocations = false;
            $this->activityAllocations = [];
            $this->formattedAllocations = [];
            $this->weeks = [];
            $this->allocationMessage = '';
            $this->submissionMessage = '';
            $this->emailMessage = '';
            $this->activityStatus = '';
            $this->hasEditableAllocations = false;
            $this->editActivityAllocationId = null;

            // Determine which employee to filter by
            $empId = $this->selectedEmployee;

            if (empty($empId)) {
                $this->allocationMessage = 'Please select an employee.';
                return;
            }

            // Fetch allocations for the selected employee, month, and year
            $allocations = ActivityAllocation::with([
                'monthlyActivity.parCalendar',
                'monthlyActivity.employee',
                'project',
                'activity',
            ])
                ->whereHas('monthlyActivity', function ($query) use ($empId) {
                    $query->where('emp_id', $empId)
                        ->whereHas('parCalendar', function ($query) {
                            $query->where('month', $this->selectedMonth)
                                ->where('year', $this->selectedYear);
                        });
                })
                ->get();

            if ($allocations->isEmpty()) {
                $this->allocationMessage = "Activity Allocation have not updated for the employee on {$this->selectedMonth}-{$this->selectedYear}";
                $this->showActivityAllocations = true;

                return;
            }

            $statuses = $allocations->pluck('monthlyActivity.status')->unique()->values();
            $this->activityStatus = $statuses->count() === 1 ? (string) $statuses->first() : 'mixed';
            $this->hasEditableAllocations = $allocations->every(
                fn ($allocation): bool => $allocation->monthlyActivity?->status === 'pending'
            );
            $this->editActivityAllocationId = $this->hasEditableAllocations
                ? (int) $allocations->first()->id
                : null;

            // Get unique weeks from the allocations
            $weeksCollection = $allocations->pluck('monthlyActivity.parCalendar.week')
                ->unique()
                ->sort()
                ->values();

            foreach ($weeksCollection as $week) {
                $this->weeks[] = [
                    'week' => $week,
                    'label' => 'W-'.$week,
                ];
            }

            // Group allocations by project and activity, with hours by week
            $groupedAllocations = [];

            foreach ($allocations as $allocation) {
                $projectName = $allocation->project->project_name ?? 'N/A';
                $activityName = $allocation->activity->name ?? $allocation->activity_description ?? 'N/A';
                $monthlyActivityStatus = $allocation->monthlyActivity->status ?? 'N/A';
                $key = $projectName.'|'.$activityName.'|'.$monthlyActivityStatus;
                $week = $allocation->monthlyActivity->parCalendar->week;

                if (! isset($groupedAllocations[$key])) {
                    $groupedAllocations[$key] = [
                        'project' => $projectName,
                        'activity' => $activityName,
                        'status' => $monthlyActivityStatus,
                        'hours_by_week' => [],
                    ];
                }

                $groupedAllocations[$key]['hours_by_week'][$week] = $allocation->working_hrs;
            }

            // Format allocations for display
            foreach ($groupedAllocations as $allocation) {
                $row = [
                    'project' => $allocation['project'],
                    'activity' => $allocation['activity'],
                    'status' => $allocation['status'],
                    'hours' => [],
                    'total' => 0,
                ];

                foreach ($this->weeks as $week) {
                    $hours = $allocation['hours_by_week'][$week['week']] ?? 0;
                    $row['hours'][] = $hours;
                    $row['total'] += $hours;
                }

                $this->formattedAllocations[] = $row;
            }

            $this->activityAllocations = $allocations->toArray();
            $this->showActivityAllocations = true;
        } catch (\Exception $e) {
            $this->allocationMessage = 'Error: '.$e->getMessage();
            $this->showActivityAllocations = true;
        }
    }

    public function submitActivityAllocationData(EmailSender $emailSender): void
    {
        try {
            $authUser = Auth::user();
            $empId = ! empty($this->selectedEmployee)
                ? $this->selectedEmployee
                : ($authUser?->emp_id ?? '');

            if (empty($empId)) {
                $this->allocationMessage = 'Please select an employee.';

                return;
            }

            $monthlyActivityIds = $this->monthlyActivityIds($empId, 'pending');
            $updatedCount = MonthlyActivity::query()
                ->whereIn('id', $monthlyActivityIds)
                ->where('status', 'pending')
                ->update(['status' => 'requested']);

            if ($updatedCount === 0) {
                $this->allocationMessage = 'No pending activity allocation found for the selected employee.';

                return;
            }

            $this->activityStatus = 'requested';
            $this->submissionMessage = 'Activity allocation submitted successfully.';

            if ($emailSender->enabled() && ! $this->sendRequestedEmail($emailSender, $empId)) {
                $this->emailMessage = 'The status was updated, but the supervisor email could not be sent.';
            }
        } catch (\Exception $e) {
            $this->allocationMessage = 'Error: '.$e->getMessage();
        }
    }

    public function approveActivityAllocationData(EmailSender $emailSender): void
    {
        try {
            $authUser = Auth::user();
            $empId = ! empty($this->selectedEmployee)
                ? $this->selectedEmployee
                : ($authUser?->emp_id ?? '');

            if (empty($empId)) {
                $this->allocationMessage = 'Please select an employee.';

                return;
            }

            $monthlyActivityIds = $this->monthlyActivityIds($empId, 'requested');
            $updatedCount = MonthlyActivity::query()
                ->whereIn('id', $monthlyActivityIds)
                ->where('status', 'requested')
                ->update(['status' => 'approved']);

            if ($updatedCount === 0) {
                $this->allocationMessage = 'No requested activity allocation found for the selected employee.';

                return;
            }

            $this->activityStatus = 'approved';
            $this->submissionMessage = 'Activity allocation approved successfully.';

            if ($emailSender->enabled() && ! $this->sendApprovedEmail($emailSender, $empId)) {
                $this->emailMessage = 'The status was updated, but the employee email could not be sent.';
            }
        } catch (\Exception $e) {
            $this->allocationMessage = 'Error: '.$e->getMessage();
        }
    }

    private function monthlyActivityIds(string $empId, string $status)
    {
        return MonthlyActivity::query()
            ->where('emp_id', $empId)
            ->where('status', $status)
            ->whereHas('parCalendar', function ($query): void {
                $query->where('year', $this->selectedYear)
                    ->where('month', $this->selectedMonth);
            })
            ->pluck('id');
    }

    private function sendRequestedEmail(EmailSender $emailSender, string $empId): bool
    {
        $employee = Employee::query()->where('emp_id', $empId)->first();
        $supervisorCode = $employee?->getRawOriginal('supervisor');
        $supervisor = $supervisorCode
            ? Employee::query()->where('emp_id', $supervisorCode)->first()
            : null;

        if (! $supervisor && $employee?->supervisor_id) {
            $supervisor = Employee::query()->find($employee->supervisor_id);
        }

        if (! $employee || ! $supervisor?->email) {
            return false;
        }

        return $emailSender->send(
            subject: "Activity Allocations Submitted - {$employee->name}",
            to: $supervisor->email,
            cc: $employee->email,
            body: "Activity Allocations for {$employee->name} ({$employee->emp_id}) for {$this->selectedMonth} {$this->selectedYear} have been submitted and are awaiting approval.",
        );
    }

    private function sendApprovedEmail(EmailSender $emailSender, string $empId): bool
    {
        $employee = Employee::query()->where('emp_id', $empId)->first();

        if (! $employee?->email) {
            return false;
        }

        return $emailSender->send(
            subject: "Activity Allocations Approved - {$this->selectedMonth} {$this->selectedYear}",
            to: $employee->email,
            cc: null,
            body: "Your Activity Allocations for {$this->selectedMonth} {$this->selectedYear} have been approved.",
        );
    }

    public function render()
    {
        return view('livewire.activity-allocations.index');
    }
}
