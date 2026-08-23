<?php

namespace App\Livewire;

use App\Models\HolidayList;
use App\Models\ParCalendar;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Collection;
use Livewire\Component;

class ParCalendars extends Component
{
    // ──────────────────────────────────────────────
    // Public state — bound to the Blade view
    // ──────────────────────────────────────────────
    public int    $selectedYear;
    public string $selectedMonth;
    public bool   $generated  = false;
    public bool   $saved      = false;
    public string $message    = '';
    public string $holidayMessage = '';
    public array  $weeks      = [];
    public array  $summary    = [];

    // Weekend days: Friday (5) & Saturday (6)
    private array $weekendDays = [5, 6];

    // ──────────────────────────────────────────────
    // Lifecycle
    // ──────────────────────────────────────────────
    public function mount(): void
    {
        $this->selectedYear  = (int) now()->format('Y');
        $this->selectedMonth = now()->format('F');
    }

    // ──────────────────────────────────────────────
    // Validation rules
    // ──────────────────────────────────────────────
    protected function rules(): array
    {
        return [
            'selectedYear'  => ['required', 'integer', 'min:2000', 'max:2100'],
            'selectedMonth' => ['required', 'string', 'in:January,February,March,April,May,June,July,August,September,October,November,December'],
        ];
    }

    // ──────────────────────────────────────────────
    // Generate — only calculates, does NOT persist
    // ──────────────────────────────────────────────
    public function generate(): void
    {
        $this->validate();

        $monthNumber = $this->monthNameToNumber($this->selectedMonth);
        $officeHolidayDates = $this->fetchOfficeHolidayDates($this->selectedYear, $monthNumber);

        $this->weeks   = $this->buildWeeks($this->selectedYear, $monthNumber, $officeHolidayDates);
        $this->summary = $this->buildSummary($this->weeks);

        $this->generated = true;
        $this->saved     = false;
        $this->message   = '';
    }

    private function monthNameToNumber(string $monthName): int
    {
        return Carbon::parse("{$monthName} 1")->month;
    }

    // ──────────────────────────────────────────────
    // Save — persists (upserts) records to DB
    // ──────────────────────────────────────────────
    public function save(): void
    {
        if (! $this->generated || empty($this->weeks)) {
            $this->message = 'Please generate the calendar first.';
            return;
        }

        foreach ($this->weeks as $week) {
            ParCalendar::updateOrCreate(
                [
                    'year'  => $week['year'],
                    'month' => $this->selectedMonth,
                    'week'  => $week['week'],
                ],
                [
                    'month'        => $this->selectedMonth,
                    'weekStart'    => $week['weekStart'],
                    'weekEnd'      => $week['weekEnd'],
                    'total_days'   => $week['total_days'],
                    'working_days' => $week['working_days'],
                    'office_holidays' => $week['office_holidays'],
                ]
            );
        }

        $this->saved   = true;
        $this->message = "Calendar for {$this->summary['month_name']} {$this->selectedYear} saved successfully.";
    }

    // ──────────────────────────────────────────────
    // Reset form
    // ──────────────────────────────────────────────
    public function resetForm(): void
    {
        $this->weeks     = [];
        $this->summary   = [];
        $this->generated = false;
        $this->saved     = false;
        $this->message   = '';
        $this->holidayMessage = '';
    }

    // ──────────────────────────────────────────────
    // Core calculation logic
    // ──────────────────────────────────────────────

    /**
     * Build the weekly breakdown for a given year & month.
     *
     * Rules:
     *  - Week starts Sunday (0), ends Saturday (6)
     *  - Friday (5) & Saturday (6) are off-days
     *  - If a partial week contains ONLY Friday and/or Saturday, working_days = 0
     */
    private function buildWeeks(int $year, int $month, Collection $officeHolidayDates): array
    {
        $firstDay = Carbon::create($year, $month, 1)->startOfDay();
        $lastDay  = Carbon::create($year, $month, 1)->endOfMonth()->startOfDay();

        // Rewind to the nearest Sunday on or before the 1st
        $weekStart = $firstDay->copy();
        if ($weekStart->dayOfWeek !== Carbon::SUNDAY) {
            $weekStart->previous(Carbon::SUNDAY);
        }

        $weeks      = [];
        $weekNumber = 1;

        while ($weekStart->lte($lastDay)) {
            $weekEnd = $weekStart->copy()->addDays(6); // Saturday

            // Clamp to month boundaries
            $effectiveStart = $weekStart->lt($firstDay) ? $firstDay->copy() : $weekStart->copy();
            $effectiveEnd   = $weekEnd->gt($lastDay)    ? $lastDay->copy()   : $weekEnd->copy();

            [$totalDays, $workingDaysBeforeHolidays] = $this->countDays($effectiveStart, $effectiveEnd);
            $officeHolidays = $this->countOfficeHolidays($effectiveStart, $effectiveEnd, $officeHolidayDates);
            $workingDays = max(0, $workingDaysBeforeHolidays - $officeHolidays);

            $weeks[] = [
                'year'         => $year,
                'month'        => $month,
                'week'         => $weekNumber,
                'weekStart'    => $effectiveStart->format('Y-m-d'),
                'weekEnd'      => $effectiveEnd->format('Y-m-d'),
                'start_label'  => $effectiveStart->format('d M Y') . ' (' . $effectiveStart->format('D') . ')',
                'end_label'    => $effectiveEnd->format('d M Y')   . ' (' . $effectiveEnd->format('D')   . ')',
                'total_days'   => $totalDays,
                'working_days' => $workingDays,
                'office_holidays' => $officeHolidays,
            ];

            $weekStart->addDays(7);
            $weekNumber++;
        }

        return $weeks;
    }

