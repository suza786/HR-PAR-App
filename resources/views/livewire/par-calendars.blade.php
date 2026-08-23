<div>
   <div class="max-w-5xl mx-auto px-4 py-8">

    {{-- ─────────────────────────────────────────── --}}
    {{-- Header --}}
    {{-- ─────────────────────────────────────────── --}}
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Par Calendars</h1>
        <p class="text-sm text-gray-500 mt-1">Generate and save weekly working-day breakdowns by year &amp; month.</p>
    </div>

    {{-- ─────────────────────────────────────────── --}}
    {{-- Controls --}}
    {{-- ─────────────────────────────────────────── --}}
    <div class="bg-white border border-gray-200 rounded-xl p-5 mb-6 shadow-sm">
        <div class="flex flex-wrap items-end gap-4">

            {{-- Year --}}
            <div class="flex flex-col gap-1">
                <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Year</label>
                <select wire:model="selectedYear"
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @for ($y = 2020; $y <= 2030; $y++)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
                @error('selectedYear')
                    <span class="text-xs text-red-500">{{ $message }}</span>
                @enderror
            </div>

            {{-- Month --}}
            <div class="flex flex-col gap-1">
                <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Month</label>
                <select wire:model="selectedMonth"
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @foreach (['January','February','March','April','May','June','July','August','September','October','November','December'] as $name)
                        <option value="{{ $name }}">{{ $name }}</option>
                    @endforeach
                </select>
                @error('selectedMonth')
                    <span class="text-xs text-red-500">{{ $message }}</span>
                @enderror
            </div>

            {{-- Action buttons --}}
            <div class="flex gap-2 mt-1">
                <button wire:click="generate" wire:loading.attr="disabled"
                        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 disabled:opacity-60 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                    <span wire:loading wire:target="generate">
                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                        </svg>
                    </span>
                    Generate
                </button>

                @if ($generated)
                    <button wire:click="save" wire:loading.attr="disabled"
                            class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-60 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                        <span wire:loading wire:target="save">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                            </svg>
                        </span>
                        Save to Database
                    </button>

                    <button wire:click="resetForm"
                            class="text-sm font-medium text-blue-700 hover:text-gray-700 px-3 py-2 rounded-lg border border-gray-200 hover:border-gray-300 transition-colors">
                        Reset
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- ─────────────────────────────────────────── --}}
    {{-- Success / info message --}}
    {{-- ─────────────────────────────────────────── --}}
    @if ($message)
        <div class="mb-5 flex items-center gap-2 rounded-lg px-4 py-3 text-sm font-medium
            {{ $saved ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
            @if ($saved)
                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            @else
                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M12 3a9 9 0 100 18A9 9 0 0012 3z"/>
                </svg>
            @endif
            {{ $message }}
        </div>
    @endif

    @if ($holidayMessage)
        <div class="mb-5 rounded-lg px-4 py-3 text-sm font-medium bg-amber-50 text-amber-700 border border-amber-200">
            {{ $holidayMessage }}
        </div>
    @endif

    {{-- ─────────────────────────────────────────── --}}
    {{-- Summary cards --}}
    {{-- ─────────────────────────────────────────── --}}
    @if ($generated && !empty($summary))
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 mb-6">
            @foreach ([
                ['label' => 'Total Weeks',       'value' => $summary['total_weeks'],        'color' => 'bg-blue-50 text-blue-700'],
                ['label' => 'Total Days',         'value' => $summary['total_days'],         'color' => 'bg-slate-50 text-slate-700'],
                ['label' => 'Working Days',       'value' => $summary['total_working_days'], 'color' => 'bg-emerald-50 text-emerald-700'],
                ['label' => 'Office Holidays',    'value' => $summary['total_office_holidays'], 'color' => 'bg-amber-50 text-amber-700'],
                ['label' => 'Off Days',           'value' => $summary['total_off_days'],     'color' => 'bg-rose-50 text-rose-700'],
            ] as $card)
                <div class="rounded-xl border border-gray-100 p-4 shadow-sm bg-white">
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">{{ $card['label'] }}</p>
                    <p class="text-3xl font-semibold {{ $card['color'] }} inline-block px-2 py-0.5 rounded-md">{{ $card['value'] }}</p>
                </div>
            @endforeach
        </div>
    @endif

    {{-- ─────────────────────────────────────────── --}}
    {{-- Weekly table --}}
    {{-- ─────────────────────────────────────────── --}}
    @if ($generated && !empty($weeks))
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-700">
                    {{ $summary['month_name'] }} {{ $selectedYear }} — Weekly Breakdown
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs tracking-wide">
                        <tr>
                            <th class="px-5 py-3 text-left font-medium">Week</th>
                            <th class="px-5 py-3 text-left font-medium">Start Date</th>
                            <th class="px-5 py-3 text-left font-medium">End Date</th>
                            <th class="px-5 py-3 text-center font-medium">Total Days</th>
                            <th class="px-5 py-3 text-center font-medium">Working Days</th>
                            <th class="px-5 py-3 text-center font-medium">Office Holidays</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($weeks as $week)
                            <tr class="hover:bg-gray-50 transition-colors
                                {{ $week['working_days'] === 0 ? 'bg-rose-50/40' : '' }}">

                                <td class="px-5 py-3 font-medium text-gray-700">
                                    Week {{ $week['week'] }}
                                </td>

                                <td class="px-5 py-3 text-gray-600">
                                    {{ $week['start_label'] }}
                                </td>

                                <td class="px-5 py-3 text-gray-600">
                                    {{ $week['end_label'] }}
                                </td>

                                <td class="px-5 py-3 text-center text-gray-700 font-medium">
                                    {{ $week['total_days'] }}
                                </td>

                                <td class="px-5 py-3 text-center">
                                    @if ($week['working_days'] === 0)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-100 text-rose-700">
                                            0 — Off only
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                            {{ $week['working_days'] }}
                                        </span>
                                    @endif
                                </td>

                                <td class="px-5 py-3 text-center">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                                        {{ $week['office_holidays'] }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                    {{-- Footer totals --}}
                    <tfoot class="bg-gray-50 border-t border-gray-200 font-semibold text-gray-700">
                        <tr>
                            <td colspan="3" class="px-5 py-3 text-right text-xs uppercase tracking-wide text-gray-400">Totals</td>
                            <td class="px-5 py-3 text-center">{{ $summary['total_days'] }}</td>
                            <td class="px-5 py-3 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                    {{ $summary['total_working_days'] }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                                    {{ $summary['total_office_holidays'] }}
                                </span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Legend --}}
        <p class="mt-3 text-xs text-gray-400">
            <span class="inline-block w-3 h-3 rounded-sm bg-rose-200 mr-1 align-middle"></span>
            Highlighted rows contain only Friday / Saturday — working days = 0.
            &nbsp;|&nbsp; Week starts Sunday, ends Saturday.
            &nbsp;|&nbsp; Off days: Friday &amp; Saturday.
        </p>
    @endif

</div>
</div>
