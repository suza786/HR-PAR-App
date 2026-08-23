<div>
    <!-- Header with Back Button -->
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold">{{ __('Edit Monthly Activities') }}</h1>
        <a href="{{ route('monthly-activities.index') }}" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600" wire:navigate>
            {{ __('Back') }}
        </a>
    </div>

    <!-- Session Messages -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- ========== SECTION 1: PAR CALENDAR (READ-ONLY) ========== -->
    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow mb-6">
        <h2 class="text-2xl font-bold mb-4 text-gray-800">{{ __('PAR Calendar') }}</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <!-- Year Display -->
            <div>
                <label class="block text-sm font-semibold mb-2">{{ __('Year') }}</label>
                <input type="text" value="{{ $selectedYear }}" class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-100" readonly>
            </div>

            <!-- Month Display -->
            <div>
                <label class="block text-sm font-semibold mb-2">{{ __('Month') }}</label>
                <input type="text" value="{{ $selectedMonth }}" class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-100" readonly>
            </div>

            <div></div>
        </div>

        <!-- Calendar Data Table -->
        @if(count($calendarWeeks) > 0)
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
        <div class="bg-white border border-gray-200 rounded-lg p-6 shadow mb-6">
            <h2 class="text-2xl font-bold mb-4 text-gray-800">{{ __('Your Leaves for the month of') }} {{ $selectedMonth }}</h2>

            @if($userLeavesLoaded && count($userLeaves) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm border-collapse">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border border-gray-300 px-4 py-2 text-left font-semibold">{{ __('Start Date') }}</th>
                                <th class="border border-gray-300 px-4 py-2 text-left font-semibold">{{ __('End Date') }}</th>
                                <th class="border border-gray-300 px-4 py-2 text-left font-semibold">{{ __('Leave Type') }}</th>
                                <th class="border border-gray-300 px-4 py-2 text-center font-semibold">{{ __('Days') }}</th>
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
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ $userLeavesMessage ?: __("You don't have any leave entry for the month {$selectedMonth}-{$selectedYear} ....") }}
                </div>
            @endif
        </div>
    @endif

    <!-- ========== SECTION 3: MONTHLY ACTIVITIES (EDIT) ========== -->
    @if(count($calendarWeeks) > 0)
        <div class="bg-white border border-gray-200 rounded-lg p-6 shadow">
            <h2 class="text-2xl font-bold mb-4 text-gray-800">{{ __('Monthly Activities') }}</h2>

                <!-- Single Employee Selector -->
                <div class="mb-4">
                    <label class="block text-sm font-semibold mb-2">{{ __('Employee') }}</label>
                    <select wire:model="selectedEmployee" wire:change="updatedSelectedEmployee($event.target.value)" class="w-full border border-gray-300 rounded px-3 py-2" disabled>
                        <option value="0" >{{ __('Select Employee') }}</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->emp_id }}">{{ $emp->emp_id ?? '' }} {{ $emp->first_name ?? '' }} {{ $emp->last_name ?? '' }}</option>
                        @endforeach
                    </select>
                </div>

                <form wire:submit.prevent="updateActivities">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm border-collapse">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border border-gray-300 px-4 py-2 text-left font-semibold">{{ __('Week') }}</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left font-semibold">{{ __('Employee') }}</th>
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

                                    <!-- Employee Dropdown -->
                                    <td class="border border-gray-300 px-4 py-2">
                                        @php $sel = $employees->firstWhere('emp_id', $selectedEmployee); @endphp
                                        @if($sel)
                                            {{ $sel->emp_id ?? '' }} 
                                        @else
                                            <span class="text-gray-500">{{ __('Not selected') }}</span>
                                        @endif
                                    </td>

                                    <!-- Leave Hours -->
                                    <td class="border border-gray-300 px-4 py-2">
                                        <input type="number" wire:model="activities.{{ $index }}.leave_hrs" class="w-full border border-gray-300 rounded px-2 py-1 text-sm text-center" placeholder="0" min="0" max="8">
                                    </td>

                                    <!-- Working Hours -->
                                    <td class="border border-gray-300 px-4 py-2">
                                        <input type="number" wire:model="activities.{{ $index }}.working_hrs" class="w-full border border-gray-300 rounded px-2 py-1 text-sm text-center" placeholder="0" min="0" max="40">
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
                    <button type="submit" class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600 font-semibold">
                        {{ __('Update') }}
                    </button>
                    <a href="{{ route('monthly-activities.index') }}" class="bg-gray-300 text-gray-800 px-6 py-2 rounded hover:bg-gray-400 font-semibold" wire:navigate>
                        {{ __('Cancel') }}
                    </a>
                </div>
            </form>
        </div>
    @endif
</div>
