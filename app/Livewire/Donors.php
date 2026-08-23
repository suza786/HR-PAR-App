<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Donor;

class Donors extends Component
{
    public $donors;
    public $name, $country, $donor_id;
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'country' => 'required|string|max:255',
    ];

    public function mount()
    {
        $this->loadDonors();
    }

    public function loadDonors()
    {
        $this->donors = Donor::all();
    }

    public function render()
    {
        return view('livewire.donors');
    }

    public function openCreateModal()
    {
        $this->resetFields();
        $this->showCreateModal = true;
    }

    public function createDonor()
    {       
        $this->validate();
        Donor::create([
            'name' => $this->name,
            'country' => $this->country,
        ]);
        $this->showCreateModal = false;
        $this->loadDonors();
        $this->resetFields();
    }

    public function openEditModal($id)
    {
        $donor = Donor::findOrFail($id);
        $this->donor_id = $donor->id;
        $this->name = $donor->name;
        $this->country = $donor->country;
        $this->showEditModal = true;
    }

    public function updateDonor()
    {
        $this->validate();
        $donor = Donor::findOrFail($this->donor_id);
        $donor->update([
            'name' => $this->name,
            'country' => $this->country,
        ]);
        $this->showEditModal = false;
        $this->loadDonors();
        $this->resetFields();
    }

    public function openDeleteModal($id)
    {
        $this->donor_id = $id;
        $this->showDeleteModal = true;
    }

    public function deleteDonor()
    {
        Donor::destroy($this->donor_id);
        $this->showDeleteModal = false;
        $this->loadDonors();
        $this->resetFields();
    }

    private function resetFields()
    {
        $this->name = '';
        $this->country = '';
        $this->donor_id = null;
    }
}
