<table class="w-full bg-gray-50 p-6 rounded">
    <tr>
        <th class="text-left align-top w-1/4 pr-2"><label class="block mb-1 font-semibold">Emp ID</label></th>
        <td class="w-1/4 pr-4"><input type="text" wire:model="emp_id" class="border p-2 mb-2 w-full" placeholder="Emp ID">@error('emp_id') <span class="text-red-500">{{ $message }}</span> @enderror</td>
        <th class="text-left align-top w-1/4 pr-2"><label class="block mb-1 font-semibold">Name</label></th>
        <td class="w-1/4"><input type="text" wire:model="name" class="border p-2 mb-2 w-full" placeholder="Name">@error('name') <span class="text-red-500">{{ $message }}</span> @enderror</td>
    </tr>
    <tr>
        <th class="text-left align-top pr-2"><label class="block mb-1 font-semibold">Email</label></th>
        <td class="pr-4"><input type="email" wire:model="email" class="border p-2 mb-2 w-full" placeholder="Email">@error('email') <span class="text-red-500">{{ $message }}</span> @enderror</td>
        <th class="text-left align-top pr-2"><label class="block mb-1 font-semibold">Position</label></th>
        <td><input type="text" wire:model="position" class="border p-2 mb-2 w-full" placeholder="Position">@error('position') <span class="text-red-500">{{ $message }}</span> @enderror</td>
    </tr>
    <tr>
        <th class="text-left align-top pr-2"><label class="block mb-1 font-semibold">Mobile</label></th>
        <td class="pr-4"><input type="text" wire:model="mobile" class="border p-2 mb-2 w-full" placeholder="Mobile">@error('mobile') <span class="text-red-500">{{ $message }}</span> @enderror</td>
        <th class="text-left align-top pr-2"><label class="block mb-1 font-semibold">Gender</label></th>
        <td>
            <select wire:model="gender" class="border p-2 mb-2 w-full">
                <option value="">Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>
            @error('gender') <span class="text-red-500">{{ $message }}</span> @enderror
        </td>
    </tr>
    <tr>
        <th class="text-left align-top pr-2"><label class="block mb-1 font-semibold">Birth Date</label></th>
        <td class="pr-4"><input type="date" wire:model="birth_date" class="border p-2 mb-2 w-full" placeholder="Birth Date">@error('birth_date') <span class="text-red-500">{{ $message }}</span> @enderror</td>
        <th class="text-left align-top pr-2"><label class="block mb-1 font-semibold">Join Date</label></th>
        <td><input type="date" wire:model="join_date" class="border p-2 mb-2 w-full" placeholder="Join Date">@error('join_date') <span class="text-red-500">{{ $message }}</span> @enderror</td>
    </tr>
    <tr>
        <th class="text-left align-top pr-2"><label class="block mb-1 font-semibold">Separation Date</label></th>
        <td class="pr-4"><input type="date" wire:model="separation_date" class="border p-2 mb-2 w-full" placeholder="Separation Date">@error('separation_date') <span class="text-red-500">{{ $message }}</span> @enderror</td>
        <th class="text-left align-top pr-2"><label class="block mb-1 font-semibold">User Status</label></th>
        <td>
            <select wire:model="user_status" class="border p-2 mb-2 w-full">
                <option value="">Select Status</option>
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>
            @error('user_status') <span class="text-red-500">{{ $message }}</span> @enderror
        </td>
    </tr>
    <tr>
        <th class="text-left align-top pr-2"><label class="block mb-1 font-semibold">Emp Type</label></th>
        <td class="pr-4">
            <select wire:model="emp_type_id" class="border p-2 mb-2 w-full">
                <option value="">Select Emp Type</option>
                @foreach($empTypes as $type)
                    <option value="{{ $type->id }}">{{ $type->emp_type }}</option>
                @endforeach
            </select>
            @error('emp_type_id') <span class="text-red-500">{{ $message }}</span> @enderror
        </td>
        <th class="text-left align-top pr-2"><label class="block mb-1 font-semibold">Supervisor</label></th>
        <td>
            <select wire:model="supervisor_id" class="border p-2 mb-2 w-full">
                <option value="">Select Supervisor</option>
                @foreach($supervisors as $sup)
                    <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                @endforeach
            </select>
            @error('supervisor_id') <span class="text-red-500">{{ $message }}</span> @enderror
        </td>
    </tr>
    <tr>
        <th class="text-left align-top pr-2"><label class="block mb-1 font-semibold">Location</label></th>
        <td class="pr-4">
            <select wire:model="location_id" class="border p-2 mb-2 w-full">
                <option value="">Select Location</option>
                @foreach($locations as $loc)
                    <option value="{{ $loc->id }}">{{ $loc->location_name }}</option>
                @endforeach
            </select>
            @error('location_id') <span class="text-red-500">{{ $message }}</span> @enderror
        </td>
        <th class="text-left align-top pr-2"><label class="block mb-1 font-semibold">Department</label></th>
        <td>
            <select wire:model="department_id" class="border p-2 mb-2 w-full">
                <option value="">Select Department</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                @endforeach
            </select>
            @error('department_id') <span class="text-red-500">{{ $message }}</span> @enderror
        </td>
    </tr>
    <tr>
        <th class="text-left align-top pr-2"><label class="block mb-1 font-semibold">Photo</label></th>
        <td class="pr-4"><input type="file" wire:model="photo" class="border p-2 mb-2 w-full">@error('photo') <span class="text-red-500">{{ $message }}</span> @enderror</td>
        <th class="text-left align-top pr-2"><label class="block mb-1 font-semibold">Roles</label></th>
        <td>
            <select wire:model="role_ids" multiple class="border p-2 mb-2 w-full">
                <option value="">Select Roles</option>
                @foreach($rolesList as $role)
                    <option value="{{ $role->id }}">{{ $role->role_name }}</option>
                @endforeach
            </select>
            @error('role_ids') <span class="text-red-500">{{ $message }}</span> @enderror
        </td>
    </tr>
    <tr>
        <th class="text-left align-top pr-2"><label class="block mb-1 font-semibold">Password</label></th>
        <td class="pr-4"><input type="password" wire:model="password" class="border p-2 mb-2 w-full" placeholder="Password">@error('password') <span class="text-red-500">{{ $message }}</span> @enderror</td>
        <th class="text-left align-top pr-2"><label class="block mb-1 font-semibold">Confirm Password</label></th>
        <td><input type="password" wire:model="password_confirmation" class="border p-2 mb-2 w-full" placeholder="Confirm Password"></td>
    </tr>
</table>
