<div>
    <div class="mx-auto max-w-7xl px-4 py-8">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-100">{{ __('PAR Report') }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ __('View and export an employee Personnel Activity Report by month.') }}
            </p>
        </div>

        <section class="mb-6 rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-zinc-800">
            <h2 class="mb-4 text-base font-semibold text-gray-700 dark:text-gray-200">{{ __('Report selection') }}</h2>
            <form wire:submit="show" class="grid grid-cols-1 items-start gap-4 md:grid-cols-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div>
                        <label for="par-report-year" class="mb-2 block text-xs font-medium uppercase tracking-wide">
                            {{ __('Year') }}
                        </label>
                        <select id="par-report-year" wire:model="selectedYear" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-blue-500 focus:ring-blue-500 dark:border-neutral-600 dark:bg-zinc-900 dark:text-gray-100">
                            @for ($year = now()->year - 6; $year <= now()->year + 1; $year++)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endfor
                        </select>
                        @error('selectedYear') <span class="mt-1 block text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="par-report-month" class="mb-1 block text-xs font-medium uppercase tracking-wide text-gray-800">
                            {{ __('Month') }}
                        </label>
                        <select id="par-report-month" wire:model="selectedMonth" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-blue-500 focus:ring-blue-500 dark:border-neutral-600 dark:bg-zinc-900 dark:text-gray-100">
                            @foreach (['January','February','March','April','May','June','July','August','September','October','November','December'] as $month)
                                <option value="{{ $month }}">{{ $month }}</option>
                            @endforeach
                        </select>
                        @error('selectedMonth') <span class="mt-1 block text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="par-report-employee" class="mb-1 block text-xs font-medium uppercase tracking-wide text-gray-800">
                            {{ __('Employee') }}
                        </label>
                        <select id="par-report-employee" wire:model="selectedEmployee" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-blue-500 focus:ring-blue-500 dark:border-neutral-600 dark:bg-zinc-900 dark:text-gray-100">
                            <option value="">{{ __('Select employee') }}</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee['emp_id'] }}">
                                    {{ $employee['emp_id'] }} - {{ $employee['name'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('selectedEmployee') <span class="mt-1 block text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-6">
                        <button type="submit" wire:loading.attr="disabled" wire:target="show" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:opacity-60">
                            <svg wire:loading wire:target="show" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                            </svg>
                            {{ __('Show') }}
                        </button>
                    </div>
                </div>
            </form>            
        </section>

        @if ($reportRequested && empty($report))
            <div class="rounded-xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm font-medium text-amber-800" role="alert">
                {{ $reportMessage }}
            </div>
        @endif

        @if (! empty($report))
            <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-zinc-800">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200 px-5 py-4 dark:border-neutral-700">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ __('Personnel Activity Report (PAR)') }}</h2>
                        <p class="text-sm text-gray-500">{{ $report['month'] }} {{ $report['year'] }}</p>
                    </div>
                    <button type="button" wire:click="exportPdf" wire:loading.attr="disabled" wire:target="exportPdf" class="inline-flex items-center gap-2 rounded-lg bg-green-700 px-4 py-2 text-sm font-semibold text-dark transition hover:bg-rose-700 disabled:opacity-60">
                        <flux:icon.arrow-down-tray class="size-4" />
                        <span wire:loading.remove wire:target="exportPdf">{{ __('Export PDF') }}</span>
                        <span wire:loading wire:target="exportPdf">{{ __('Preparing PDF...') }}</span>
                    </button>
                </div>               
                <div class="grid grid-cols-1 gap-x-8 gap-y-2 border-b border-gray-200 px-5 py-4 text-sm md:grid-cols-2 dark:border-neutral-700">
                    <table class="w-full text-sm text-gray-700 dark:text-gray-200 mb-10">
                    <tr>
                        <th class="px-5 py-1 text-left font-medium">{{ __('Employee Name:') }}</th>
                        <td class="px-5 py-1 text-gray-600">{{ $report['employee']['name'] }}</td>
                        <th class="px-5 py-1 text-left font-medium">{{ __('Employee ID:') }}</th>
                        <td class="px-5 py-1 text-gray-600">{{ $report['employee']['emp_id'] }}</td>
                    </tr>
                    <tr>
                        <th class="px-5 py-1 text-left font-medium">{{ __('Job Title:') }}</th>
                        <td class="px-5 py-1 text-gray-600">{{ $report['employee']['position'] }}</td>
                        <th class="px-5 py-1 text-left font-medium">{{ __('Location:') }}</th>
                        <td class="px-5 py-1 text-gray-600">{{ $report['employee']['location'] }}</td>
                    </tr>
                    <tr>
                        <th class="px-5 py-1 text-left font-medium">{{ __('Month:') }}</th>
                        <td class="px-5 py-1 text-gray-600">{{ $report['month'] }}</td>
                        <th class="px-5 py-1 text-left font-medium">{{ __('Year:') }}</th>
                        <td class="px-5 py-1 text-gray-600">{{ $report['year'] }}</td>
                    </tr>
                    </table>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1000px] border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50 text-gray-600 dark:bg-zinc-900 dark:text-gray-300">
                                <th rowspan="2" class="border border-gray-300 px-3 py-2 text-left">{{ __('Activity Name') }}</th>
                                <th rowspan="2" class="border border-gray-300 px-3 py-2 text-left">{{ __('Project') }}</th>
                                @foreach ($report['weeks'] as $week)
                                    <th class="border border-gray-300 px-2 py-2 text-center font-medium">
                                        {{ $week['start'] }} - {{ $week['end'] }}
                                    </th>
                                @endforeach
                                <th rowspan="2" class="border border-gray-300 px-2 py-2 text-center">{{ __('Total Hours') }}</th>
                                <th rowspan="2" class="border border-gray-300 px-2 py-2 text-center">{{ __('Percentage Distribution') }}</th>
                            </tr>
                            <tr class="bg-slate-50 text-gray-600 dark:bg-zinc-900 dark:text-gray-300">
                                @foreach ($report['weeks'] as $week)
                                    <th class="border border-gray-300 px-2 py-2 text-center">{{ $week['label'] }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 dark:text-gray-200">
                            @foreach ($report['rows'] as $row)
                                <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                                    <td class="border border-gray-300 px-3 py-2">{{ $row['activity'] }}</td>
                                    <td class="border border-gray-300 px-3 py-2" title="{{ $row['project_name'] }}">{{ $row['project'] }}</td>
                                    @foreach ($report['weeks'] as $week)
                                        <td class="border border-gray-300 px-2 py-2 text-center">{{ number_format($row['hours'][$week['number']] ?? 0, 2) }}</td>
                                    @endforeach
                                    <td class="border border-gray-300 px-2 py-2 text-center font-semibold">{{ number_format($row['total_hours'], 2) }}</td>
                                    <td class="border border-gray-300 px-2 py-2 text-center">{{ number_format($row['percentage'], 2) }}%</td>
                                </tr>
                            @endforeach
                            <tr class="bg-amber-50 font-medium text-amber-900">
                                <td colspan="2" class="border border-gray-300 px-3 py-2">{{ __('Office Holidays / Leave Hours') }}</td>
                                @foreach ($report['weeks'] as $week)
                                    <td class="border border-gray-300 px-2 py-2 text-center">{{ number_format($report['weekly_leave_hours'][$week['number']] ?? 0, 2) }}</td>
                                @endforeach
                                <td class="border border-gray-300 px-2 py-2 text-center">{{ number_format($report['total_leave_hours'], 2) }}</td>
                                <td class="border border-gray-300"></td>
                            </tr>
                            <tr class="bg-slate-100 font-bold text-gray-800 dark:bg-zinc-900 dark:text-gray-100">
                                <td colspan="2" class="border border-gray-300 px-3 py-2">{{ __('Total Number of Work Hours') }}</td>
                                @foreach ($report['weeks'] as $week)
                                    <td class="border border-gray-300 px-2 py-2 text-center">{{ number_format($report['weekly_totals'][$week['number']] ?? 0, 2) }}</td>
                                @endforeach
                                <td class="border border-gray-300 px-2 py-2 text-center">{{ number_format($report['total_allocated_hours'], 2) }}</td>
                                <td class="border border-gray-300 px-2 py-2 text-center">{{ $report['total_allocated_hours'] > 0 ? '100.00%' : '0.00%' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="grid grid-cols-1 gap-6 border-t border-gray-200 p-5 md:grid-cols-2 dark:border-neutral-700">
                    <div>
                        <h3 class="mb-2 text-sm font-semibold text-gray-700 dark:text-gray-200">{{ __('Office Holidays (if any):') }}</h3>
                        <table class="w-full text-xs">
                            <thead class="bg-gray-50 dark:bg-zinc-900">
                                <tr>
                                    <th class="border border-gray-200 px-2 py-1 text-left">{{ __('Holiday Name') }}</th>
                                    <th class="border border-gray-200 px-2 py-1 text-left">{{ __('Date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse ($report['holidays'] as $holiday)
                                <tr>
                                    <td class="border border-gray-200 px-2 py-1">{{ $holiday['name'] }}</td>
                                    <td class="border border-gray-200 px-2 py-1">{{ $holiday['date'] }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="border border-gray-200 px-2 py-2 text-center text-gray-400">{{ __('No office holidays recorded.') }}</td></tr    >
                            @endforelse                        
                            </tbody>
                        </table>
                    </div>
                    <div>
                        <h3 class="mb-2 text-sm font-semibold text-gray-700 dark:text-gray-200">{{ __('Leave Information') }}</h3>
                        <table class="w-full text-xs">
                            <thead class="bg-gray-50 dark:bg-zinc-900">
                                <tr>
                                    <th class="border border-gray-200 px-2 py-1 text-left">{{ __('Start Date') }}</th>
                                    <th class="border border-gray-200 px-2 py-1 text-left">{{ __('End Date') }}</th>
                                    <th class="border border-gray-200 px-2 py-1 text-left">{{ __('Leave Type') }}</th>
                                    <th class="border border-gray-200 px-2 py-1 text-right">{{ __('Days') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($report['leaves'] as $leave)
                                    <tr>
                                        <td class="border border-gray-200 px-2 py-1">{{ $leave['start'] }}</td>
                                        <td class="border border-gray-200 px-2 py-1">{{ $leave['end'] }}</td>
                                        <td class="border border-gray-200 px-2 py-1">{{ $leave['type'] }}</td>
                                        <td class="border border-gray-200 px-2 py-1 text-right">{{ number_format($leave['days'], 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="border border-gray-200 px-2 py-2 text-center text-gray-400">{{ __('No approved leave recorded.') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <hr class="border-gray-500 dark:border-neutral-700 my-5">

                <div class="border-t border-gray-200 bg-gray-50 px-5 py-4 text-sm dark:border-neutral-700 dark:bg-zinc-900">
                    <div class="flex flex-wrap justify-between gap-2">
                        <p><span class="font-semibold">{{ __('PAR Status:') }}</span> {{ $report['status'] }}</p>
                        <p><span class="font-semibold">{{ __('Supervisor:') }}</span> {{ $report['employee']['supervisor'] }}</p>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">{{ __('Note: This is an online system generated copy. No signature is needed.') }}</p>
                </div>
            </section>
        @endif
    </div>
</div>
