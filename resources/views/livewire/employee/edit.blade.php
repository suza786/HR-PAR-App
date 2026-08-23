<div>
    <h1 class="text-2xl font-bold mb-4">Edit Employee</h1>
    <form wire:submit.prevent="submit" enctype="multipart/form-data">
        @include('livewire.employee.form-fields')
        <div class="flex justify-end mt-4">
            <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded mr-2">Update</button>
            <a href="{{ route('employees.index') }}" class="bg-gray-300 px-4 py-2 rounded">Cancel</a>
        </div>
    </form>
</div>
