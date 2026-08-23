<div>
    <h1 class="text-2xl font-bold mb-4">Add Personal Detail</h1>
    <form wire:submit.prevent="save">
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
                <th class="text-left align-top w-1/4 pr-2"><label class="block mb-1 font-semibold">Father Name</label></th>
                <td class="w-1/4"><input type="text" wire:model="father_name" class="border p-2 mb-2 w-full" placeholder="Father Name">@error('father_name') <span class="text-red-500">{{ $message }}</span> @enderror</td>
            </tr>
            <tr>
                <th class="text-left align-top pr-2"><label class="block mb-1 font-semibold">Mother Name</label></th>
                <td class="pr-4"><input type="text" wire:model="mother_name" class="border p-2 mb-2 w-full" placeholder="Mother Name">@error('mother_name') <span class="text-red-500">{{ $message }}</span> @enderror</td>
                <th class="text-left align-top pr-2"><label class="block mb-1 font-semibold">Spouse Name</label></th>
                <td><input type="text" wire:model="spouse_name" class="border p-2 mb-2 w-full" placeholder="Spouse Name">@error('spouse_name') <span class="text-red-500">{{ $message }}</span> @enderror</td>
            </tr>
            <tr>
                <th class="text-left align-top pr-2"><label class="block mb-1 font-semibold">Spouse Mobile</label></th>
                <td class="pr-4"><input type="text" wire:model="spouse_mobile" class="border p-2 mb-2 w-full" placeholder="Spouse Mobile">@error('spouse_mobile') <span class="text-red-500">{{ $message }}</span> @enderror</td>
                <th class="text-left align-top pr-2"><label class="block mb-1 font-semibold">Personal Email</label></th>
                <td><input type="email" wire:model="personal_email" class="border p-2 mb-2 w-full" placeholder="Personal Email">@error('personal_email') <span class="text-red-500">{{ $message }}</span> @enderror</td>
            </tr>
            <tr>
                <th class="text-left align-top pr-2"><label class="block mb-1 font-semibold">Present Address</label></th>
                <td class="pr-4"><input type="text" wire:model="present_address" class="border p-2 mb-2 w-full" placeholder="Present Address">@error('present_address') <span class="text-red-500">{{ $message }}</span> @enderror</td>
                <th class="text-left align-top pr-2"><label class="block mb-1 font-semibold">Permanent Address</label></th>
                <td><input type="text" wire:model="permanent_address" class="border p-2 mb-2 w-full" placeholder="Permanent Address">@error('permanent_address') <span class="text-red-500">{{ $message }}</span> @enderror</td>
            </tr>
            <tr>
                <th class="text-left align-top pr-2"><label class="block mb-1 font-semibold">National ID</label></th>
                <td class="pr-4"><input type="text" wire:model="national_id" class="border p-2 mb-2 w-full" placeholder="National ID">@error('national_id') <span class="text-red-500">{{ $message }}</span> @enderror</td>
                <th class="text-left align-top pr-2"><label class="block mb-1 font-semibold">TIN ID</label></th>
                <td><input type="text" wire:model="tin_id" class="border p-2 mb-2 w-full" placeholder="TIN ID">@error('tin_id') <span class="text-red-500">{{ $message }}</span> @enderror</td>
            </tr>
            <tr>
                <th class="text-left align-top pr-2"><label class="block mb-1 font-semibold">Passport Num</label></th>
                <td class="pr-4"><input type="text" wire:model="passport_num" class="border p-2 mb-2 w-full" placeholder="Passport Num">@error('passport_num') <span class="text-red-500">{{ $message }}</span> @enderror</td>
                <th class="text-left align-top pr-2"><label class="block mb-1 font-semibold">Blood Group</label></th>
                <td>
                    <select wire:model="blood_group" class="border p-2 mb-2 w-full">
                        <option value="">Select Blood Group</option>
                        <option value="A+">A+</option>
                        <option value="B+">B+</option>
                        <option value="O+">O+</option>
                        <option value="AB+">AB+</option>
                        <option value="A-">A-</option>
                        <option value="B-">B-</option>
                        <option value="O-">O-</option>
                        <option value="AB-">AB-</option>
                    </select>
                    @error('blood_group') <span class="text-red-500">{{ $message }}</span> @enderror
                </td>
            </tr>
            <tr>
                <th class="text-left align-top pr-2"><label class="block mb-1 font-semibold">Religion</label></th>
                <td class="pr-4">
                    <select wire:model="religion" class="border p-2 mb-2 w-full">
                        <option value="">Select Religion</option>
                        <option value="Islam">Islam</option>
                        <option value="Hinduism">Hinduism</option>
                        <option value="Christianity">Christianity</option>
                        <option value="Buddhism">Buddhism</option>
                    </select>
                    @error('religion') <span class="text-red-500">{{ $message }}</span> @enderror
                </td>
                <td colspan="2"></td>
            </tr>
        </table>
        <div class="flex justify-end">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded mr-2">Save</button>
            <a href="{{ route('personal-details.index') }}" class="bg-gray-300 px-4 py-2 rounded">Cancel</a>
        </div>
    </form>
</div>
