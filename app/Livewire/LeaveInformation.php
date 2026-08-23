<?php

namespace App\Livewire;

use App\Models\LeaveRequested;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LeaveInformation extends Component
{
    public int $selectedYear;
    public string $selectedMonth;
    public bool $loaded = false;
    public array $leaves = [];
    public string $message = '';

    public function mount(): void
    {
        $this->selectedYear = (int) now()->format('Y');
        $this->selectedMonth = now()->format('F');
    }

    protected function rules(): array
    {
        return [
            'selectedYear' => ['required', 'integer', 'min:2025', 'max:2100'],
            'selectedMonth' => ['required', 'string', 'in:January,February,March,April,May,June,July,August,September,October,November,December'],
        ];
    }

    public function showData(): void
    {
        $this->validate();

        $this->loaded = false;
        $this->leaves = [];
        $this->message = '';

        $empId = Auth::user()->emp_id ?? null;
        
        if (empty($empId)) {
            $this->loaded = true;
            $this->message = $this->emptyMessage();
            return;
        }

        $monthNumber = Carbon::parse("{$this->selectedMonth} 1")->month;
        $monthStart = Carbon::create($this->selectedYear, $monthNumber, 1)->startOfDay();
        $monthEnd = $monthStart->copy()->endOfMonth();

        // dd($monthStart->toDateString());
        $leaveRequests = LeaveRequested::query()
            ->with('leaveType')
            ->where('emp_id', $empId)
            ->whereDate('end_date', '>=', $monthStart->toDateString())
            ->whereDate('start_date', '<=', $monthEnd->toDateString())
            ->orderBy('start_date')
            ->get();

        $this->leaves = $leaveRequests
            ->map(fn ($leaveRequest) => [
                'start_date' => Carbon::parse($leaveRequest->start_date)->format('d-M-Y'),
                'end_date' => Carbon::parse($leaveRequest->end_date)->format('d-M-Y'),
                'leave_type' => optional($leaveRequest->leaveType)->leave_type ?: 'Leave',
                'days' => $this->formatDays((float) $leaveRequest->num_of_days),
            ])
            ->all();

        $this->loaded = true;

        if (empty($this->leaves)) {
            $this->message = $this->emptyMessage();
        }
    }

    private function formatDays(float $days): int|float
    {
        return (floor($days) === $days) ? (int) $days : $days;
    }

    private function emptyMessage(): string
    {
        return "You don't have any leave entry for the month {$this->selectedMonth}-{$this->selectedYear}....";
    }

    public function render()
    {
        return view('livewire.leave-information');
    }
}
