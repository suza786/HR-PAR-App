<div>
    <h1 class="text-2xl font-bold mb-4">Add Project</h1>
    <form wire:submit.prevent="save" class="bg-white p-6 rounded shadow">
        <table class="w-full bg-gray-50 p-4 rounded mb-4">
            <tr>
                <th class="text-right align-top w-1/4 pr-2"><label class="block mb-1 font-semibold">Donor</label></th>
                <td class="w-1/4 pr-4">
                    <select wire:model="donor_id" class="border p-2 mb-2 w-full">
                        <option value="">Select Donor</option>
                        @foreach($donors as $donor)
                            <option value="{{ $donor->id }}">{{ $donor->name }}</option>
                        @endforeach
                    </select>
                    @error('donor_id') <span class="text-red-500">{{ $message }}</span> @enderror
                </td>
                <th class="text-left align-top w-1/4 pr-2"><label class="block mb-1 font-semibold">Project Code</label></th>
                <td class="w-1/4"><input type="text" wire:model="project_code" class="border p-2 mb-2 w-full" placeholder="Project Code">@error('project_code') <span class="text-red-500">{{ $message }}</span> @enderror</td>
            </tr>
            <tr>
                <th class="text-left align-top pr-2"><label class="block mb-1 font-semibold">Project Name</label></th>
                <td class="pr-4"><input type="text" wire:model="project_name" class="border p-2 mb-2 w-full" placeholder="Project Name">@error('project_name') <span class="text-red-500">{{ $message }}</span> @enderror</td>
                <th class="text-left align-top pr-2"><label class="block mb-1 font-semibold">Short Name</label></th>
                <td><input type="text" wire:model="short_name" class="border p-2 mb-2 w-full" placeholder="Short Name">@error('short_name') <span class="text-red-500">{{ $message }}</span> @enderror</td>
            </tr>
            <tr>
                <th class="text-left align-top pr-2"><label class="block mb-1 font-semibold">Start Date</label></th>
                <td class="pr-4"><input type="date" wire:model="start_date" class="border p-2 mb-2 w-full" placeholder="Start Date">@error('start_date') <span class="text-red-500">{{ $message }}</span> @enderror</td>
                <th class="text-left align-top pr-2"><label class="block mb-1 font-semibold">End Date</label></th>
                <td><input type="date" wire:model="end_date" class="border p-2 mb-2 w-full" placeholder="End Date">@error('end_date') <span class="text-red-500">{{ $message }}</span> @enderror</td>
            </tr>
            <tr>
                <th class="text-left align-top pr-2"><label class="block mb-1 font-semibold">Currency</label></th>
                <td class="pr-4">
                    <select wire:model="currency" class="border p-2 mb-2 w-full">
                        <option value="">Select Currency</option>
                        <option value="EURO">€ EURO</option>
                        <option value="USD">$ USD</option>
                        <option value="BDT">৳ BDT</option>
                    </select>
                    @error('currency') <span class="text-red-500">{{ $message }}</span> @enderror
                </td>
                <th class="text-left align-top pr-2"><label class="block mb-1 font-semibold">Total Budget</label></th>
                <td><input type="number" wire:model="total_budget" class="border p-2 mb-2 w-full" placeholder="Total Budget">@error('total_budget') <span class="text-red-500">{{ $message }}</span> @enderror</td>
            </tr>
            <tr>
                <th class="text-left align-top pr-2"><label class="block mb-1 font-semibold">Total Revised Budget</label></th>
                <td class="pr-4"><input type="number" wire:model="total_revised_budget" class="border p-2 mb-2 w-full" placeholder="Total Revised Budget">@error('total_revised_budget') <span class="text-red-500">{{ $message }}</span> @enderror</td>
                <th class="text-left align-top pr-2"><label class="block mb-1 font-semibold">Status</label></th>
                <td class="w-1/4 pr-4">
                    <select wire:model="status" class="border p-2 mb-2 w-full" required>
                        <option value="">Status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                    @error('status') <span class="text-red-500">{{ $message }}</span> @enderror</td>
            </tr>
        </table>
        <div class="flex justify-end">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded mr-2">Save</button>
            <a href="{{ route('projects.index') }}" class="bg-gray-300 px-4 py-2 rounded">Cancel</a>
        </div>
    </form>
</div>