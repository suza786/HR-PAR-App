<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\EmpType;

class EmpTypes extends Component
{
    public $empTypes;
    public $emp_type, $type_description, $emp_type_id;
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;

    protected $rules = [
        'emp_type' => 'required|string|max:255',
        'type_description' => 'nullable|string|max:255',
    ];

    public function mount()
    {
        $this->loadEmpTypes();
    }

    public function loadEmpTypes()
    {
        $this->empTypes = EmpType::all();
    }

    public function render()
    {
        return view('livewire.emp-types');
    }

    public function openCreateModal()
    {
        $this->resetFields();
        $this->showCreateModal = true;
    }

    public function createEmpType()
    {
        $this->validate();
        EmpType::create([
            'emp_type' => $this->emp_type,
            'type_description' => $this->type_description,
        ]);
        $this->showCreateModal = false;
        $this->loadEmpTypes();
        $this->resetFields();
    }

    public function openEditModal($id)
    {
        $empType = EmpType::findOrFail($id);
        $this->emp_type_id = $empType->id;
        $this->emp_type = $empType->emp_type;
        $this->type_description = $empType->type_description;
        $this->showEditModal = true;
    }

    public function updateEmpType()
    {
        $this->validate();
        $empType = EmpType::findOrFail($this->emp_type_id);
        $empType->update([
            'emp_type' => $this->emp_type,
            'type_description' => $this->type_description,
        ]);
        $this->showEditModal = false;
        $this->loadEmpTypes();
        $this->resetFields();
    }

    public function openDeleteModal($id)
    {
        $this->emp_type_id = $id;
        $this->showDeleteModal = true;
    }

    public function deleteEmpType()
    {
        EmpType::destroy($this->emp_type_id);
        $this->showDeleteModal = false;
        $this->loadEmpTypes();
        $this->resetFields();
    }

    private function resetFields()
    {
        $this->emp_type = '';
        $this->type_description = '';
        $this->emp_type_id = null;
    }
}
