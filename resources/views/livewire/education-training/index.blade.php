<div>
    <h1 class="text-2xl font-bold mb-4">Education/Trainings</h1>
    @if (session()->has('success'))
        <div class="bg-green-100 text-green-800 p-2 rounded mb-2">{{ session('success') }}</div>
    @endif
    <a href="{{ route('education-trainings.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded mb-4 inline-block">Add Education/Training</a>
    <table class="min-w-full bg-white border border-gray-300" style="border-collapse: separate; border-spacing: 0 8px;">
        <thead class="bg-gray-300">
            <tr class="bg-blue-300">
                <th class="py-3 px-4 border border-gray-300">Employee</th>
                <th class="py-3 px-4 border border-gray-300">Degree</th>
                <th class="py-3 px-4 border border-gray-300">Board/University/Institute</th>
                <th class="py-3 px-4 border border-gray-300">Session Duration</th>
                <th class="py-3 px-4 border border-gray-300">Result/Marks</th>
                <th class="py-3 px-4 border border-gray-300">Year</th>
                <th class="py-3 px-4 border border-gray-300">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($educationTrainings as $et)
            <tr>
                <td class="py-2 px-4 border border-gray-300">{{ $et->employee ? $et->employee->emp_id . ' - ' . $et->employee->name : '' }}</td>
                <td class="py-2 px-4 border border-gray-300">{{ $et->degree }}</td>
                <td class="py-2 px-4 border border-gray-300">{{ $et->board_university_institute }}</td>
                <td class="py-2 px-4 border border-gray-300">{{ $et->session_duration }}</td>
                <td class="py-2 px-4 border border-gray-300">{{ $et->result_marks }}</td>
                <td class="py-2 px-4 border border-gray-300">{{ $et->year }}</td>
                <td class="py-2 px-4 border border-gray-300">
                    <a href="{{ route('education-trainings.edit', $et->id) }}" class="bg-yellow-500 text-white px-2 py-1 rounded">Edit</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
