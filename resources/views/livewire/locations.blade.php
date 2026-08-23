<div>
    <h1 class="text-2xl font-bold mb-4">Locations</h1>
    <button wire:click="openCreateModal" class="bg-blue-500 text-white px-4 py-2 rounded mb-4">Add Location</button>

    <table class="min-w-full bg-white border border-gray-300" style="border-collapse: separate; border-spacing: 0 8px;">
        <thead class="bg-gray-300">
            <tr class="bg-blue-300">
                <th class="py-3 px-4 border border-gray-300">ID</th>
                <th class="py-3 px-4 border border-gray-300">Location Name</th>
                <th class="py-3 px-4 border border-gray-300">Short Name</th>
                <th class="py-3 px-4 border border-gray-300">Address</th>
                <th class="py-3 px-4 border border-gray-300">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($locations as $location)
                <tr>
                    <td class="py-2 px-4 border border-gray-300">{{ $location->id }}</td>
                    <td class="py-2 px-4 border border-gray-300">{{ $location->location_name }}</td>
                    <td class="py-2 px-4 border border-gray-300">{{ $location->short_name }}</td>
                    <td class="py-2 px-4 border border-gray-300">{{ $location->address }}</td>
                    <td class="py-2 px-4 border border-gray-300">
                        <button wire:click="openEditModal({{ $location->id }})" class="bg-yellow-500 text-white px-2 py-1 rounded mr-2">Edit</button>
                        <button wire:click="openDeleteModal({{ $location->id }})" class="bg-red-500 text-white px-2 py-1 rounded">Delete</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="py-4 text-center text-sm text-gray-500">No locations found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($showCreateModal)
        <div class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50">
            <div class="bg-white p-6 rounded shadow w-full max-w-lg">
                <h2 class="text-lg font-bold mb-4">Add Location</h2>

                <div class="space-y-4">
                    <div>
                        <input type="text" wire:model="location_name" placeholder="Location Name" class="border p-2 mb-2 w-full" />
                        @error('location_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <input type="text" wire:model="short_name" placeholder="Short Name" class="border p-2 mb-2 w-full" />
                        @error('short_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <textarea wire:model="address" placeholder="Address" class="border p-2 mb-2 w-full"></textarea>
                        @error('address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-4">
                    <button wire:click="$set('showCreateModal', false)" class="bg-gray-300 px-4 py-2 rounded">Cancel</button>
                    <button wire:click="createLocation" class="bg-blue-500 text-white px-4 py-2 rounded">Save</button>
                </div>
            </div>
        </div>
    @endif

    @if($showEditModal)
        <div class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50">
            <div class="bg-white p-6 rounded shadow w-full max-w-lg">
                <h2 class="text-lg font-bold mb-4">Edit Location</h2>

                <div class="space-y-4">
                    <div>
                        <input type="text" wire:model="location_name" placeholder="Location Name" class="border p-2 mb-2 w-full" />
                        @error('location_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <input type="text" wire:model="short_name" placeholder="Short Name" class="border p-2 mb-2 w-full" />
                        @error('short_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <textarea wire:model="address" placeholder="Address" class="border p-2 mb-2 w-full"></textarea>
                        @error('address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-4">
                    <button wire:click="$set('showEditModal', false)" class="bg-gray-300 px-4 py-2 rounded">Cancel</button>
                    <button wire:click="updateLocation" class="bg-yellow-500 text-white px-4 py-2 rounded">Update</button>
                </div>
            </div>
        </div>
    @endif

    @if($showDeleteModal)
        <div class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50">
            <div class="bg-white p-6 rounded shadow w-full max-w-md">
                <h2 class="text-lg font-bold mb-4">Delete Location</h2>
                <p>Are you sure you want to delete this location?</p>

                <div class="flex justify-end gap-2 mt-4">
                    <button wire:click="$set('showDeleteModal', false)" class="bg-gray-300 px-4 py-2 rounded">Cancel</button>
                    <button wire:click="deleteLocation" class="bg-red-500 text-white px-4 py-2 rounded">Delete</button>
                </div>
            </div>
        </div>
    @endif
</div>
