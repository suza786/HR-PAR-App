<div>
    <h1 class="text-2xl font-bold mb-4">Add Salary Allocation</h1>

    <form wire:submit.prevent="save" class="bg-white p-6 rounded shadow">
        <!-- Top Section: Employee, Start Month, End Month -->
        <div class="mb-6 p-4 bg-gray-50 rounded">
            <h3 class="text-lg font-semibold mb-4">Allocation Period</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Employee Dropdown -->
                <div class="mb-4">
                    <label class="block text-sm font-semibold mb-2">{{ __('Employee: ') }}</label>
                    <select wire:model="emp_id" class="w-full border border-gray-300 rounded px-3 py-2">
                        <option value="">{{ __('Select Employee') }}</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee['emp_id'] }}">{{ $employee['emp_id'] }} - {{ $employee['name'] }}</option>
                        @endforeach
                    </select>
                    @error('emp_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Start Month</label>
                    <input type="date" wire:model="start_month" class="border p-2 w-full"
                           min="{{ $currentYear }}-01-01" max="{{ $currentYear }}-12-31">
                    @error('start_month') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">End Month</label>
                    <input type="date" wire:model="end_month" class="border p-2 w-full"
                           min="{{ $currentYear }}-01-01" max="{{ $currentYear }}-12-31">
                    @error('end_month') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Dynamic Allocation Rows -->
        <div class="mb-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Project Allocations</h3>
                <button type="button" wire:click="addRatio" class="bg-green-500 text-white px-4 py-2 rounded">Add Ratio</button>
            </div>

            @foreach($allocations as $index => $allocation)
            <div class="flex items-center space-x-4 mb-2 p-4 bg-gray-50 rounded" wire:key="allocation-{{ $index }}">
                <div class="flex-1">
                    <label class="block text-sm font-medium mb-1">Project {{ $index + 1 }}</label>
                    <select wire:model="allocations.{{ $index }}.project_id" class="border p-2 w-full">
                        <option value="">Select Project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->project_name }}</option>
                        @endforeach
                    </select>
                    @error('allocations.' . $index . '.project_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="flex-1">
                    <label class="block text-sm font-medium mb-1">Allocation Ratio (0.00 - 1.00)</label>
                    <input type="number" step="0.01" min="0" max="1"
                           wire:model="allocations.{{ $index }}.allocation_ratio"
                           class="border p-2 w-full" placeholder="0.50">
                    @error('allocations.' . $index . '.allocation_ratio') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                @if($index > 0)
                <button type="button" wire:click="removeRatio({{ $index }})"
                        class="bg-red-500 text-white px-3 py-2 rounded mt-6">×</button>
                @else
                <div class="w-10"></div> <!-- Spacer for first row -->
                @endif
            </div>
            @endforeach

            @error('allocations') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Total Ratio Display -->
        <div class="mb-6 p-4 bg-blue-50 rounded">
            <div class="flex justify-between items-center">
                <span class="text-lg font-semibold">Total Ratio:</span>
                <span class="text-xl font-bold {{ $this->totalRatio == 1.0 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($this->totalRatio * 100, 2) }}%</span>
            </div>
            @if($this->totalRatio != 1.0)
                <div class="text-red-500 text-sm mt-2">
                    @if($this->totalRatio > 1.0)
                        Total allocation exceeds 100%. Please reduce allocation ratios.
                    @else
                        Total allocation is less than 100%. Please increase allocation ratios.
                    @endif
                </div>
            @else
                <div class="text-green-500 text-sm mt-2">✓ Total allocation is exactly 100%</div>
            @endif
        </div>

        <div class="flex justify-end">
            <button type="submit" wire:loading.attr="disabled" wire:target="save"
                    class="bg-blue-500 text-white px-4 py-2 rounded mr-2 disabled:opacity-50 disabled:cursor-not-allowed">
                <span wire:loading.remove wire:target="save">Save Allocations</span>
                <span wire:loading wire:target="save">Saving...</span>
            </button>
            <a href="{{ route('salary-allocations.index') }}" class="bg-gray-300 px-4 py-2 rounded">Cancel</a>
        </div>
    </form>
</div>