    private function fetchOfficeHolidayDates(int $year, int $month): Collection
    {
        try {
            $this->holidayMessage = '';

            return $this->extractHolidayDates(HolidayList::where('leave_year', $year)->get(), $year, $month);
        } catch (\Throwable $exception) {
            // Fall back to the API below if the direct LMS connection is unavailable.
        }

        $apiPath = route('api.holidays.index', [], false);
        $urls = collect([
            url($apiPath),
            rtrim(config('app.url'), '/') . $apiPath,
        ])->filter()->unique();

        foreach ($urls as $url) {
            try {
                $response = Http::timeout(5)->acceptJson()->get($url, ['year' => $year]);
          
                if ($response->successful()) {
                    $this->holidayMessage = '';
                    $payload = $response->json();
                    $holidays = $payload['data'] ?? $payload;

                    return $this->extractHolidayDates(collect($holidays), $year, $month);
                }
            } catch (\Throwable $exception) {
                continue;
            }
        }

        $this->holidayMessage = 'Office holidays could not be loaded. The calendar was generated without office holidays.';

        return collect();
    }

    private function extractHolidayDates(Collection $holidays, int $year, int $month): Collection
    {
        return $holidays
            ->flatMap(fn ($holiday) => $this->holidayDatesFromItem($this->holidayToArray($holiday)))
            ->filter(fn (Carbon $date) => $date->year === $year && $date->month === $month)
            ->filter(fn (Carbon $date) => ! in_array($date->dayOfWeek, $this->weekendDays, true))
            ->map(fn (Carbon $date) => $date->format('Y-m-d'))
            ->unique()
            ->values();
    }

    private function holidayToArray(mixed $holiday): array
    {
        if ($holiday instanceof HolidayList) {
            return $holiday->getAttributes();
        }

        if ($holiday instanceof \Illuminate\Contracts\Support\Arrayable) {
            return $holiday->toArray();
        }

        return (array) $holiday;
    }

    private function holidayDatesFromItem(array $holiday): array
    {
        $startDate = $this->firstValidDate($holiday, [
            'date_',
            'holiday_date',
            'date',
            'leave_date',
            'start_date',
            'from_date',
            'date_from',
            'startDate',
            'fromDate',
        ]);

        if (! $startDate) {
            return [];
        }

        $endDate = $this->firstValidDate($holiday, [
            'end_date',
            'to_date',
            'date_to',
            'endDate',
            'toDate',
        ]) ?? $startDate->copy();

        if ($endDate->lt($startDate)) {
            $endDate = $startDate->copy();
        }

        $dates = [];
        $current = $startDate->copy();

        while ($current->lte($endDate)) {
            $dates[] = $current->copy();
            $current->addDay();
        }

        return $dates;
    }

    private function firstValidDate(array $holiday, array $keys): ?Carbon
    {
        foreach ($keys as $key) {
            if (empty($holiday[$key])) {
                continue;
            }

            try {
                return Carbon::parse($holiday[$key])->startOfDay();
            } catch (\Throwable $exception) {
                continue;
            }
        }

        return null;
    }

    private function countOfficeHolidays(Carbon $start, Carbon $end, Collection $officeHolidayDates): int
    {
        return $officeHolidayDates
            ->filter(fn (string $date) => Carbon::parse($date)->betweenIncluded($start, $end))
            ->count();
    }

    /**
     * Count total days and working days between two dates (inclusive).
     *
     * @return array{0: int, 1: int}  [totalDays, workingDays]
     */
    private function countDays(Carbon $start, Carbon $end): array
    {
        $totalDays   = 0;
        $workingDays = 0;
        $current     = $start->copy();

        while ($current->lte($end)) {
            $totalDays++;
            if (! in_array($current->dayOfWeek, $this->weekendDays, true)) {
                $workingDays++;
            }
            $current->addDay();
        }

        return [$totalDays, $workingDays];
    }

    /**
     * Build summary statistics from computed weeks.
     */
    private function buildSummary(array $weeks): array
    {
        $totalDays   = array_sum(array_column($weeks, 'total_days'));
        $workingDays = array_sum(array_column($weeks, 'working_days'));
        $officeHolidays = array_sum(array_column($weeks, 'office_holidays'));

        return [
            'month_name'         => $this->selectedMonth,
            'total_weeks'        => count($weeks),
            'total_days'         => $totalDays,
            'total_working_days' => $workingDays,
            'total_office_holidays' => $officeHolidays,
            'total_off_days'     => $totalDays - $workingDays,
        ];
    }

    // ──────────────────────────────────────────────
    // Render
    // ──────────────────────────────────────────────
    public function render()
    {
        return view('livewire.par-calendars');
    }
}
