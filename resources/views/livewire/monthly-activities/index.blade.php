<div>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold">{{ __('Monthly Activities') }}</h1>
        <a href="{{ route('monthly-activities.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600" wire:navigate>
            {{ __('Create Monthly Activity') }}
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow mb-6">
        <h2 class="text-2xl font-bold mb-4 text-gray-800">{{ __('Show Existing Monthly Activities') }}</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div>
                <label class="block text-sm font-semibold mb-2">{{ __('Year') }}</label>
                <select wire:model="selectedYear" class="w-full border border-gray-300 rounded px-3 py-2">
                    @for($year = now()->year - 3; $year <= now()->year + 3; $year++)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endfor
                </select>
            </div>

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
            </div>
        </div>

        <button type="button" wire:click="showMonthlyActivityData" class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600 font-semibold">
            {{ __('Show') }}
        </button>
    </div>

    @if($showMonthlyActivities)
        <div class="bg-white border border-gray-200 rounded-lg p-6 shadow">
            <h2 class="text-2xl font-bold mb-4 text-gray-800">{{ __('Monthly Activities') }} - {{ $selectedEmployee }}</h2>

            @if($activityMessage)
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-3 rounded">
                    {{ $activityMessage }}
                </div>
            @else
                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-100 border-b">
                            <tr>
                                <th class="px-4 py-2 text-left font-semibold">{{ __('Month') }}</th>
                                <th class="px-4 py-2 text-left font-semibold">{{ __('Week') }}</th>
                                <th class="px-4 py-2 text-left font-semibold">{{ __('Employee') }}</th>
                                <th class="px-4 py-2 text-left font-semibold">{{ __('Leave Hours') }}</th>
                                <th class="px-4 py-2 text-left font-semibold">{{ __('Working Hours') }}</th>
                                <th class="px-4 py-2 text-left font-semibold">{{ __('Note') }}</th>
                                @if(Auth::user()->user_type === 'HR')
                                    <th class="px-4 py-2 text-left font-semibold">{{ __('Actions') }}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($monthlyActivities as $activity)
                                <tr class="border-b hover:bg-gray-50" wire:key="monthly-activity-{{ $activity['id'] }}">
                                    <td class="px-4 py-2">{{ $activity['par_calendar']['month'] ?? 'N/A' }}</td>
                                    <td class="px-4 py-2">{{ isset($activity['par_calendar']['week']) ? 'Week-'.$activity['par_calendar']['week'] : 'N/A' }}</td>
                                    <td class="px-4 py-2">
                                        {{ $activity['emp_id'] }} - {{ $activity['employee']['name'] ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-2">{{ $activity['leave_hrs'] }}</td>
                                    <td class="px-4 py-2">{{ $activity['working_hrs'] }}</td>
                                    <td class="px-4 py-2">{{ $activity['note'] }}</td>
                                    @if(Auth::user()->user_type === 'HR')
                                    <td class="px-4 py-2">
                                        <a href="{{ route('monthly-activities.edit', $activity['id']) }}" class="text-blue-500 hover:underline" wire:navigate>
                                            {{ __('Edit') }}
                                        </a>
                                    </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif
</div>
