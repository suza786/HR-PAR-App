<div>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold">{{ __('Edit Monthly Activity Allocations') }}</h1>
        <a href="{{ route('activity-allocations.index') }}" class="bg-gray-300 text-gray-800 px-5 py-2 rounded hover:bg-gray-400 font-semibold" wire:navigate>
            {{ __('Back') }}
        </a>
    </div>

    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow mb-6">
        <h2 class="text-2xl font-bold mb-4">{{ __('Allocation Period') }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-2">{{ __('Employee') }}</label>
                <input type="text" value="{{ $selectedEmployeeCode }} - {{ $selectedEmployeeName }}" class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-100" readonly>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-2">{{ __('Year') }}</label>
                <input type="text" value="{{ $selectedYear }}" class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-100" readonly>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-2">{{ __('Month') }}</label>
                <input type="text" value="{{ $selectedMonth }}" class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-100" readonly>
            </div>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow">
        <h2 class="text-2xl font-bold mb-4">{{ __('Activity Allocations') }}</h2>

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

        <form wire:submit.prevent="save">
            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="w-full text-sm border-collapse">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border border-gray-300 px-4 py-2 text-left font-semibold">{{ __('Project') }}</th>
                            <th class="border border-gray-300 px-4 py-2 text-left font-semibold">{{ __('Activity') }}</th>
                            @foreach($weeks as $week)
                                <th class="border border-gray-300 px-4 py-2 text-center font-semibold">{{ __('Week') }}-{{ $week['week'] }} {{ __('hrs') }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($activityAllocations as $rowKey => $row)
                            <tr wire:key="edit-activity-allocation-{{ $rowKey }}" class="border-b hover:bg-gray-50">
                                <td class="border border-gray-300 px-4 py-2">
                                    <select wire:model="activityAllocations.{{ $rowKey }}.project_id" class="w-full border border-gray-300 rounded px-2 py-1">
                                        @foreach($salaryAllocations as $allocation)
                                            <option value="{{ $allocation->project_id }}">{{ $allocation->project->project_name ?? 'N/A' }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="border border-gray-300 px-4 py-2">
                                    <select wire:model="activityAllocations.{{ $rowKey }}.activity_id" class="w-full border border-gray-300 rounded px-2 py-1">
                                        <option value="">{{ __('Select Activity') }}</option>
                                        @foreach($activities as $activity)
                                            <option value="{{ $activity->id }}">{{ $activity->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                @foreach($weeks as $week)
                                    <td class="border border-gray-300 px-4 py-2">
                                        <input type="number" step="0.01" min="0"
                                               wire:model.blur="activityAllocations.{{ $rowKey }}.weekly_hrs.{{ $week['id'] }}"
                                               class="w-full border border-gray-300 rounded px-2 py-1 text-center">
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-100 font-semibold">
                            <td colspan="2" class="border border-gray-300 px-4 py-2">{{ __('Allocated total') }}</td>
                            @foreach($weeks as $week)
                                <td class="border border-gray-300 px-4 py-2 text-center {{ abs((float) ($weeklyTotals[$week['id']] ?? 0) - (float) $weeklyTotalsExpected[$week['id']]) < 0.001 ? 'text-green-700' : 'text-red-700' }}">
                                    {{ number_format((float) ($weeklyTotals[$week['id']] ?? 0), 2) }} / {{ number_format((float) $weeklyTotalsExpected[$week['id']], 2) }}
                                </td>
                            @endforeach
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <a href="{{ route('activity-allocations.index') }}" class="bg-gray-300 text-gray-800 px-6 py-2 rounded hover:bg-gray-400 font-semibold" wire:navigate>
                    {{ __('Cancel') }}
                </a>
                <button type="submit" wire:loading.attr="disabled" wire:target="save"
                        class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600 font-semibold disabled:opacity-50">
                    <span wire:loading.remove wire:target="save">{{ __('Update') }}</span>
                    <span wire:loading wire:target="save">{{ __('Updating...') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>
