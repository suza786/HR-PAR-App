<?php

use App\Livewire\SalaryAllocation\Create;

it('validates employees on the employee model connection', function () {
    $component = new Create;
    $component->currentYear = 2026;

    $rules = (fn () => $this->rules())->call($component);

    expect((string) $rules['emp_id'][1])
        ->toBe('exists:sna_lms.employees,emp_id');
});
