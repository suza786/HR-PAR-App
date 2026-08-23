<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Department;

class Departments extends Component
{
    public $departments;
    public $name, $description, $parent_dept_id, $department_id;
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:255',
        'parent_dept_id' => 'nullable|integer|exists:departments,id',
    ];

    public function mount()
    {
        $this->loadDepartments();
    }

    public function loadDepartments()
    {
        $this->departments = Department::all();
    }

    public function render()
    {
        return view('livewire.departments', [
            // 'parentOptions' => Department::pluck('id', 'id')
            'parentOptions' => Department::all()
        ]);
    }

    public function openCreateModal()
    {
        $this->resetFields();
        $this->showCreateModal = true;
    }

    public function createDepartment()
    {
        $this->validate();
        Department::create([
            'name' => $this->name,
            'description' => $this->description,
            'parent_dept_id' => $this->parent_dept_id,
        ]);
        $this->showCreateModal = false;
        $this->loadDepartments();
        $this->resetFields();
    }

    public function openEditModal($id)
    {
        $department = Department::findOrFail($id);
        $this->department_id = $department->id;
        $this->name = $department->name;
        $this->description = $department->description;
        $this->parent_dept_id = $department->parent_dept_id;
        $this->showEditModal = true;
    }

    public function updateDepartment()
    {
        $this->validate();
        $department = Department::findOrFail($this->department_id);
        $department->update([
            'name' => $this->name,
            'description' => $this->description,
            'parent_dept_id' => $this->parent_dept_id == "" ? 0 : $this->parent_dept_id,
        ]);
        $this->showEditModal = false;
        $this->loadDepartments();
        $this->resetFields();
    }

    public function openDeleteModal($id)
    {
        $this->department_id = $id;
        $this->showDeleteModal = true;
    }

    public function deleteDepartment()
    {
        Department::destroy($this->department_id);
        $this->showDeleteModal = false;
        $this->loadDepartments();
        $this->resetFields();
    }

    private function resetFields()
    {
        $this->name = '';
        $this->description = '';
        $this->parent_dept_id = null;
        $this->department_id = null;
    }
}
