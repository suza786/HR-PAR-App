<div class="h-full rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-zinc-800">
    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow mb-6">
        <h2 class="text-2xl font-bold mb-4 text-gray-800">{{ __('Leave Information') }} </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <!-- Year Dropdown -->
            <div>
                <label class="block text-sm font-semibold mb-2">{{ __('Year') }}</label>
                <select wire:model="selectedYear" class="w-full border border-gray-300 rounded px-3 py-2">
                    @for($year = now()->year - 3; $year <= now()->year + 3; $year++)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endfor
                </select>
            </div>

            <!-- Month Dropdown -->
            <div>
                <label class="block text-sm font-semibold mb-2">{{ __('Month') }}</label>
                <select wire:model="selectedMonth" class="w-full border border-gray-300 rounded px-3 py-2">
                    @foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $monthName)
                        <option value="{{ $monthName }}">{{ $monthName }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Show Data Button -->
            <div class="flex items-end">
                <button wire:click="showData" class="w-full bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 font-semibold">
                    {{ __('Show Data') }}
                </button>
            </div>
        </div>


        @if ($loaded)
            <div class="mt-5">
                @if ($message)
                    <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                        {{ $message }}
                    </div>
                @else
                    <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-neutral-700">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500 dark:bg-zinc-900 dark:text-gray-400">
                                <tr>
                                    <th class="px-4 py-3 text-left font-medium">{{ __('Start Date') }}</th>
                                    <th class="px-4 py-3 text-left font-medium">{{ __('End Date') }}</th>
                                    <th class="px-4 py-3 text-left font-medium">{{ __('Leave Type') }}</th>
                                    <th class="px-4 py-3 text-center font-medium">{{ __('Days') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-neutral-700">
                                @foreach ($leaves as $leave)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700/50">
                                        <td class="px-4 py-3 text-gray-700 dark:text-gray-200">{{ $leave['start_date'] }}</td>
                                        <td class="px-4 py-3 text-gray-700 dark:text-gray-200">{{ $leave['end_date'] }}</td>
                                        <td class="px-4 py-3 text-gray-700 dark:text-gray-200">{{ $leave['leave_type'] }}</td>
                                        <td class="px-4 py-3 text-center font-medium text-gray-800 dark:text-gray-100">{{ $leave['days'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
