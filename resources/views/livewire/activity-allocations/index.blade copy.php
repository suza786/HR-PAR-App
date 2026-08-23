<div>
    <!-- Header with Back Button -->
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold">{{ __('Activity Allocations') }}</h1>
        <a href="{{ route('activity-allocations.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600" wire:navigate>
            {{ __('Create Activity Allocation') }}
        </a>
    </div>

    <!-- Session Messages -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if($submissionMessage)
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ $submissionMessage }}
        </div>
    @endif

    <!-- ========== SECTION 1: USER SELECTION ========== -->
    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow mb-6">
        <h2 class="text-2xl font-bold mb-4 text-gray-800">{{ __('Show Existing Activity Allocations') }}</h2>

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

            <!-- Employee Dropdown or Read-only Field -->
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

        <!-- Show Button -->
        <div class="flex justify-start">
            <button type="button" wire:click="showActivityAllocationData" class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600 font-semibold">
                {{ __('Show') }}
            </button>
        </div>
    </div>

    <!-- ========== SECTION 2: ACTIVITY ALLOCATIONS ========== -->
    @if($showActivityAllocations)
        <div class="bg-white border border-gray-200 rounded-lg p-6 shadow">
            <h2 class="text-2xl font-bold mb-4 text-gray-800">
                {{ __('Activity Allocations') }} - {{$selectedEmployee}}
            </h2>

            @if($allocationMessage)
                <div class="bg-yellow-200 border border-yellow-400 text-red-700 px-4 py-3 rounded">
                    {{ $allocationMessage }}
                </div>
            @elseif(count($formattedAllocations) > 0)
                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-100 border-b">
                            <tr>
                                <th class="px-4 py-2 text-left font-semibold">{{ __('Project') }}</th>
                                <th class="px-4 py-2 text-left font-semibold">{{ __('Activity Name') }}</th>
                                @foreach($weeks as $week)
                                    <th class="px-4 py-2 text-center font-semibold">{{ $week['label'] }} hours</th>
                                @endforeach
                                <th class="px-4 py-2 text-center font-semibold">{{ __('Total Hours') }}</th>
                                @if(Auth::user()->user_type === 'HR' && $hasEditableAllocations)
                                    <th class="px-4 py-2 text-center font-semibold">{{ __('Actions') }}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($formattedAllocations as $allocation)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-2">{{ $allocation['project'] }}</td>
                                    <td class="px-4 py-2">{{ $allocation['activity'] }}</td>
                                    @foreach($allocation['hours'] as $hours)
                                        <td class="px-4 py-2 text-center">{{ $hours }}</td>
                                    @endforeach
                                    <td class="px-4 py-2 text-center font-semibold">{{ $allocation['total'] }}</td>
                                    @if(Auth::user()->user_type === 'HR' && $hasEditableAllocations)
                                        <td class="px-4 py-2 text-center">
                                            @if($allocation['status'] === 'pending')
                                                <div class="flex flex-wrap justify-center gap-2">
                                                    @foreach($allocation['edit_links'] as $editLink)
                                                        @if($editLink['id'])
                                                            <a href="{{ route('activity-allocations.edit', $editLink['id']) }}"
                                                               class="text-blue-600 hover:underline" wire:navigate>
                                                                {{ __('Edit') }} {{ $editLink['week'] }}
                                                            </a>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-gray-400">—</span>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                            <!-- Total Row -->
                            <tr class="bg-gray-200 font-bold border-b border-gray-400">
                                <td class="px-4 py-2 text-center font-semibold" colspan="2">Total</td>
                                @php
                                    $weekTotals = [];
                                    $grandTotal = 0;
                                    
                                    // Initialize week totals
                                    foreach($weeks as $index => $week) {
                                        $weekTotals[$index] = 0;
                                    }
                                    
                                    // Calculate totals for each week and grand total
                                    foreach($formattedAllocations as $allocation) {
                                        foreach($allocation['hours'] as $index => $hours) {
                                            $weekTotals[$index] += $hours;
                                        }
                                        $grandTotal += $allocation['total'];
                                    }
                                @endphp
                                @foreach($weekTotals as $weekTotal)
                                    <td class="px-4 py-2 text-center font-semibold">{{ $weekTotal }}</td>
                                @endforeach
                                <td class="px-4 py-2 text-center font-semibold">{{ $grandTotal }}</td>
                                @if(Auth::user()->user_type === 'HR' && $hasEditableAllocations)
                                    <td class="px-4 py-2"></td>
                                @endif
                            </tr>
                        </tbody>
                    </table>
                    <div class="mt-4 py-2 px-10 rounded font-semibold {{ $activityStatus === 'approved' ? 'bg-green-500 text-white' : ($activityStatus === 'pending' ? 'bg-yellow-200 text-red-700' : 'bg-blue-200 text-red-700') }}">
                        <strong>Status:</strong> {{ $activityStatus }}
                    </div>
                    @if($activityStatus === 'pending')
                        <!-- Submit Button -->
                        <div class="flex justify-end mt-4">
                            <button type="button" wire:click="submitActivityAllocationData" class="bg-red-500 text-white px-6 py-2 rounded hover:bg-blue-600 font-semibold">
                                {{ __('Submit') }}
                            </button>
                        </div>
                    @elseif($activityStatus === 'requested')
                        <div class="flex justify-end mt-4 mr-5">
                            <button type="button" wire:click="approveActivityAllocationData" class="bg-green-500 text-white px-6 py-2 rounded font-semibold">
                                {{ __('Approved') }}
                            </button>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    @endif
</div>
