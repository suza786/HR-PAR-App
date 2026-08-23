<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Location;

class Locations extends Component
{
    public $locations;
    public $location_id;
    public $location_name;
    public $short_name;
    public $address;
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;

    protected $rules = [
        'location_name' => 'required|string|max:255',
        'short_name' => 'nullable|string|max:255',
        'address' => 'nullable|string|max:255',
    ];

    public function mount()
    {
        $this->loadLocations();
    }

    public function loadLocations()
    {
        $this->locations = Location::all();
    }

    public function render()
    {
        return view('livewire.locations');
    }

    public function openCreateModal()
    {
        $this->resetFields();
        $this->showCreateModal = true;
    }

    public function createLocation()
    {
        $this->validate();

        Location::create([
            'location_name' => $this->location_name,
            'short_name' => $this->short_name,
            'address' => $this->address,
        ]);

        $this->showCreateModal = false;
        $this->loadLocations();
        $this->resetFields();
    }

    public function openEditModal($id)
    {
        $location = Location::findOrFail($id);

        $this->location_id = $location->id;
        $this->location_name = $location->location_name;
        $this->short_name = $location->short_name;
        $this->address = $location->address;
        $this->showEditModal = true;
    }

    public function updateLocation()
    {
        $this->validate();

        $location = Location::findOrFail($this->location_id);
        $location->update([
            'location_name' => $this->location_name,
            'short_name' => $this->short_name,
            'address' => $this->address,
        ]);

        $this->showEditModal = false;
        $this->loadLocations();
        $this->resetFields();
    }

    public function openDeleteModal($id)
    {
        $this->location_id = $id;
        $this->showDeleteModal = true;
    }

    public function deleteLocation()
    {
        Location::destroy($this->location_id);
        $this->showDeleteModal = false;
        $this->loadLocations();
        $this->resetFields();
    }

    private function resetFields()
    {
        $this->location_name = '';
        $this->short_name = '';
        $this->address = '';
        $this->location_id = null;
    }
}
