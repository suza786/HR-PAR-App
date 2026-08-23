<div>
    <h1 class="text-2xl font-bold mb-4">Edit Salary</h1>
    <form wire:submit.prevent="update">
        <table class="w-full bg-gray-50 p-6 rounded mb-4">
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
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded mr-2">Update</button>
            <a href="{{ route('salaries.index') }}" class="bg-gray-300 px-4 py-2 rounded">Cancel</a>
        </div>
    </form>
</div>
