<?php

namespace App\Livewire\Project;

use Livewire\Component;
use App\Models\Project;

class Edit extends Component
{
    public $project_id, $donor_id, $project_code, $project_name, $short_name;
    public $start_date, $end_date, $currency, $total_budget, $total_revised_budget, $status;
    public $donors = [];

    protected function rules()
    {
        return [
            'donor_id' => 'required|exists:donors,id',
            'project_code' => 'required|unique:projects,project_code,' . $this->project_id,
            'project_name' => 'required|unique:projects,project_name,' . $this->project_id,
            'short_name' => 'required|max:4|unique:projects,short_name,' . $this->project_id,
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'currency' => 'required',
            'total_budget' => 'nullable|numeric',
            'total_revised_budget' => 'nullable|numeric',
            'status' => 'required|boolean',
        ];
    }

    public function mount($id)
    {
        $project = Project::findOrFail($id);
        $this->project_id = $project->id;
        $this->donor_id = $project->donor_id;
        $this->project_code = $project->project_code;
        $this->project_name = $project->project_name;
        $this->short_name = $project->short_name;
        $this->start_date = $project->start_date;
        $this->end_date = $project->end_date;
        $this->currency = $project->currency;
        $this->total_budget = $project->total_budget;
        $this->total_revised_budget = $project->total_revised_budget;
        $this->status = $project->status;
        $this->donors = \App\Models\Donor::all();
    }

    public function update()
    {
        $this->validate();
        $project = Project::findOrFail($this->project_id);
        $project->update([
            'donor_id' => $this->donor_id,
            'project_code' => $this->project_code,
            'project_name' => $this->project_name,
            'short_name' => $this->short_name,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'currency' => $this->currency,
            'total_budget' => $this->total_budget,
            'total_revised_budget' => $this->total_revised_budget,
            'status' => $this->status,
        ]);
        session()->flash('success', 'Project updated successfully.');
        return redirect()->route('projects.index');
    }

    public function render()
    {
        return view('livewire.project.edit');
    }
}