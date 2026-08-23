<?php

namespace App\Livewire\Project;

use Livewire\Component;
use App\Models\Project;

class Index extends Component
{
    public $projects;

    public function mount()
    {
        $this->projects = Project::with('donor')->latest()->get();
    }

    public function render()
    {
        return view('livewire.project.index');
    }
}