<div>
    <h1 class="text-2xl font-bold mb-4">Employee Types</h1>
    <button wire:click="openCreateModal" class="bg-blue-500 text-white px-4 py-2 rounded mb-4">Add Employee Type</button>
    <table class="min-w-full bg-white border border-gray-300" style="border-collapse: separate; border-spacing: 0 8px;">
        <thead class="bg-gray-300">
            <tr class="bg-blue-300">
                <th class="py-3 px-4 border border-gray-300">ID</th>
                <th class="py-3 px-4 border border-gray-300">Emp Type</th>
                <th class="py-3 px-4 border border-gray-300">Description</th>
                <th class="py-3 px-4 border border-gray-300">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($empTypes as $empType)
            <tr>
                <td class="py-2 px-4 border border-gray-300">{{ $empType->id }}</td>
                <td class="py-2 px-4 border border-gray-300">{{ $empType->emp_type }}</td>
                <td class="py-2 px-4 border border-gray-300">{{ $empType->type_description }}</td>
                <td class="py-2 px-4 border border-gray-300">
                    <button wire:click="openEditModal({{ $empType->id }})" class="bg-yellow-500 text-white px-2 py-1 rounded">Edit</button>
                    <button wire:click="openDeleteModal({{ $empType->id }})" class="bg-red-500 text-white px-2 py-1 rounded">Delete</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Create Modal -->
    @if($showCreateModal)
    <div class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50">
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-lg font-bold mb-4">Add Employee Type</h2>
            <input type="text" wire:model="emp_type" placeholder="Emp Type" class="border p-2 mb-2 w-full">
            @error('emp_type') <span class="text-red-500">{{ $message }}</span> @enderror
            <input type="text" wire:model="type_description" placeholder="Description" class="border p-2 mb-2 w-full">
            @error('type_description') <span class="text-red-500">{{ $message }}</span> @enderror
            <div class="flex justify-end">
                <button wire:click="createEmpType" class="bg-blue-500 text-white px-4 py-2 rounded mr-2">Save</button>
                <button wire:click="$set('showCreateModal', false)" class="bg-gray-300 px-4 py-2 rounded">Cancel</button>
            </div>
        </div>
    </div>
    @endif

    <!-- Edit Modal -->
    @if($showEditModal)
    <div class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50">
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-lg font-bold mb-4">Edit Employee Type</h2>
            <input type="text" wire:model="emp_type" placeholder="Emp Type" class="border p-2 mb-2 w-full">
            @error('emp_type') <span class="text-red-500">{{ $message }}</span> @enderror
            <input type="text" wire:model="type_description" placeholder="Description" class="border p-2 mb-2 w-full">
            @error('type_description') <span class="text-red-500">{{ $message }}</span> @enderror
            <div class="flex justify-end">
                <button wire:click="updateEmpType" class="bg-yellow-500 text-white px-4 py-2 rounded mr-2">Update</button>
                <button wire:click="$set('showEditModal', false)" class="bg-gray-300 px-4 py-2 rounded">Cancel</button>
            </div>
        </div>
    </div>
    @endif

    <!-- Delete Modal -->
    @if($showDeleteModal)
    <div class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50">
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-lg font-bold mb-4">Delete Employee Type</h2>
            <p>Are you sure you want to delete this employee type?</p>
            <div class="flex justify-end mt-4">
                <button wire:click="deleteEmpType" class="bg-red-500 text-white px-4 py-2 rounded mr-2">Delete</button>
                <button wire:click="$set('showDeleteModal', false)" class="bg-gray-300 px-4 py-2 rounded">Cancel</button>
            </div>
        </div>
    </div>
    @endif
</div>
