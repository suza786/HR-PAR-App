<div>
    <h1 class="text-2xl font-bold mb-4">Edit Salary Allocation</h1>

    <form wire:submit.prevent="update" class="bg-white p-6 rounded shadow">
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Employee</label>
            <!-- <input type="text" value="{{ optional($employees->firstWhere('emp_id', $emp_id))->name ?? $emp_id }}" class="border p-2 w-full bg-gray-100" readonly> -->
            <input type="text" value="{{ $emp_id}}" class="border p-2 w-full bg-gray-100" readonly>
            @error('emp_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Project</label>
            <select wire:model="project_id" class="border p-2 w-full">
                <option value="">Select Project</option>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}" {{ $project->id == $project_id ? 'selected' : '' }}>{{ $project->project_name }}</option>
                @endforeach
            </select>
            @error('project_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Allocation Ratio (0.00 - 1.00)</label>
            <input type="number" step="0.01" min="0" max="1" wire:model="allocation_ratio"
                   class="border p-2 w-full" placeholder="0.50">
            @error('allocation_ratio') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
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

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Year</label>
            <input type="number" wire:model="curr_year" class="border p-2 w-full" readonly>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded mr-2">Update</button>
            <a href="{{ route('salary-allocations.index') }}" class="bg-gray-300 px-4 py-2 rounded">Cancel</a>
        </div>
    </form>
</div>