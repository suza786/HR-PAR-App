<div>
    <div class="mb-6">
        <h1 class="text-3xl font-bold">{{ __('Activity Allocations') }}</h1>
    </div>

      <!-- ========== SECTION 1: Monthly Activities ========== -->
    <div class="space-y-6">
        <div class="bg-white border border-gray-200 rounded-lg p-6 shadow">
            <h2 class="text-2xl font-bold mb-4">{{ __('Monthly Activities') }}</h2>            

            <!-- Employee Dropdown -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-3">
                <div class="mb-4">
                    <label class="block text-sm font-semibold mb-2">{{ __('Employee: ') }}</label>
                    
                    <select wire:model="selectedEmployeeId" class="w-full border border-gray-300 rounded px-3 py-2">
                        <option value="0">{{ __('Select Employee') }}</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee['emp_id'] }}">{{ $employee['emp_id'] }} - {{ $employee['name'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>          

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-3">
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
                    <button wire:click="showMonthlyActivities" class="w-full bg-blue-500 text-white px-2 py-2 rounded hover:bg-blue-600 font-semibold">
                        {{ __('Show') }}
                    </button>
                </div>
            </div>

            @if($monthlyActivitiesLoaded && $monthlyActivities->count())
                <div class="overflow-x-auto border border-gray-200 rounded-lg mt-5">
                    <table class="w-full text-sm border-collapse">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border border-gray-300 px-4 py-2 text-left font-semibold">{{ __('Week') }}</th>
                                <th class="border border-gray-300 px-4 py-2 text-left font-semibold">{{ __('Employee') }}</th>
                                <th class="border border-gray-300 px-4 py-2 text-left font-semibold">{{ __('Leave Hours') }}</th>
                                <th class="border border-gray-300 px-4 py-2 text-left font-semibold">{{ __('Working Hours') }}</th>
                                <th class="border border-gray-300 px-4 py-2 text-left font-semibold">{{ __('Note') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($monthlyActivities as $activity)
                                <tr class="border-b hover:bg-gray-50" align="center">
                                    <td class="px-4 py-2">{{ 'Week-' . $activity->parCalendar->week }}</td>
                                    <td class="px-4 py-2">{{ $activity->employee->emp_id ?? 'N/A' }}</td>
                                    <td class="px-4 py-2">{{ $activity->leave_hrs }}</td>
                                    <td class="px-4 py-2">{{ $activity->working_hrs }}</td>
                                    <td class="px-4 py-2">{{ $activity->note }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            @elseif($monthlyActivitiesLoaded)
                <div class="bg-green-200 border border-yellow-400 text-red-700 mt-10 px-4 py-3 rounded">
                    {{ $monthlyActivitiesMessage }}
                </div>
            @endif
        </div>

        <div class="bg-white border border-gray-200 rounded-lg p-6 shadow">
            <h2 class="text-2xl font-bold mb-4">{{ __('Salary Allocations') }}</h2>

            @if($activityAllocationsAlreadyCreated)
                <div class="bg-green-200 border border-yellow-400 text-blue-800 px-4 py-3 rounded">
                    {{ $activityAllocationsMessage }}
                </div>
            @elseif($salaryAllocationsLoaded && $salaryAllocations->count())
                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="w-full text-sm border-collapse">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border border-gray-300 px-4 py-2 text-left font-semibold">{{ __('Project') }}</th>
                                <th class="border border-gray-300 px-4 py-2 text-left font-semibold">{{ __('Allocation Ratio') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($salaryAllocations as $allocation)
                                <tr class="border-b hover:bg-gray-50" align="center">
                                    <td class="px-4 py-2">{{ $allocation->project->project_name ?? 'N/A' }}</td>
                                    <td class="px-4 py-2">{{ number_format($allocation->allocation_ratio * 100, 2) }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @elseif($monthlyActivitiesLoaded)
                <div class="bg-yellow-200 border border-yellow-400 text-red-700 px-4 py-3 rounded">
                    {{ $salaryAllocationsMessage ?? __('Salary Allocations information not updated for the employee. Please update the information before proceed') }}
                </div>
            @endif
        </div>

        @if(! $activityAllocationsAlreadyCreated && $salaryAllocationsLoaded && $salaryAllocations->count())
            <div class="bg-white border border-gray-200 rounded-lg p-6 shadow">
                <h2 class="text-2xl font-bold mb-4">{{ __('Activity Allocations') }}</h2>

                <form wire:submit.prevent="save">
                    @php
                        $weeklyActivitiesSorted = $monthlyActivities
                            ->sortBy(fn($activity) => $activity->parCalendar->week)
                            ->filter(fn($activity) => (float) $activity->working_hrs > 0)
                            ->values();
                    @endphp

                    @if(session()->has('success'))
                        <div class="mb-4 rounded border border-green-300 bg-green-50 px-4 py-3 text-sm text-green-700">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session()->has('error'))
                        <div class="mb-4 rounded border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($weeklyHoursError)
                        <div class="mb-4 rounded border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700">
                            {{ $weeklyHoursError }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="mb-4 rounded border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700">
                            <ul class="list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="w-full text-sm border-collapse">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border border-gray-300 px-4 py-2 text-left font-semibold">{{ __('Project Name') }}</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left font-semibold">{{ __('Activity Name') }}</th>
                                    @foreach($weeklyActivitiesSorted as $activity)
                                        <th class="border border-gray-300 px-4 py-2 text-left font-semibold">{{ __('Week') . '-' . $activity->parCalendar->week . ' hrs' }}</th>
                                    @endforeach
                                    <th class="border border-gray-300 px-4 py-2 text-left font-semibold">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activityAllocations as $rowKey => $row)
                                    <tr wire:key="activity-allocation-row-{{ $rowKey }}" class="border-b hover:bg-gray-50" align="center">
                                        <td class="px-4 py-2">
                                            {{ $row['project_name'] }}
                                            <input type="hidden" wire:model="activityAllocations.{{ $rowKey }}.project_id">
                                        </td>
                                        <td class="px-4 py-2">
                                            <select wire:model.defer="activityAllocations.{{ $rowKey }}.activity_id" class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
                                                <option value="">{{ __('Select Activity') }}</option>
                                                @foreach($activities as $activityOption)
                                                    <option value="{{ $activityOption->id }}">{{ $activityOption->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        @foreach($weeklyActivitiesSorted as $activity)
                                            <td class="px-4 py-2">
                                                <input type="number" step="0.01" min="0" wire:model.defer="activityAllocations.{{ $rowKey }}.weekly_hrs.{{ $activity->id }}" class="w-full border border-gray-300 rounded px-2 py-1 text-sm" placeholder="0.00">
                                            </td>
                                        @endforeach
                                        <td class="px-4 py-2">
                                            <button type="button" wire:click="addActivity('{{ $rowKey }}')" class="whitespace-nowrap bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 font-semibold">
                                                {{ __('Add Activity') }}
                                            </button>
                                            @if($row['is_added'] ?? false)
                                                <button type="button" wire:click="removeActivity('{{ $rowKey }}')" class="ml-2 whitespace-nowrap bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 font-semibold">
                                                    {{ __('Remove') }}
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-gray-100 font-semibold">
                                    <td colspan="2" class="border border-gray-300 px-4 py-2 text-left">{{ __('Total hours') }}</td>
                                    @foreach($weeklyActivitiesSorted as $activity)
                                        <td class="border border-gray-300 px-4 py-2 text-left">{{ number_format($weeklyTotalsExpected[$activity->id] ?? 0, 2) }}</td>
                                    @endforeach
                                    <td class="border border-gray-300 px-4 py-2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="flex justify-end mt-6">
                        <button type="submit" class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600 font-semibold">
                            {{ __('Save') }}
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>
