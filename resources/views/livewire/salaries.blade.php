<div>
    <h1 class="text-2xl font-bold mb-4">Salaries</h1>
    @if (session()->has('success'))
        <div class="bg-green-100 text-green-800 p-2 rounded mb-2">{{ session('success') }}</div>
    @endif
    <button wire:click="openCreateModal" class="bg-blue-500 text-white px-4 py-2 rounded mb-4">Add Salary</button>

    <!-- Create/Edit Modal -->
    @if($showCreateModal)
    <div class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 z-50">
        <div class="bg-white p-6 rounded shadow w-full max-w-xl mx-auto min-w-0">
            <h2 class="text-lg font-bold mb-4">{{ $isEdit ? 'Edit Salary' : 'Add Salary' }}</h2>
            <form wire:submit.prevent="{{ $isEdit ? 'update' : 'save' }}">
                <table class="w-full bg-gray-50 p-4 rounded mb-4">
                    <tr>
                        <th class="text-right align-top w-1/4 pr-2"><label class="block mb-1 font-semibold">Employee</label></th>
                        <td class="w-1/4 pr-4">
                            <select wire:model="employee_id" class="border p-2 mb-2 w-full">
                                <option value="">Select Employee</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->emp_id }} - {{ $emp->name }}</option>
                                @endforeach
                            </select>
                            @error('employee_id') <span class="text-red-500">{{ $message }}</span> @enderror
                        </td>
                        <th class="text-left align-top w-1/4 pr-2"><label class="block mb-1 font-semibold">Basic</label></th>
                        <td class="w-1/4"><input type="number" wire:model="basic" class="border p-2 mb-2 w-full" placeholder="Basic">@error('basic') <span class="text-red-500">{{ $message }}</span> @enderror</td>
                    </tr>
                    <tr>
                        <th class="text-left align-top pr-2"><label class="block mb-1 font-semibold">Perquisites</label></th>
                        <td class="pr-4"><input type="number" wire:model="perquisites" class="border p-2 mb-2 w-full" placeholder="Perquisites">@error('perquisites') <span class="text-red-500">{{ $message }}</span> @enderror</td>
                        <th class="text-left align-top pr-2"><label class="block mb-1 font-semibold">PF Organization</label></th>
                        <td><input type="number" wire:model="pf_organization" class="border p-2 mb-2 w-full" placeholder="PF Organization">@error('pf_organization') <span class="text-red-500">{{ $message }}</span> @enderror</td>
                    </tr>
                    <tr>
                        <th class="text-left align-top pr-2"><label class="block mb-1 font-semibold">Gratuity</label></th>
                        <td class="pr-4"><input type="number" wire:model="gratuity" class="border p-2 mb-2 w-full" placeholder="Gratuity">@error('gratuity') <span class="text-red-500">{{ $message }}</span> @enderror</td>
                        <th class="text-left align-top pr-2"><label class="block mb-1 font-semibold">Bonus</label></th>
                        <td><input type="number" wire:model="bonus" class="border p-2 mb-2 w-full" placeholder="Bonus">@error('bonus') <span class="text-red-500">{{ $message }}</span> @enderror</td>
                    </tr>
                    <tr>
                        <th class="text-left align-top pr-2"><label class="block mb-1 font-semibold">Income Tax</label></th>
                        <td class="pr-4"><input type="number" wire:model="income_tax" class="border p-2 mb-2 w-full" placeholder="Income Tax">@error('income_tax') <span class="text-red-500">{{ $message }}</span> @enderror</td>
                        <th class="text-left align-top pr-2"><label class="block mb-1 font-semibold">Effective Date</label></th>
                        <td><input type="date" wire:model="efective_date" class="border p-2 mb-2 w-full" placeholder="Effective Date">@error('efective_date') <span class="text-red-500">{{ $message }}</span> @enderror</td>
                    </tr>
                    <tr>
                        <th class="text-left align-top pr-2"><label class="block mb-1 font-semibold">Salary Year</label></th>
                        <td class="pr-4"><input type="text" wire:model="salary_year" class="border p-2 mb-2 w-full bg-gray-100" readonly></td>
                        <td colspan="2"></td>
                    </tr>
                </table>
                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded mr-2">{{ $isEdit ? 'Update' : 'Save' }}</button>
                    <button type="button" wire:click="closeCreateModal" class="bg-gray-300 px-4 py-2 rounded">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    @endif
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
                    <button wire:click="edit({{ $salary->id }})" class="bg-yellow-500 text-white px-2 py-1 rounded">Edit</button>
                    <button wire:click="delete({{ $salary->id }})" class="bg-red-500 text-white px-2 py-1 rounded">Delete</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
