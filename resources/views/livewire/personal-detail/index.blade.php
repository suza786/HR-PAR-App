<div>
    <h1 class="text-2xl font-bold mb-4">Personal Details</h1>
    @if (session()->has('success'))
        <div class="bg-green-100 text-green-800 p-2 rounded mb-2">{{ session('success') }}</div>
    @endif
    <a href="{{ route('personal-details.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded mb-4 inline-block">Add Personal Detail</a>
    <table class="min-w-full bg-white border border-gray-300" style="border-collapse: separate; border-spacing: 0 8px;">
        <thead class="bg-gray-300">
            <tr class="bg-blue-300">
                <th class="py-3 px-4 border border-gray-300">Employee</th>
                <th class="py-3 px-4 border border-gray-300">Father Name</th>
                <th class="py-3 px-4 border border-gray-300">Mother Name</th>
                <th class="py-3 px-4 border border-gray-300">Spouse Name</th>
                <th class="py-3 px-4 border border-gray-300">Personal Email</th>
                <th class="py-3 px-4 border border-gray-300">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($personalDetails as $detail)
            <tr>
                <td class="py-2 px-4 border border-gray-300">{{ $detail->employee ? $detail->employee->emp_id . ' - ' . $detail->employee->name : '' }}</td>
                <td class="py-2 px-4 border border-gray-300">{{ $detail->father_name }}</td>
                <td class="py-2 px-4 border border-gray-300">{{ $detail->mother_name }}</td>
                <td class="py-2 px-4 border border-gray-300">{{ $detail->spouse_name }}</td>
                <td class="py-2 px-4 border border-gray-300">{{ $detail->personal_email }}</td>
                <td class="py-2 px-4 border border-gray-300">
                    <a href="{{ route('personal-details.edit', $detail->id) }}" class="bg-yellow-500 text-white px-2 py-1 rounded">Edit</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
