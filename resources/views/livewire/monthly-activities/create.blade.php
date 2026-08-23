<div>
    <!-- Header with Back Button -->
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold">{{ __('Create Monthly Activities') }}</h1>
        <a href="{{ route('monthly-activities.index') }}" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-blue-600" wire:navigate>
            {{ __('Back') }}
        </a>
    </div>

    <!-- Session Messages -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- ========== SECTION 1: PAR CALENDAR ========== -->
    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow mb-6">
        <h2 class="text-2xl font-bold mb-4 text-gray-800">{{ __('PAR Calendar') }}</h2>

        <div class="grid grid-cols-3 md:grid-cols-3 gap-4 mb-6">
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

            <div>
                <label class="block text-sm font-semibold mb-2">{{ __('Employee') }}</label>
                <select wire:model="selectedEmployee" class="w-full border border-gray-300 rounded px-3 py-2">
                    <option value="">{{ __('Select Employee') }}</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee['emp_id'] }}">{{ $employee['emp_id'] }} - {{ $employee['name'] }}</option>
                    @endforeach
                </select>
                @error('selectedEmployee') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <!-- Daily Working Hours Dropdown -->
            <div>
                <label class="block text-sm font-semibold mb-2">{{ __('Daily Working Hours') }}</label>
                <select wire:model="dailyWorkingHours" class="w-full border border-gray-300 rounded px-3 py-2">
                    @foreach([3,3.5,4,4.5,5,5.5,6,6.5,7,7.5,8,8.5,9,9.5,10] as $hours)
                        <option value="{{ $hours }}">{{ $hours }}</option>
                    @endforeach
                </select>
            </div>
            <!-- Show Data Button -->
            <div class="flex items-end">
                <button type="button" wire:click="showCalendarData" class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600 font-semibold">
                    {{ __('Show Data') }}
                </button>
            </div>
        </div>

        @if($duplicateMessage)
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-5 rounded mb-4">
                {{ $duplicateMessage }}
            </div>
        @endif
       
        <!-- Calendar Data Table or Message -->
        @if($calendarMessage)
            <div class="bg-gray-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ $calendarMessage }}
            </div>
        @elseif($calendarGenerated && count($calendarWeeks) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm border-collapse">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border border-gray-300 px-4 py-2 text-left font-semibold">{{ __('Week #') }}</th>
                            <th class="border border-gray-300 px-4 py-2 text-left font-semibold">{{ __('Start Date') }}</th>
                            <th class="border border-gray-300 px-4 py-2 text-left font-semibold">{{ __('End Date') }}</th>
                            <th class="border border-gray-300 px-4 py-2 text-center font-semibold">{{ __('Total Days') }}</th>
                            <th class="border border-gray-300 px-4 py-2 text-center font-semibold">{{ __('Working Days') }}</th>
                            <th class="border border-gray-300 px-4 py-2 text-center font-semibold">{{ __('Office Holidays') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($calendarWeeks as $week)
                            <tr class="hover:bg-gray-50">
                                <td class="border border-gray-300 px-4 py-2 font-semibold">{{ __('Week ' . $week['week']) }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ $week['weekStart'] }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ $week['weekEnd'] }}</td>
                                <td class="border border-gray-300 px-4 py-2 text-center">{{ $week['totalDays'] }}</td>
                                <td class="border border-gray-300 px-4 py-2 text-center">{{ $week['workingDays'] }}</td>
                                <td class="border border-gray-300 px-4 py-2 text-center">{{ $week['officeHolidays'] ?? 0 }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- ========== SECTION 2: YOUR LEAVES ========== -->
    @if($userLeavesLoaded)
        <div class="bg-emerald-100 border border-gray-200 rounded-lg p-6 shadow mb-6">
            <h2 class="text-2xl font-bold mb-4 text-gray-800">{{ __('Your Leaves for the month of') }} {{ $selectedMonth }}-{{ $selectedYear }}</h2>

            @if($userLeavesLoaded && count($userLeaves) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm border-collapse">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border border-sky-300 px-4 py-2 text-left font-semibold">{{ __('Start Date') }}</th>
                                <th class="border border-sky-300 px-4 py-2 text-left font-semibold">{{ __('End Date') }}</th>
                                <th class="border border-sky-300 px-4 py-2 text-left font-semibold">{{ __('Leave Type') }}</th>
                                <th class="border border-sky-300 px-4 py-2 text-center font-semibold">{{ __('Days') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($userLeaves as $leave)
                                <tr class="hover:bg-gray-50">
                                    <td class="border border-gray-300 px-4 py-2">{{ $leave['startDate'] }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ $leave['endDate'] }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ $leave['leaveType'] }}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">{{ $leave['days'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ $userLeavesMessage ?: __("You don't have any leave entry for the month {$selectedMonth}-{$selectedYear} ....") }}
                </div>
            @endif
        </div>
    @endif
<!-- ========== SECTION 3: Monthly Holidays ========== -->
    @if($holidayLoaded)
        <div class="bg-slate-100 border border-gray-200 rounded-lg p-6 shadow mb-6">
            <h2 class="text-2xl font-bold mb-4 text-gray-800">{{ __('Holidays for the month of') }} {{ $selectedMonth }}-{{ $selectedYear }}</h2>

            @if($holidayLoaded && count($holidayList) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm border-collapse">
                        <thead class="bg-gray-100 text-center" align="center">
                            <tr>
                                <th class="border border-gray-300 px-4 py-2 text-left font-semibold">{{ __('Holiday Name') }}</th>
                                <th class="border border-gray-300 px-4 py-2 text-left font-semibold">{{ __('Date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($holidayList as $holiday)
                                <tr class="hover:bg-gray-50" align="center">
                                    <td class="border border-gray-300 px-4 py-2">{{ $holiday['title'] }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ $holiday['date'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="text-sm font-semibold text-red-700 bg-blue-200 p-2 mt-4 rounded text-center">
                        {{ __('Note: holiday hours are already adjusted in the following weekly hours. ') }}
                    </div>
                </div>
            @else
                <div class="border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ $holidayMessage ?: __("You don't have any holidays for the month {$selectedMonth}-{$selectedYear} ....") }}
                </div>
            @endif                        
        </div>
    @endif

    <!-- ========== SECTION 4: MONTHLY ACTIVITIES ========== -->
    @if($calendarGenerated && count($calendarWeeks) > 0)
        <div class="bg-white border border-gray-200 rounded-lg p-6 shadow">
            <h2 class="text-2xl font-bold mb-4 text-gray-800">{{ __('Monthly Activities') }}</h2>

            <form wire:submit.prevent="saveActivities">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm border-collapse">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border border-gray-300 px-4 py-2 text-left font-semibold">{{ __('Week') }}</th>
                                <th class="border border-gray-300 px-4 py-2 text-left font-semibold">{{ __('Employee ID') }}</th>
                                <th class="border border-gray-300 px-4 py-2 text-center font-semibold">{{ __('Leave Hrs') }}</th>
                                <th class="border border-gray-300 px-4 py-2 text-center font-semibold">{{ __('Working Hrs') }}</th>
                                <th class="border border-gray-300 px-4 py-2 text-left font-semibold">{{ __('Note') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activities as $index => $activity)
                                <tr class="hover:bg-gray-50">
                                    <!-- Week Display -->
                                    <td class="border border-gray-300 px-4 py-2">
                                        <span class="font-semibold">Week {{ $activity['week_number'] }}</span>
                                    </td>

                                    <!-- SelectedEmployee -->
                                    <td class="border border-gray-300 px-4 py-2">
                                         <input type="text" wire:model="selectedEmployee" class="w-full border border-gray-300 rounded px-2 py-1 text-sm" readonly >
                                    </td>

                                    <!-- Leave Hours -->
                                    <td class="border border-gray-300 px-4 py-2">
                                        <input type="number" wire:model="activities.{{ $index }}.leave_hrs" class="w-full border border-gray-300 rounded px-2 py-1 text-sm text-center" placeholder="0" min="0" max="{{ $activity['working_hrs'] ?? 40 }}">
                                        @error('activities.'.$index.'.leave_hrs') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </td>

                                    <!-- Working Hours -->
                                    <td class="border border-gray-300 px-4 py-2">
                                        <input type="number" wire:model="activities.{{ $index }}.working_hrs" class="w-full border border-gray-300 rounded px-2 py-1 text-sm text-center" placeholder="0" min="0" max="{{ $activity['working_hrs'] ?? 40 }}">
                                        @error('activities.'.$index.'.working_hrs') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </td>

                                    <!-- Note -->
                                    <td class="border border-gray-300 px-4 py-2">
                                        <input type="text" wire:model="activities.{{ $index }}.note" class="w-full border border-gray-300 rounded px-2 py-1 text-sm" placeholder="Optional note">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-3 mt-6">
                    <button type="submit" wire:loading.attr="disabled" wire:target="saveActivities"
                            class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600 font-semibold disabled:opacity-50">
                        <span wire:loading.remove wire:target="saveActivities">{{ __('Save') }}</span>
                        <span wire:loading wire:target="saveActivities">{{ __('Saving...') }}</span>
                    </button>
                    <a href="{{ route('monthly-activities.index') }}" class="bg-gray-300 text-gray-800 px-6 py-2 rounded hover:bg-gray-400 font-semibold" wire:navigate>
                        {{ __('Cancel') }}
                    </a>
                </div>
            </form>
        </div>
    @endif
</div>
