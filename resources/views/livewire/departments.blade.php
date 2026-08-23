<div>
    <h1 class="text-2xl font-bold mb-4">Departments</h1>
    <button wire:click="openCreateModal" class="bg-blue-500 text-white px-4 py-2 rounded mb-4">Add Department</button>
    <table class="min-w-full bg-white border border-gray-300" style="border-collapse: separate; border-spacing: 0 8px;">
        <thead class="bg-gray-300">
            <tr class="bg-blue-300">
                <th class="py-3 px-4 border border-gray-300">ID</th>
                <th class="py-3 px-4 border border-gray-300">Name</th>
                <th class="py-3 px-4 border border-gray-300">Description</th>
                <th class="py-3 px-4 border border-gray-300">Parent Dept</th>
                <th class="py-3 px-4 border border-gray-300">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($departments as $department)
            <tr>
                <td class="py-2 px-4 border border-gray-300">{{ $department->id }}</td>
                <td class="py-2 px-4 border border-gray-300">{{ $department->name }}</td>
                <td class="py-2 px-4 border border-gray-300">{{ $department->description }}</td>
                <td class="py-2 px-4 border border-gray-300">{{ $department->parentDepartment ? $department->parentDepartment->name : "N/A" }}</td>
                <td class="py-2 px-4 border border-gray-300">
                    <button wire:click="openEditModal({{ $department->id }})" class="bg-yellow-500 text-white px-2 py-1 rounded">Edit</button>
                    <button wire:click="openDeleteModal({{ $department->id }})" class="bg-red-500 text-white px-2 py-1 rounded">Delete</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Create Modal -->
    @if($showCreateModal)
    <div class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50">
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-lg font-bold mb-4">Add Department</h2>
            <input type="text" wire:model="name" placeholder="Name" class="border p-2 mb-2 w-full">
            @error('name') <span class="text-red-500">{{ $message }}</span> @enderror
            <input type="text" wire:model="description" placeholder="Description" class="border p-2 mb-2 w-full">
            @error('description') <span class="text-red-500">{{ $message }}</span> @enderror
            <select wire:model="parent_dept_id" class="border p-2 mb-2 w-full">
                <option value="0">Select Parent Department</option>
                @foreach($parentOptions as $id => $parent)
                    <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                @endforeach
            </select>
            @error('parent_dept_id') <span class="text-red-500">{{ $message }}</span> @enderror
            <div class="flex justify-end">
                <button wire:click="createDepartment" class="bg-blue-500 text-white px-4 py-2 rounded mr-2">Save</button>
                <button wire:click="$set('showCreateModal', false)" class="bg-gray-300 px-4 py-2 rounded">Cancel</button>
            </div>
        </div>
    </div>
    @endif

    <!-- Edit Modal -->
    @if($showEditModal)
    <div class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50">
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-lg font-bold mb-4">Edit Department</h2>
            <input type="text" wire:model="name" placeholder="Name" class="border p-2 mb-2 w-full">
            @error('name') <span class="text-red-500">{{ $message }}</span> @enderror
            <input type="text" wire:model="description" placeholder="Description" class="border p-2 mb-2 w-full">
            @error('description') <span class="text-red-500">{{ $message }}</span> @enderror
            <select wire:model="parent_dept_id" class="border p-2 mb-2 w-full">
                <option value="">Select Parent Department</option>
                @foreach($parentOptions as $id => $parent)
                    <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                @endforeach
            </select>
            @error('parent_dept_id') <span class="text-red-500">{{ $message }}</span> @enderror
            <div class="flex justify-end">
                <button wire:click="updateDepartment" class="bg-yellow-500 text-white px-4 py-2 rounded mr-2">Update</button>
                <button wire:click="$set('showEditModal', false)" class="bg-gray-300 px-4 py-2 rounded">Cancel</button>
            </div>
        </div>
    </div>
    @endif

    <!-- Delete Modal -->
    @if($showDeleteModal)
    <div class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50">
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-lg font-bold mb-4">Delete Department</h2>
            <p>Are you sure you want to delete this department?</p>
            <div class="flex justify-end mt-4">
                <button wire:click="deleteDepartment" class="bg-red-500 text-white px-4 py-2 rounded mr-2">Delete</button>
                <button wire:click="$set('showDeleteModal', false)" class="bg-gray-300 px-4 py-2 rounded">Cancel</button>
            </div>
        </div>
    </div>
    @endif
    

    <!-- Modal toggle -->

    <div
    data-dialog-backdrop="default-modal"
    data-dialog-backdrop-close="true"
    class="pointer-events-none fixed inset-0 z-[999] grid h-screen w-screen place-items-center bg-black bg-opacity-60 opacity-0 backdrop-blur-sm transition-opacity duration-300"
    >
    <div
        data-dialog="modal"
        class="relative m-4 p-4 w-2/5 min-w-[40%] max-w-[40%] rounded-lg bg-white shadow-sm"
    >
        <div class="flex shrink-0 items-center pb-4 text-xl font-medium text-slate-800">
        Its a simple Modal
        </div>
        <div class="relative border-t border-slate-200 py-4 leading-normal text-slate-600 font-light">
        The key to more success is to have a lot of pillows. Put it this
        way, it took me twenty five years to get these plants, twenty five
        years of blood sweat and tears, and I&apos;m never giving up,
        I&apos;m just getting started. I&apos;m up to something. Fan luv.
        </div>
        <div class="flex shrink-0 flex-wrap items-center pt-4 justify-end">
        <button data-dialog-close="true" class="rounded-md border border-transparent py-2 px-4 text-center text-sm transition-all text-slate-600 hover:bg-slate-100 focus:bg-slate-100 active:bg-slate-100 disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none" type="button">
            Cancel
        </button>
        <button data-dialog-close="true" class="rounded-md bg-green-600 py-2 px-4 border border-transparent text-center text-sm text-white transition-all shadow-md hover:shadow-lg focus:bg-green-700 focus:shadow-none active:bg-green-700 hover:bg-green-700 active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none ml-2" type="button">
            Confirm
        </button>
        </div>
    </div>
    </div>

</div>
