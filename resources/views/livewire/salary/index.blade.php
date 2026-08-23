<div>
    <h1 class="text-2xl font-bold mb-4">Salaries</h1>
    @if (session()->has('success'))
        <div class="bg-green-100 text-green-800 p-2 rounded mb-2">{{ session('success') }}</div>
    @endif
    <a href="{{ route('salaries.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded mb-4 inline-block">Add Salary</a>
    <table class="min-w-full bg-white border border-gray-300" style="border-collapse: separate; border-spacing: 0 8px;">
        <thead class="bg-gray-300">
            <tr class="bg-blue-300">
                <th class="py-3 px-4 border border-gray-300">Employee</th>
                <th class="py-3 px-4 border border-gray-300">Basic</th>
                <th class="py-3 px-4 border border-gray-300">Perquisites</th>
                <th class="py-3 px-4 border border-gray-300">PF Org</th>
                <th class="py-3 px-4 border border-gray-300">Gratuity</th>
                <th class="py-3 px-4 border border-gray-300">Bonus</th>
                <th class="py-3 px-4 border border-gray-300">Income Tax</th>
                <th class="py-3 px-4 border border-gray-300">Effective Date</th>
                <th class="py-3 px-4 border border-gray-300">Salary Year</th>
                <th class="py-3 px-4 border border-gray-300">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($salaries as $salary)
            <tr>
                <td class="py-2 px-4 border border-gray-300">{{ $salary->employee ? $salary->employee->emp_id . ' - ' . $salary->employee->name : '' }}</td>
                <td class="py-2 px-4 border border-gray-300">{{ $salary->basic }}</td>
                <td class="py-2 px-4 border border-gray-300">{{ $salary->perquisites }}</td>
                <td class="py-2 px-4 border border-gray-300">{{ $salary->pf_organization }}</td>
                <td class="py-2 px-4 border border-gray-300">{{ $salary->gratuity }}</td>
                <td class="py-2 px-4 border border-gray-300">{{ $salary->bonus }}</td>
                <td class="py-2 px-4 border border-gray-300">{{ $salary->income_tax }}</td>
                <td class="py-2 px-4 border border-gray-300">{{ $salary->efective_date }}</td>
                <td class="py-2 px-4 border border-gray-300">{{ $salary->salary_year }}</td>
                <td class="py-2 px-4 border border-gray-300">
                    <a href="{{ route('salaries.edit', $salary->id) }}" class="bg-yellow-500 text-white px-2 py-1 rounded">Edit</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
