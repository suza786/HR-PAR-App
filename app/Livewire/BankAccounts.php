<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\BankAccount;
use App\Models\Employee;

class BankAccounts extends Component
{
    public $bankAccounts;
    public $employees;
    public $showCreateModal = false;
    public $showEditModal = false;
    public $bank_account_id;
    public $employee_id, $ac_number, $bank_name, $ac_status = 1;

    protected $rules = [
        'employee_id' => 'required|exists:employees,id',
        'ac_number' => 'required|string',
        'bank_name' => 'required|string',
        'ac_status' => 'required|in:1,0',
    ];

    public function mount()
    {
        $this->bankAccounts = BankAccount::with('employee')->latest()->get();
        $this->employees = Employee::all();
    }

    public function render()
    {
        return view('livewire.bank-accounts');
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
    }

    public function openEditModal($id)
    {
        $account = BankAccount::findOrFail($id);
        $this->bank_account_id = $account->id;
        $this->employee_id = $account->employee_id;
        $this->ac_number = $account->ac_number;
        $this->bank_name = $account->bank_name;
        $this->ac_status = $account->ac_status;
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
    }

    public function resetForm()
    {
        $this->bank_account_id = null;
        $this->employee_id = '';
        $this->ac_number = '';
        $this->bank_name = '';
        $this->ac_status = 1;
    }

    public function save()
    {
        $this->validate();
        BankAccount::create([
            'employee_id' => $this->employee_id,
            'ac_number' => $this->ac_number,
            'bank_name' => $this->bank_name,
            'ac_status' => $this->ac_status,
        ]);
        $this->closeCreateModal();
        $this->bankAccounts = BankAccount::with('employee')->latest()->get();
        session()->flash('success', 'Bank account added successfully.');
    }

    public function update()
    {
        $this->validate();
        $account = BankAccount::findOrFail($this->bank_account_id);
        $account->update([
            'employee_id' => $this->employee_id,
            'ac_number' => $this->ac_number,
            'bank_name' => $this->bank_name,
            'ac_status' => $this->ac_status,
        ]);
        $this->closeEditModal();
        $this->bankAccounts = BankAccount::with('employee')->latest()->get();
        session()->flash('success', 'Bank account updated successfully.');
    }

    public function delete($id)
    {
        BankAccount::findOrFail($id)->delete();
        $this->bankAccounts = BankAccount::with('employee')->latest()->get();
        session()->flash('success', 'Bank account deleted successfully.');
    }
}
