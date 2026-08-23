<?php

use App\Livewire\ActivityAllocation\Edit;
use App\Models\ActivityAllocation;
use App\Models\MonthlyActivity;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ActivityAllocationAuthorizationUser extends Authenticatable
{
    public $id = 1;

    public $user_type = 'HR';
}

function authorizeActivityAllocationEdit(Edit $component, ActivityAllocation $allocation): void
{
    (fn () => $this->authorizeEditing($allocation))->call($component);
}

it('allows HR users to edit pending activity allocations', function () {
    $this->actingAs(new ActivityAllocationAuthorizationUser);

    $allocation = new ActivityAllocation;
    $allocation->setRelation('monthlyActivity', new MonthlyActivity(['status' => 'pending']));

    authorizeActivityAllocationEdit(new Edit, $allocation);

    expect(true)->toBeTrue();
});

it('blocks HR users from editing non-pending activity allocations', function () {
    $this->actingAs(new ActivityAllocationAuthorizationUser);

    $allocation = new ActivityAllocation;
    $allocation->setRelation('monthlyActivity', new MonthlyActivity(['status' => 'requested']));

    expect(fn () => authorizeActivityAllocationEdit(new Edit, $allocation))
        ->toThrow(HttpException::class);
});

it('blocks non-HR users from editing pending activity allocations', function () {
    $user = new ActivityAllocationAuthorizationUser;
    $user->user_type = 'Employee';
    $this->actingAs($user);

    $allocation = new ActivityAllocation;
    $allocation->setRelation('monthlyActivity', new MonthlyActivity(['status' => 'pending']));

    expect(fn () => authorizeActivityAllocationEdit(new Edit, $allocation))
        ->toThrow(HttpException::class);
});
