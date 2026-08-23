<?php

namespace App\Livewire\PersonalDetail;

use Livewire\Component;
use App\Models\PersonalDetail;

class Index extends Component
{
    public $personalDetails;

    public function mount()
    {
        $this->personalDetails = PersonalDetail::with('employee')->latest()->get();
    }

    public function render()
    {
        return view('livewire.personal-detail.index');
    }
}
