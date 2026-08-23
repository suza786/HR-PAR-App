<?php

use App\Livewire\ActivityAllocation\Create;

it('keeps dynamically added activity rows independent', function () {
    $component = new Create;
    $component->weeklyTotalsExpected = [10 => 40, 11 => 32];
    $component->activityAllocations = [
        'row_a' => [
            'row_key' => 'row_a',
            'project_id' => 1,
            'project_name' => 'Project A',
            'activity_id' => 5,
            'weekly_hrs' => [10 => '20.00', 11 => '16.00'],
            'is_added' => false,
        ],
        'row_b' => [
            'row_key' => 'row_b',
            'project_id' => 2,
            'project_name' => 'Project B',
            'activity_id' => 8,
            'weekly_hrs' => [10 => '20.00', 11 => '16.00'],
            'is_added' => false,
        ],
    ];

    $component->addActivity('row_a');

    $keys = array_keys($component->activityAllocations);
    $addedRowKey = $keys[1];
    $component->activityAllocations[$addedRowKey]['activity_id'] = 12;
    $component->activityAllocations[$addedRowKey]['weekly_hrs'][10] = '7.50';

    expect($keys[0])->toBe('row_a')
        ->and($keys[2])->toBe('row_b')
        ->and($component->activityAllocations[$addedRowKey]['is_added'])->toBeTrue()
        ->and($component->activityAllocations[$addedRowKey]['activity_id'])->toBe(12)
        ->and($component->activityAllocations[$addedRowKey]['weekly_hrs'][10])->toBe('7.50')
        ->and($component->activityAllocations['row_b']['activity_id'])->toBe(8)
        ->and($component->activityAllocations['row_b']['weekly_hrs'][10])->toBe('20.00');
});
