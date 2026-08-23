<div>
    <h1 class="text-2xl font-bold mb-4">Employees</h1>
    <a href="{{ route('employees.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded mb-4 inline-block">Add Employee</a>
    <table class="min-w-full bg-white border border-gray-300" style="border-collapse: separate; border-spacing: 0 8px;">
        <thead class="bg-gray-300">
            <tr class="bg-blue-300">
                <th class="py-3 px-4 border border-gray-300">Emp ID</th>
                <th class="py-3 px-4 border border-gray-300">Name</th>
                <th class="py-3 px-4 border border-gray-300">Email</th>
                <th class="py-3 px-4 border border-gray-300">Department</th>
                <th class="py-3 px-4 border border-gray-300">Position</th>
                <th class="py-3 px-4 border border-gray-300">Mobile</th>
                <th class="py-3 px-4 border border-gray-300">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employees as $employee)
            <tr>
                <td class="py-2 px-4 border border-gray-300">{{ $employee->emp_id }}</td>
                <td class="py-2 px-4 border border-gray-300">{{ $employee->name }}</td>
                <td class="py-2 px-4 border border-gray-300">{{ $employee->email }}</td>
                <td class="py-2 px-4 border border-gray-300">{{ $employee->department ? $employee->department->name : '' }}</td>
                <td class="py-2 px-4 border border-gray-300">{{ $employee->position }}</td>
                <td class="py-2 px-4 border border-gray-300">{{ $employee->mobile }}</td>
                <td class="py-2 px-4 border border-gray-300">
                    <a href="{{ route('employees.edit', $employee->id) }}" class="bg-yellow-500 text-white px-2 py-1 rounded">Edit</a>
                    <button wire:click="$emit('deleteEmployee', {{ $employee->id }})" class="bg-red-500 text-white px-2 py-1 rounded">Delete</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
