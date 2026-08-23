<?php

use App\Livewire\MonthlyActivities\Create;
use App\Models\MonthlyActivity;
use App\Models\ParCalendar;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

it('shows only the warning when monthly activities already exist for the selected employee', function () {
    Schema::dropIfExists('monthly_activities');
    Schema::dropIfExists('par_calendars');

    Schema::create('par_calendars', function (Blueprint $table): void {
        $table->id();
        $table->string('month');
        $table->integer('year');
        $table->integer('week');
        $table->date('weekStart');
        $table->date('weekEnd');
        $table->integer('working_days');
        $table->integer('office_holidays')->default(0);
        $table->integer('total_days');
        $table->softDeletes();
        $table->timestamps();
    });

    Schema::create('monthly_activities', function (Blueprint $table): void {
        $table->id();
        $table->foreignId('par_calendar_id');
        $table->string('emp_id');
        $table->unsignedTinyInteger('leave_hrs')->default(0);
        $table->unsignedTinyInteger('working_hrs')->default(0);
        $table->string('note')->nullable();
        $table->string('status')->default('pending');
        $table->softDeletes();
        $table->timestamps();
    });

    $calendar = ParCalendar::create([
        'month' => 'January',
        'year' => 2026,
        'week' => 1,
        'weekStart' => '2026-01-01',
        'weekEnd' => '2026-01-07',
        'working_days' => 5,
        'office_holidays' => 0,
        'total_days' => 7,
    ]);

    MonthlyActivity::create([
        'par_calendar_id' => $calendar->id,
        'emp_id' => 'EMP001',
        'leave_hrs' => 0,
        'working_hrs' => 40,
    ]);

    $component = new Create;
    $component->selectedYear = 2026;
    $component->selectedMonth = 'January';
    $component->selectedEmployee = 'EMP001';
    $component->employees = [['emp_id' => 'EMP001', 'name' => 'Test Employee']];
    $component->userLeavesLoaded = true;
    $component->holidayLoaded = true;

    $component->showCalendarData();

    expect($component->duplicateMessage)
        ->toBe('Monthly Activities already created for the user: EMP001 - Test Employee')
        ->and($component->calendarGenerated)->toBeFalse()
        ->and($component->userLeavesLoaded)->toBeFalse()
        ->and($component->holidayLoaded)->toBeFalse()
        ->and($component->activities)->toBe([]);
});
