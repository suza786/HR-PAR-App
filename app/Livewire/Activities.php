<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Activity;

class Activities extends Component
{
    public $activities;
    public $name, $description, $activity_id;
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:255',
    ];

    public function mount()
    {
        $this->loadActivities();
    }

    public function loadActivities()
    {
        $this->activities = Activity::all();
    }

    public function render()
    {
        return view('livewire.activities');
    }

    public function openCreateModal()
    {
        $this->resetFields();
        $this->showCreateModal = true;
    }

    public function createActivity()
    {
        $this->validate();
        Activity::create([
            'name' => $this->name,
            'description' => $this->description,
        ]);
        $this->showCreateModal = false;
        $this->loadActivities();
        $this->resetFields();
    }

    public function openEditModal($id)
    {
        $activity = Activity::findOrFail($id);
        $this->activity_id = $activity->id;
        $this->name = $activity->name;
        $this->description = $activity->description;
        $this->showEditModal = true;
    }

    public function updateActivity()
    {
        $this->validate();
        $activity = Activity::findOrFail($this->activity_id);
        $activity->update([
            'name' => $this->name,
            'description' => $this->description,
        ]);
        $this->showEditModal = false;
        $this->loadActivities();
        $this->resetFields();
    }

    public function openDeleteModal($id)
    {
        $this->activity_id = $id;
        $this->showDeleteModal = true;
    }

    public function deleteActivity()
    {
        Activity::destroy($this->activity_id);
        $this->showDeleteModal = false;
        $this->loadActivities();
        $this->resetFields();
    }

    private function resetFields()
    {
        $this->name = '';
        $this->description = '';
        $this->activity_id = null;
    }
}
