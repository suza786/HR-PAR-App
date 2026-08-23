<div>
    <h1 class="text-2xl font-bold mb-4">Edit Education/Training</h1>
    <form wire:submit.prevent="update">
        <table class="w-full bg-gray-50 p-6 rounded mb-4">
            <tr>
                <th class="text-right align-top w-1/4 pr-2"><label class="block mb-1 font-semibold">Employee</label></th>
                <td class="w-1/4 pr-4">
                    <select wire:model="employee_id" class="border p-2 mb-2 w-full">
                        <option value="">Select Employee</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->emp_id }} - {{ $emp->name }}</option>
                        @endforeach
                    </select>
                    @error('employee_id') <span class="text-red-500">{{ $message }}</span> @enderror
                </td>
                <th class="text-left align-top w-1/4 pr-2"><label class="block mb-1 font-semibold">Degree</label></th>
                <td class="w-1/4"><input type="text" wire:model="degree" class="border p-2 mb-2 w-full" placeholder="Degree">@error('degree') <span class="text-red-500">{{ $message }}</span> @enderror</td>
            </tr>
            <tr>
                <th class="text-left align-top pr-2"><label class="block mb-1 font-semibold">Board/University/Institute</label></th>
                <td class="pr-4"><input type="text" wire:model="board_university_institute" class="border p-2 mb-2 w-full" placeholder="Board/University/Institute">@error('board_university_institute') <span class="text-red-500">{{ $message }}</span> @enderror</td>
                <th class="text-left align-top pr-2"><label class="block mb-1 font-semibold">Session Duration</label></th>
                <td><input type="text" wire:model="session_duration" class="border p-2 mb-2 w-full" placeholder="Session Duration">@error('session_duration') <span class="text-red-500">{{ $message }}</span> @enderror</td>
            </tr>
            <tr>
                <th class="text-left align-top pr-2"><label class="block mb-1 font-semibold">Result/Marks</label></th>
                <td class="pr-4"><input type="text" wire:model="result_marks" class="border p-2 mb-2 w-full" placeholder="Result/Marks">@error('result_marks') <span class="text-red-500">{{ $message }}</span> @enderror</td>
                <th class="text-left align-top pr-2"><label class="block mb-1 font-semibold">Year</label></th>
                <td><input type="number" wire:model="year" class="border p-2 mb-2 w-full" placeholder="Year">@error('year') <span class="text-red-500">{{ $message }}</span> @enderror</td>
            </tr>
        </table>
        <div class="flex justify-end">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded mr-2">Update</button>
            <a href="{{ route('education-trainings.index') }}" class="bg-gray-300 px-4 py-2 rounded">Cancel</a>
        </div>
    </form>
</div>
