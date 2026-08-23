<div>
    <h1 class="text-2xl font-bold mb-4">Projects</h1>
    @if (session()->has('success'))
        <div class="bg-green-100 text-green-800 p-2 rounded mb-2">{{ session('success') }}</div>
    @endif
    <a href="{{ route('projects.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded mb-4 inline-block">Add Project</a>
    <table class="min-w-full bg-white border border-gray-300" style="border-collapse: separate; border-spacing: 0 8px;">
        <thead class="bg-gray-300">
            <tr class="bg-blue-300">
                <th class="py-3 px-4 border border-gray-300">Donor Name</th>
                <th class="py-3 px-4 border border-gray-300">Project Code</th>
                <th class="py-3 px-4 border border-gray-300">Project Name</th>
                <th class="py-3 px-4 border border-gray-300">Short Name</th>
                <th class="py-3 px-4 border border-gray-300">Start Date</th>
                <th class="py-3 px-4 border border-gray-300">End Date</th>
                <th class="py-3 px-4 border border-gray-300">Total Budget</th>
                <th class="py-3 px-4 border border-gray-300">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($projects as $project)
            <tr>
                <td class="py-2 px-4 border border-gray-300">{{ $project->donor ? $project->donor->name : '' }}</td>
                <td class="py-2 px-4 border border-gray-300">{{ $project->project_code }}</td>
                <td class="py-2 px-4 border border-gray-300">{{ $project->project_name }}</td>
                <td class="py-2 px-4 border border-gray-300">{{ $project->short_name }}</td>
                <td class="py-2 px-4 border border-gray-300">{{ $project->start_date }}</td>
                <td class="py-2 px-4 border border-gray-300">{{ $project->end_date }}</td>
                <td class="py-2 px-4 border border-gray-300">{{ $project->total_budget }}</td>
                <td class="py-2 px-4 border border-gray-300">
                    <a href="{{ route('projects.edit', $project->id) }}" class="bg-yellow-500 text-white px-2 py-1 rounded">Edit</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>