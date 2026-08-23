<?php

namespace App\Livewire\ActivityAllocation;

use App\Models\Activity;
use App\Models\ActivityAllocation;
use App\Models\MonthlyActivity;
use App\Models\SalaryAllocation;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Edit extends Component
{
    #[Locked]
    public int $activityAllocationId;

    #[Locked]
    public array $allocationIdsByRow = [];

    public int $selectedYear;

    public string $selectedMonth;

    public string $selectedEmployeeCode;

    public string $selectedEmployeeName = '';

    public $salaryAllocations = [];

    public $activities = [];

    public array $weeks = [];

    public array $activityAllocations = [];

    public array $weeklyTotalsExpected = [];

    public array $weeklyTotals = [];

    public ?string $weeklyHoursError = null;

    public array $months = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December',
    ];

    public function mount($id): void
    {
        abort_unless(Auth::user()?->user_type === 'HR', 403);

        $this->activityAllocationId = (int) $id;
        $anchor = ActivityAllocation::with([
            'monthlyActivity.parCalendar',
            'monthlyActivity.employee',
        ])->findOrFail($this->activityAllocationId);

        $this->authorizeEditing($anchor);

        $monthlyActivity = $anchor->monthlyActivity;
        $this->selectedEmployeeCode = (string) $monthlyActivity->emp_id;
        $this->selectedEmployeeName = (string) ($monthlyActivity->employee?->name ?? '');
        $this->selectedYear = (int) $monthlyActivity->parCalendar->year;
        $this->selectedMonth = (string) $monthlyActivity->parCalendar->month;

        $monthlyActivities = $this->monthActivitiesQuery()->get()
            ->sortBy(fn ($activity) => $activity->parCalendar->week)
            ->values();

        $this->authorizeMonthEditing($monthlyActivities);
        $this->activities = Activity::query()->orderBy('name')->get();
        $this->loadSalaryAllocations();
        $this->loadActivityAllocations($monthlyActivities);
    }

    public function getSelectedMonthNumberProperty(): int
    {
        return array_search($this->selectedMonth, $this->months, true) + 1;
    }

    public function updatedActivityAllocations(): void
    {
        $this->weeklyHoursError = null;
        $this->computeWeeklyTotals();
    }

    public function save()
    {
        $anchor = ActivityAllocation::with('monthlyActivity.parCalendar')->findOrFail($this->activityAllocationId);
        $this->authorizeEditing($anchor);

        $monthlyActivities = $this->monthActivitiesQuery()->get();
        $this->authorizeMonthEditing($monthlyActivities);

        $rules = ['activityAllocations' => ['required', 'array', 'min:1']];

        foreach ($this->activityAllocations as $rowKey => $row) {
            $rules["activityAllocations.{$rowKey}.project_id"] = ['required', 'exists:projects,id'];
            $rules["activityAllocations.{$rowKey}.activity_id"] = ['required', 'exists:activities,id'];

            foreach ($this->weeks as $week) {
                $rules["activityAllocations.{$rowKey}.weekly_hrs.{$week['id']}"] = ['required', 'numeric', 'min:0'];
            }
        }

        $this->validate($rules);

        if (! $this->weeklyTotalsMatch()) {
            return;
        }

        $allowedIds = ActivityAllocation::query()
            ->whereIn('monthly_activity_id', $monthlyActivities->pluck('id'))
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->sort()
            ->values();

        $mappedIds = collect($this->allocationIdsByRow)
            ->flatten()
            ->map(fn ($id): int => (int) $id)
            ->sort()
            ->values();

        abort_unless($mappedIds->all() === $allowedIds->all(), 403);

        DB::transaction(function () use ($allowedIds): void {
            foreach ($this->activityAllocations as $rowKey => $row) {
                foreach ($this->allocationIdsByRow[$rowKey] as $monthlyActivityId => $allocationId) {
                    ActivityAllocation::query()
                        ->whereKey($allocationId)
                        ->whereIn('id', $allowedIds)
                        ->update([
                            'project_id' => (int) $row['project_id'],
                            'activity_id' => (int) $row['activity_id'],
                            'working_hrs' => round((float) $row['weekly_hrs'][$monthlyActivityId], 2),
                        ]);
                }
            }
        });

        session()->flash('success', 'Monthly activity allocations updated successfully.');

        return redirect()->route('activity-allocations.index');
    }

    protected function authorizeEditing(ActivityAllocation $allocation): void
    {
        abort_unless(Auth::user()?->user_type === 'HR', 403);
        abort_unless($allocation->monthlyActivity?->status === 'pending', 403);
    }

    protected function authorizeMonthEditing(Collection $monthlyActivities): void
    {
        abort_unless($monthlyActivities->isNotEmpty(), 404);
        abort_unless(
            $monthlyActivities->every(fn ($activity): bool => $activity->status === 'pending'),
            403
        );
    }

    protected function weeklyTotalsMatch(): bool
    {
        $this->computeWeeklyTotals();
        $this->weeklyHoursError = null;

        foreach ($this->weeks as $week) {
            $monthlyActivityId = $week['id'];
            $actual = (float) ($this->weeklyTotals[$monthlyActivityId] ?? 0);
            $expected = (float) $this->weeklyTotalsExpected[$monthlyActivityId];

            if (abs($actual - $expected) > 0.001) {
                $this->weeklyHoursError = "Total allocated hours for Week {$week['week']} must equal ".number_format($expected, 2).'.';

                return false;
            }
        }

        return true;
    }

    private function monthActivitiesQuery()
    {
        return MonthlyActivity::query()
            ->with('parCalendar')
            ->where('emp_id', $this->selectedEmployeeCode)
            ->whereHas('parCalendar', function ($query): void {
                $query->where('year', $this->selectedYear)
                    ->where('month', $this->selectedMonth);
            });
    }

    private function loadSalaryAllocations(): void
    {
        $monthStart = Carbon::createFromFormat(
            '!Y-m-d',
            sprintf('%s-%02d-01', $this->selectedYear, $this->selectedMonthNumber)
        )->startOfMonth();

        $this->salaryAllocations = SalaryAllocation::with('project')
            ->where('emp_id', $this->selectedEmployeeCode)
            ->where('curr_year', $this->selectedYear)
            ->whereDate('start_month', '<=', $monthStart->copy()->endOfMonth())
            ->whereDate('end_month', '>=', $monthStart)
            ->get();
    }

    private function loadActivityAllocations(Collection $monthlyActivities): void
    {
        $this->weeks = [];
        $this->weeklyTotalsExpected = [];

        foreach ($monthlyActivities as $monthlyActivity) {
            $this->weeks[] = [
                'id' => (int) $monthlyActivity->id,
                'week' => $monthlyActivity->parCalendar->week,
            ];
            $this->weeklyTotalsExpected[$monthlyActivity->id] = (float) $monthlyActivity->working_hrs;
        }

        $allocations = ActivityAllocation::query()
            ->whereIn('monthly_activity_id', $monthlyActivities->pluck('id'))
            ->orderBy('monthly_activity_id')
            ->orderBy('id')
            ->get();

        $rows = [];
        $idsByRow = [];
        $occurrences = [];

        foreach ($allocations as $allocation) {
            $weekId = (int) $allocation->monthly_activity_id;
            $baseKey = $allocation->project_id.'|'.$allocation->activity_id;
            $occurrences[$weekId][$baseKey] = ($occurrences[$weekId][$baseKey] ?? 0) + 1;
            $rowKey = 'row_'.md5($baseKey.'|'.$occurrences[$weekId][$baseKey]);

            if (! isset($rows[$rowKey])) {
                $rows[$rowKey] = [
                    'project_id' => (string) $allocation->project_id,
                    'activity_id' => (string) $allocation->activity_id,
                    'weekly_hrs' => array_fill_keys(
                        $monthlyActivities->pluck('id')->map(fn ($id): int => (int) $id)->all(),
                        '0.00'
                    ),
                ];
            }

            $rows[$rowKey]['weekly_hrs'][$weekId] = number_format((float) $allocation->working_hrs, 2, '.', '');
            $idsByRow[$rowKey][$weekId] = (int) $allocation->id;
        }

        $this->activityAllocations = $rows;
        $this->allocationIdsByRow = $idsByRow;
        $this->computeWeeklyTotals();
    }

    private function computeWeeklyTotals(): void
    {
        $this->weeklyTotals = [];

        foreach ($this->weeklyTotalsExpected as $monthlyActivityId => $expected) {
            $this->weeklyTotals[$monthlyActivityId] = number_format(
                collect($this->activityAllocations)->sum(
                    fn ($row): float => (float) ($row['weekly_hrs'][$monthlyActivityId] ?? 0)
                ),
                2,
                '.',
                ''
            );
        }
    }

    public function render()
    {
        return view('livewire.activity-allocations.edit');
    }
}
