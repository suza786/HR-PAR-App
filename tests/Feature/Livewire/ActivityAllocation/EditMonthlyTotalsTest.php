<?php

use App\Livewire\ActivityAllocation\Edit;

function activityAllocationWeeklyTotalsMatch(Edit $component): bool
{
    return (fn (): bool => $this->weeklyTotalsMatch())->call($component);
}

it('rejects a monthly allocation update when a weekly total does not match', function () {
    $component = new Edit;
    $component->weeks = [
        ['id' => 10, 'week' => 1],
        ['id' => 11, 'week' => 2],
    ];
    $component->weeklyTotalsExpected = [10 => 40, 11 => 32];
    $component->activityAllocations = [
        'row_a' => ['weekly_hrs' => [10 => 20, 11 => 16]],
        'row_b' => ['weekly_hrs' => [10 => 15, 11 => 16]],
    ];

    expect(activityAllocationWeeklyTotalsMatch($component))->toBeFalse()
        ->and($component->weeklyHoursError)->toBe('Total allocated hours for Week 1 must equal 40.00.');
});

it('accepts a monthly allocation update when every weekly total matches', function () {
    $component = new Edit;
    $component->weeks = [
        ['id' => 10, 'week' => 1],
        ['id' => 11, 'week' => 2],
    ];
    $component->weeklyTotalsExpected = [10 => 40, 11 => 32];
    $component->activityAllocations = [
        'row_a' => ['weekly_hrs' => [10 => 20, 11 => 16]],
        'row_b' => ['weekly_hrs' => [10 => 20, 11 => 16]],
    ];

    expect(activityAllocationWeeklyTotalsMatch($component))->toBeTrue()
        ->and($component->weeklyHoursError)->toBeNull();
});
