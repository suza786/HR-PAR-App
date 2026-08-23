<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Role;
use Illuminate\Support\Str;

class Roles extends Component
{
    public $roles;
    public $role_name, $slug, $note, $role_id;
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;

    protected $rules = [
        'role_name' => 'required|string|max:255',
        'note' => 'nullable|string|max:255',
    ];

    public function mount()
    {
        $this->loadRoles();
    }

    public function loadRoles()
    {
        $this->roles = Role::all();
    }

    public function render()
    {
        return view('livewire.roles');
    }

    public function openCreateModal()
    {
        $this->resetFields();
        $this->showCreateModal = true;
    }

    public function createRole()
    {
        $this->validate();
        Role::create([
            'role_name' => $this->role_name,
            'slug' => Str::slug($this->role_name),
            'note' => $this->note,
        ]);
        $this->showCreateModal = false;
        $this->loadRoles();
        $this->resetFields();
    }

    public function openEditModal($id)
    {
        $role = Role::findOrFail($id);
        $this->role_id = $role->id;
        $this->role_name = $role->role_name;
        $this->slug = $role->slug;
        $this->note = $role->note;
        $this->showEditModal = true;
    }

    public function updateRole()
    {
        $this->validate();
        $role = Role::findOrFail($this->role_id);
        $role->update([
            'role_name' => $this->role_name,
            'slug' => Str::slug($this->role_name),
            'note' => $this->note,
        ]);
        $this->showEditModal = false;
        $this->loadRoles();
        $this->resetFields();
    }

    public function openDeleteModal($id)
    {
        $this->role_id = $id;
        $this->showDeleteModal = true;
    }

    public function deleteRole()
    {
        Role::destroy($this->role_id);
        $this->showDeleteModal = false;
        $this->loadRoles();
        $this->resetFields();
    }

    private function resetFields()
    {
        $this->role_name = '';
        $this->slug = '';
        $this->note = '';
        $this->role_id = null;
    }
}
