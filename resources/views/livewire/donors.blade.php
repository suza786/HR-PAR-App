<div>
    <h1 class="text-2xl font-bold mb-4">Donors</h1>
    <button wire:click="openCreateModal" class="bg-blue-500 text-white px-4 py-2 rounded mb-4">Add Donor</button>
    <table class="min-w-full bg-white border border-gray-300" style="border-collapse: separate; border-spacing: 0 8px;">
        <thead class="bg-gray-300">
            <tr class="bg-blue-300">
                <th class="py-3 px-4 border border-gray-300">ID</th>
                <th class="py-3 px-4 border border-gray-300">Name</th>
                <th class="py-3 px-4 border border-gray-300">Country</th>
                <th class="py-3 px-4 border border-gray-300">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($donors as $donor)
            <tr>
                <td class="py-2 px-4 border border-gray-300">{{ $donor->id }}</td>
                <td class="py-2 px-4 border border-gray-300">{{ $donor->name }}</td>
                <td class="py-2 px-4 border border-gray-300">{{ $donor->country }}</td>
                <td class="py-2 px-4 border border-gray-300">
                    <button wire:click="openEditModal({{ $donor->id }})" class="bg-yellow-500 text-white px-2 py-1 rounded">Edit</button>
                    <button wire:click="openDeleteModal({{ $donor->id }})" class="bg-red-500 text-white px-2 py-1 rounded">Delete</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Create Modal -->
    @if($showCreateModal)
    <div class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50">
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-lg font-bold mb-4">Add Donor</h2>
            <input type="text" wire:model="name" placeholder="Name" class="border p-2 mb-2 w-full">
            @error('name') <span class="text-red-500">{{ $message }}</span> @enderror
            <input type="text" wire:model="country" placeholder="Country" class="border p-2 mb-2 w-full">
            @error('country') <span class="text-red-500">{{ $message }}</span> @enderror
            <div class="flex justify-end">
                <button wire:click="createDonor" class="bg-blue-500 text-white px-4 py-2 rounded mr-2">Save</button>
                <button wire:click="$set('showCreateModal', false)" class="bg-gray-300 px-4 py-2 rounded">Cancel</button>
            </div>
        </div>
    </div>
    @endif

    <!-- Edit Modal -->
    @if($showEditModal)
    <div class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50">
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-lg font-bold mb-4">Edit Donor</h2>
            <input type="text" wire:model="name" placeholder="Name" class="border p-2 mb-2 w-full">
            @error('name') <span class="text-red-500">{{ $message }}</span> @enderror
            <input type="text" wire:model="country" placeholder="Country" class="border p-2 mb-2 w-full">
            @error('country') <span class="text-red-500">{{ $message }}</span> @enderror
            <div class="flex justify-end">
                <button wire:click="updateDonor" class="bg-yellow-500 text-white px-4 py-2 rounded mr-2">Update</button>
                <button wire:click="$set('showEditModal', false)" class="bg-gray-300 px-4 py-2 rounded">Cancel</button>
            </div>
        </div>
    </div>
    @endif

    <!-- Delete Modal -->
    @if($showDeleteModal)
    <div class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50">
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-lg font-bold mb-4">Delete Donor</h2>
            <p>Are you sure you want to delete this donor?</p>
            <div class="flex justify-end mt-4">
                <button wire:click="deleteDonor" class="bg-red-500 text-white px-4 py-2 rounded mr-2">Delete</button>
                <button wire:click="$set('showDeleteModal', false)" class="bg-gray-300 px-4 py-2 rounded">Cancel</button>
            </div>
        </div>
    </div>
    @endif
</div>
