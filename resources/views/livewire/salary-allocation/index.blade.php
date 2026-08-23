<div>
    <h1 class="text-2xl font-bold mb-4">Salary Allocations</h1>

    @if (session()->has('success'))
        <div class="bg-green-100 text-green-800 p-2 rounded mb-2">{{ session('success') }}</div>
    @endif
    
    <a href="{{ route('salary-allocations.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded mb-4 inline-block float-right">Add Salary Allocation</a>
    @if(Auth::user()->user_type === 'HR')
        <div class="bg-white border border-gray-200 rounded-lg p-6 shadow mb-6">
            <h2 class="text-xl font-bold mb-4">Salary Allocation Search</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-semibold mb-2">Year</label>
                    <select wire:model="selectedYear" class="w-full border border-gray-300 rounded px-3 py-2">
                        @for($year = now()->year - 3; $year <= now()->year + 3; $year++)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-2">Month</label>
                    <select wire:model="selectedMonth" class="w-full border border-gray-300 rounded px-3 py-2">
                        @foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $monthName)
                            <option value="{{ $monthName }}">{{ $monthName }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold mb-2">Employee</label>
                    <select wire:model="selectedEmployee" class="w-full border border-gray-300 rounded px-3 py-2">
                        <option value="">Select Employee</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee['emp_id'] }}">{{ $employee['emp_id'] }} - {{ $employee['name'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <button type="button" wire:click="showSalaryAllocationData" class="bg-blue-500 text-white px-6 py-2 rounded font-semibold">Show</button>
        </div>
    @endif
    
        @if($allocationMessage)
            <div class="bg-green-200 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $allocationMessage }}</div>  

        @elseif(Auth::user()->user_type !== 'HR' || $showSalaryAllocations)
            <table class="min-w-full bg-white border border-gray-300" style="border-collapse: separate; border-spacing: 0 8px;">
                <thead class="bg-gray-300">
                    <tr class="bg-blue-300">
                        <th class="py-3 px-4 border border-gray-300">Employee</th>
                        <th class="py-3 px-4 border border-gray-300">Project</th>
                        <th class="py-3 px-4 border border-gray-300">Allocation Ratio</th>
                        <th class="py-3 px-4 border border-gray-300">Start Month</th>
                        <th class="py-3 px-4 border border-gray-300">End Month</th>
                        <th class="py-3 px-4 border border-gray-300">Year</th>
                        @if(Auth::user()->user_type === 'HR')
                            <th class="py-3 px-4 border border-gray-300">Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($salaryAllocations as $allocation)
                    <tr>
                        <td class="py-2 px-4 border border-gray-300">{{ $allocation->employee ? $allocation->employee->name . ' ' . $allocation->employee->last_name : 'N/A' }}</td>
                        <td class="py-2 px-4 border border-gray-300">{{ $allocation->project ? $allocation->project->project_name : 'N/A' }}</td>
                        <td class="py-2 px-4 border border-gray-300">{{ number_format($allocation->allocation_ratio * 100, 2) }}%</td>
                        <td class="py-2 px-4 border border-gray-300">{{ $allocation->start_month ? \Carbon\Carbon::parse($allocation->start_month)->format('M Y') : 'N/A' }}</td>
                        <td class="py-2 px-4 border border-gray-300">{{ $allocation->end_month ? \Carbon\Carbon::parse($allocation->end_month)->format('M Y') : 'N/A' }}</td>
                        <td class="py-2 px-4 border border-gray-300">{{ $allocation->curr_year }}</td>
                        @if(Auth::user()->user_type === 'HR')
                        <td class="py-2 px-4 border border-gray-300">
                                <a href="{{ route('salary-allocations.edit', $allocation->id) }}" class="bg-yellow-500 text-white px-2 py-1 rounded mr-1">Edit</a>
                            </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
</div>