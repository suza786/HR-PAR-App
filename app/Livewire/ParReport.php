<?php

namespace App\Livewire;

use App\Models\Employee;
use App\Models\MonthlyActivity;
use App\Services\EmailSender;
use App\Services\ParReportData;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ParReport extends Component
{
    public int $selectedYear;
    public string $selectedMonth;
    public string $selectedEmployee = '';
    public array $employees = [];
    public array $report = [];
    public bool $reportRequested = false;
    public string $reportMessage = '';

    public int $approvalYear;
    public ?string $approvalEmployee = null;
    public array $approvalEmployees = [];
    public array $approvalRows = [];
    public bool $approvalSearchDone = false;
    public string $approvalMessage = '';
    public string $approvalEmailMessage = '';
    public bool $showApprovalModal = false;
    public array $approvalModal = [];

    public function mount(): void
    {
        $this->selectedYear = (int) now()->format('Y');
        $this->selectedMonth = now()->format('F');
        $this->employees = $this->availableEmployees();
        $this->selectedEmployee = (string) (Auth::user()?->emp_id ?? ($this->employees[0]['emp_id'] ?? ''));
        $this->approvalYear = (int) now()->format('Y');
        $this->approvalEmployees = $this->availableSubordinates();
    }

    protected function rules(): array
    {
        return [
            'selectedYear' => ['required', 'integer', 'min:2000', 'max:2100'],
            'selectedMonth' => [
                'required',
                Rule::in([
                    'January', 'February', 'March', 'April', 'May', 'June',
                    'July', 'August', 'September', 'October', 'November', 'December',
                ]),
            ],
            'selectedEmployee' => [
                'required',
                Rule::in(array_column($this->employees, 'emp_id')),
            ],
        ];
    }

    public function show(ParReportData $reportData): void
    {
        $validated = $this->validate();
        $this->reportRequested = true;
        $this->reportMessage = '';

        $report = $reportData->build(
            (int) $validated['selectedYear'],
            $validated['selectedMonth'],
            $validated['selectedEmployee'],
        );

        if ($report === null) {
            $this->report = [];
            $this->reportMessage = 'Report data are not available.';

            return;
        }

        $this->report = $report;
    }

    public function exportPdf(ParReportData $reportData)
    {
        $validated = $this->validate();
        $report = $reportData->build(
            (int) $validated['selectedYear'],
            $validated['selectedMonth'],
            $validated['selectedEmployee'],
        );

        if ($report === null) {
            $this->reportRequested = true;
            $this->report = [];
            $this->reportMessage = 'Report data are not available.';

            return null;
        }

        $fileEmployee = preg_replace('/[^A-Za-z0-9_-]/', '-', $validated['selectedEmployee']);
        $fileName = "PAR-Report-{$fileEmployee}-{$validated['selectedMonth']}-{$validated['selectedYear']}.pdf";

        return response()->streamDownload(
            fn () => print Pdf::loadView('reports.par-report-pdf', ['report' => $report])
                ->setPaper('a4', 'landscape')
                ->output(),
            $fileName,
            ['Content-Type' => 'application/pdf'],
        );
    }

    public function showApprovalPars(): void
    {
        $validated = $this->validate([
            'approvalYear' => ['required', 'integer', 'min:2000', 'max:2100'],
            'approvalEmployee' => [
                'nullable',
                'string',
                Rule::in(array_column($this->approvalEmployees, 'emp_id')),
            ],
        ]);

        $this->approvalSearchDone = true;
        $this->approvalMessage = '';
        $this->approvalEmailMessage = '';
        $this->approvalRows = [];
        $this->showApprovalModal = false;
        $this->approvalModal = [];

        $subordinateIds = array_column($this->approvalEmployees, 'emp_id');
        $monthlyActivities = MonthlyActivity::query()
            ->with(['parCalendar', 'employee'])
            ->whereIn('emp_id', $subordinateIds)
            ->whereIn('status', ['requested', 'approved'])
            ->whereHas('parCalendar', fn ($query) => $query->where('year', $validated['approvalYear']))
            ->when(
                filled($validated['approvalEmployee']),
                fn ($query) => $query->where('emp_id', $validated['approvalEmployee']),
            )
            ->get()
            ->filter(fn (MonthlyActivity $activity) => $activity->parCalendar !== null);

        $groups = $monthlyActivities->groupBy(
            fn (MonthlyActivity $activity) => $activity->emp_id.'|'.$activity->parCalendar->month
        );

        foreach ($groups as $activities) {
            /** @var MonthlyActivity $first */
            $first = $activities->first();
            $rawStatuses = $activities->pluck('status')->unique()->sort()->values();
            $statuses = $rawStatuses
                ->map(fn (string $status) => ucfirst($status))
                ->values();
            $this->approvalRows[] = [
                'emp_id' => (string) $first->emp_id,
                'employee' => $first->employee?->name ?? (string) $first->emp_id,
                'month' => (string) $first->parCalendar->month,
                'year' => (int) $validated['approvalYear'],
                'status' => $statuses->implode('/'),
                'has_requested' => $rawStatuses->contains('requested'),
            ];
        }

        usort($this->approvalRows, function (array $left, array $right): int {
            $employeeOrder = strcasecmp($left['employee'], $right['employee']);

            return $employeeOrder !== 0
                ? $employeeOrder
                : (array_search($left['month'], $this->monthNames(), true)
                    <=> array_search($right['month'], $this->monthNames(), true));
        });
    }

    public function approvePar(string $empId, string $month, EmailSender $emailSender): void
    {
        $this->validate([
            'approvalYear' => ['required', 'integer', 'min:2000', 'max:2100'],
        ]);
        validator(['month' => $month], [
            'month' => ['required', Rule::in($this->monthNames())],
        ])->validate();

        abort_unless($this->isSubordinate($empId), 403);

        $updated = DB::transaction(fn () => MonthlyActivity::query()
            ->where('emp_id', $empId)
            ->where('status', 'requested')
            ->whereHas('parCalendar', fn ($query) => $query
                ->where('year', $this->approvalYear)
                ->where('month', $month))
            ->update(['status' => 'approved']));

        $message = $updated > 0
            ? 'Activity allocation approved successfully.'
            : 'No requested activity allocation was found for this employee and period.';

        $this->showApprovalPars();
        $this->approvalMessage = $message;
        $this->approvalEmailMessage = '';

        if ($updated > 0) {
            if (! $emailSender->enabled()) {
                $this->approvalEmailMessage = 'The PAR was approved, but no email was sent because email notifications are disabled.';
            } elseif (! $this->sendApprovalEmail($emailSender, $empId, $month)) {
                $this->approvalEmailMessage = 'The PAR was approved, but the employee email could not be sent. Check the employee email address and email settings.';
            }
        }
    }

    public function viewPar(string $empId, string $month): void
    {
        $this->validate([
            'approvalYear' => ['required', 'integer', 'min:2000', 'max:2100'],
        ]);
        validator(['month' => $month], [
            'month' => ['required', Rule::in($this->monthNames())],
        ])->validate();
        abort_unless($this->isSubordinate($empId), 403);

        $monthlyActivities = MonthlyActivity::query()
            ->with(['parCalendar', 'activityAllocations.project', 'activityAllocations.activity'])
            ->where('emp_id', $empId)
            ->whereIn('status', ['requested', 'approved'])
            ->whereHas('parCalendar', fn ($query) => $query
                ->where('year', $this->approvalYear)
                ->where('month', $month))
            ->get()
            ->filter(fn (MonthlyActivity $activity) => $activity->parCalendar !== null)
            ->sortBy(fn (MonthlyActivity $activity) => $activity->parCalendar->week)
            ->values();

        $weeks = [];
        $rows = [];
        foreach ($monthlyActivities as $monthlyActivity) {
            $weekNumber = (int) $monthlyActivity->parCalendar->week;
            $weeks[$weekNumber] = ['week' => $weekNumber, 'label' => 'W-'.$weekNumber];

            foreach ($monthlyActivity->activityAllocations as $allocation) {
                $activityName = $allocation->activity?->name
                    ?? $allocation->activity_description
                    ?? 'N/A';
                $projectName = $allocation->project?->project_name ?? 'N/A';
                $key = implode('|', [$allocation->project_id, $allocation->activity_id ?? $activityName]);

                if (! isset($rows[$key])) {
                    $rows[$key] = [
                        'project' => $projectName,
                        'activity' => $activityName,
                        'hours_by_week' => [],
                    ];
                }

                $rows[$key]['hours_by_week'][$weekNumber] =
                    ($rows[$key]['hours_by_week'][$weekNumber] ?? 0) + (float) $allocation->working_hrs;
            }
        }

        ksort($weeks);
        $weeks = array_values($weeks);
        $formattedRows = [];
        foreach ($rows as $row) {
            $hours = [];
            foreach ($weeks as $week) {
                $hours[] = $row['hours_by_week'][$week['week']] ?? 0;
            }
            $formattedRows[] = [
                'project' => $row['project'],
                'activity' => $row['activity'],
                'hours' => $hours,
                'total' => array_sum($hours),
            ];
        }

        $employee = collect($this->approvalEmployees)->firstWhere('emp_id', $empId);
        $this->approvalModal = [
            'employee' => $employee['name'] ?? $empId,
            'emp_id' => $empId,
            'month' => $month,
            'year' => $this->approvalYear,
            'weeks' => $weeks,
            'rows' => $formattedRows,
        ];
        $this->showApprovalModal = true;
    }

    private function availableSubordinates(): array
    {
        $user = Auth::user();

        if (! $user?->emp_id) {
            return [];
        }

        return Employee::query()
            ->where('supervisor', $user->emp_id)
            ->where('emp_id', '!=', $user->emp_id)
            ->orderBy('emp_id')
            ->get(['emp_id', 'name'])
            ->map(fn (Employee $employee) => [
                'emp_id' => (string) $employee->emp_id,
                'name' => (string) $employee->name,
            ])
            ->all();
    }

    private function isSubordinate(string $empId): bool
    {
        $user = Auth::user();

        return (bool) $user?->emp_id
            && $empId !== (string) $user->emp_id
            && Employee::query()
                ->where('emp_id', $empId)
                ->where('supervisor', $user->emp_id)
                ->exists();
    }

    private function sendApprovalEmail(EmailSender $emailSender, string $empId, string $month): bool
    {
        $employee = Employee::query()->where('emp_id', $empId)->first();

        if (! $employee?->email) {
            return false;
        }

        return $emailSender->send(
            subject: "Activity Allocations Approved - {$month} {$this->approvalYear}",
            to: $employee->email,
            body: "Your Personal Activity Report for {$month} {$this->approvalYear} has been approved.\n\n"
                ."Please upload your approved PAR using the link provided below:\n"
                .'https://drive.google.com/drive/folders/19A1BA9pG2MJZCJrj9Rr__D5mdIBzBXHg',
            attachments: [Attachment::fromData(function () use ($empId, $month): string {
                $report = app(ParReportData::class)->build($this->approvalYear, $month, $empId);

                if ($report === null) {
                    throw new \RuntimeException('Approved PAR report data are not available.');
                }

                return Pdf::loadView('reports.par-report-pdf', ['report' => $report])
                    ->setPaper('a4', 'landscape')
                    ->output();
            }, 'PAR-Report-'.preg_replace('/[^A-Za-z0-9_-]/', '-', $empId)."-{$month}-{$this->approvalYear}.pdf")
                ->withMime('application/pdf')],
        );
    }

    private function monthNames(): array
    {
        return [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December',
        ];
    }

    private function availableEmployees(): array
    {
        $user = Auth::user();
        $query = Employee::query()
            ->where('user_status', 'enable')
            ->orderBy('emp_id');

        if ($user?->user_type !== 'HR') {
            $query->where(fn ($query) => $query
                ->where('supervisor', $user?->emp_id)
                ->orWhere('emp_id', $user?->emp_id));
        }

        return $query
            ->get(['emp_id', 'name'])
            ->map(fn (Employee $employee) => [
                'emp_id' => (string) $employee->emp_id,
                'name' => (string) $employee->name,
            ])
            ->all();
    }

    public function render()
    {
        return view('livewire.par-report');
    }
}
