<div>
    <div class="mb-6">
        <h1 class="text-3xl font-bold">{{ __('Edit Activity Allocation') }}</h1>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-6">
        <div class="bg-white border border-gray-200 rounded-lg p-6 shadow">
            <h2 class="text-2xl font-bold mb-4">{{ __('Activity Allocation Details') }}</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-2">{{ __('Employee') }}</label>
                    <input type="text" value="{{ optional($activityAllocation->monthlyActivity->employee)->emp_id ?? 'N/A' }} - {{ optional($activityAllocation->monthlyActivity->employee)->name ?? '' }}" class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-100" readonly>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-2">{{ __('Month') }}</label>
                    <input type="text" value="{{ optional($activityAllocation->monthlyActivity->parCalendar)->month ?? 'N/A' }}" class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-100" readonly>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-2">{{ __('Week') }}</label>
                    <input type="text" value="{{ 'Week-' . optional($activityAllocation->monthlyActivity->parCalendar)->week }}" class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-100" readonly>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg p-6 shadow">
            <h2 class="text-2xl font-bold mb-4">{{ __('Activity Allocation') }}</h2>

            <form wire:submit.prevent="save">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-2">{{ __('Project') }}</label>
                        <select wire:model="activityRow.project_id" class="w-full border border-gray-300 rounded px-3 py-2">
                            @foreach($salaryAllocations as $allocation)
                                <option value="{{ $allocation->project_id }}">{{ $allocation->project->project_name ?? 'N/A' }}</option>
                            @endforeach
                        </select>
                        @error('activityRow.project_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-2">{{ __('Activity') }}</label>
                        <select wire:model="activityRow.activity_id" class="w-full border border-gray-300 rounded px-3 py-2">
                            <option value="">{{ __('Select Activity') }}</option>
                            @foreach($activities as $activity)
                                <option value="{{ $activity->id }}">{{ $activity->name }}</option>
                            @endforeach
                        </select>
                        @error('activityRow.activity_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-2">{{ __('Working Hours') }}</label>
                        <input type="number" step="0.01" wire:model="activityRow.working_hrs" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="0.00">
                        @error('activityRow.working_hrs') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <a href="{{ route('activity-allocations.index') }}" class="bg-gray-300 text-gray-800 px-6 py-2 rounded hover:bg-gray-400 font-semibold" wire:navigate>
                        {{ __('Cancel') }}
                    </a>
                    <button type="submit" class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600 font-semibold">
                        {{ __('Save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
