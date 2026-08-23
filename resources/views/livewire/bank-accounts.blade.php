<div>
    <h1 class="text-2xl font-bold mb-4">Bank Accounts</h1>
    @if (session()->has('success'))
        <div class="bg-green-100 text-green-800 p-2 rounded mb-2">{{ session('success') }}</div>
    @endif
    <button wire:click="openCreateModal" class="bg-blue-500 text-white px-4 py-2 rounded mb-4">Add Bank Account</button>
    <table class="min-w-full bg-white border border-gray-300" style="border-collapse: separate; border-spacing: 0 8px;">
        <thead class="bg-gray-300">
            <tr class="bg-blue-300">
                <th class="py-3 px-4 border border-gray-300">Employee</th>
                <th class="py-3 px-4 border border-gray-300">Account Number</th>
                <th class="py-3 px-4 border border-gray-300">Bank Name</th>
                <th class="py-3 px-4 border border-gray-300">Status</th>
                <th class="py-3 px-4 border border-gray-300">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bankAccounts as $account)
            <tr>
                <td class="py-2 px-4 border border-gray-300">{{ $account->employee ? $account->employee->emp_id . ' - ' . $account->employee->name : '' }}</td>
                <td class="py-2 px-4 border border-gray-300">{{ $account->ac_number }}</td>
                <td class="py-2 px-4 border border-gray-300">{{ $account->bank_name }}</td>
                <td class="py-2 px-4 border border-gray-300">{{ $account->ac_status ? 'Active' : 'Inactive' }}</td>
                <td class="py-2 px-4 border border-gray-300">
                    <button wire:click="openEditModal({{ $account->id }})" class="bg-yellow-500 text-white px-2 py-1 rounded">Edit</button>
                    <button wire:click="delete({{ $account->id }})" class="bg-red-500 text-white px-2 py-1 rounded">Delete</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Create Modal -->
    @if($showCreateModal)
    <div class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 z-50">
        <div class="bg-white p-6 rounded shadow max-w-xl mx-auto">
            <h2 class="text-lg font-bold mb-4">Add Bank Account</h2>
            <form wire:submit.prevent="save">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-1 font-semibold">Employee</label>
                        <select wire:model="employee_id" class="border p-2 mb-2 w-full">
                            <option value="">Select Employee</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->emp_id }} - {{ $emp->name }}</option>
                            @endforeach
                        </select>
                        @error('employee_id') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block mb-1 font-semibold">Account Number</label>
                        <input type="text" wire:model="ac_number" class="border p-2 mb-2 w-full" placeholder="Account Number">
                        @error('ac_number') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block mb-1 font-semibold">Bank Name</label>
                        <input type="text" wire:model="bank_name" class="border p-2 mb-2 w-full" placeholder="Bank Name">
                        @error('bank_name') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block mb-1 font-semibold">Branch</label>
                        <input type="text" wire:model="branch" class="border p-2 mb-2 w-full" placeholder="Branch">
                        @error('branch') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block mb-1 font-semibold">Swift Code</label>
                        <input type="text" wire:model="swift_code" class="border p-2 mb-2 w-full" placeholder="Swift Code">
                        @error('swift_code') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block mb-1 font-semibold">Routing Number</label>
                        <input type="text" wire:model="routing_num" class="border p-2 mb-2 w-full" placeholder="Routing Number">
                        @error('routing_num') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block mb-1 font-semibold">Account Status</label>
                        <select wire:model="ac_status" class="border p-2 mb-2 w-full">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                        @error('ac_status') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="flex justify-end mt-4">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded mr-2">Save</button>
                    <button type="button" wire:click="closeCreateModal" class="bg-gray-300 px-4 py-2 rounded">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Edit Modal -->
    @if($showEditModal)
    <div class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 z-50">
        <div class="bg-white p-6 rounded shadow max-w-xl mx-auto">
            <h2 class="text-lg font-bold mb-4">Edit Bank Account</h2>
            <form wire:submit.prevent="update">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-1 font-semibold">Employee</label>
                        <select wire:model="employee_id" class="border p-2 mb-2 w-full">
                            <option value="">Select Employee</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->emp_id }} - {{ $emp->name }}</option>
                            @endforeach
                        </select>
                        @error('employee_id') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block mb-1 font-semibold">Account Number</label>
                        <input type="text" wire:model="ac_number" class="border p-2 mb-2 w-full" placeholder="Account Number">
                        @error('ac_number') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block mb-1 font-semibold">Bank Name</label>
                        <input type="text" wire:model="bank_name" class="border p-2 mb-2 w-full" placeholder="Bank Name">
                        @error('bank_name') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block mb-1 font-semibold">Branch</label>
                        <input type="text" wire:model="branch" class="border p-2 mb-2 w-full" placeholder="Branch">
                        @error('branch') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block mb-1 font-semibold">Swift Code</label>
                        <input type="text" wire:model="swift_code" class="border p-2 mb-2 w-full" placeholder="Swift Code">
                        @error('swift_code') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block mb-1 font-semibold">Routing Number</label>
                        <input type="text" wire:model="routing_num" class="border p-2 mb-2 w-full" placeholder="Routing Number">
                        @error('routing_num') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block mb-1 font-semibold">Account Status</label>
                        <select wire:model="ac_status" class="border p-2 mb-2 w-full">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                        @error('ac_status') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="flex justify-end mt-4">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded mr-2">Update</button>
                    <button type="button" wire:click="closeEditModal" class="bg-gray-300 px-4 py-2 rounded">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
