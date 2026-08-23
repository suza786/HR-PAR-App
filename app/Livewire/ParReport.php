<?php

namespace App\Livewire;

use App\Models\Employee;
use App\Services\ParReportData;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
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
    
    public function mount(): void
    {
        $this->selectedYear = (int) now()->format('Y');
        $this->selectedMonth = now()->format('F');
        $this->employees = $this->availableEmployees();
        $this->selectedEmployee = (string) (Auth::user()?->emp_id ?? ($this->employees[0]['emp_id'] ?? ''));
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
