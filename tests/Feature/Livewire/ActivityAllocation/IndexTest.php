<?php

use App\Livewire\ActivityAllocation\Index;
use App\Models\MonthlyActivity;
use App\Models\ParCalendar;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;

class TestUser extends Authenticatable
{
    protected $guarded = [];

    public $id = 1;

    public $user_type = 'EMPLOYEE';

    public $emp_id = 'EMP001';

    public $name = 'Test Employee';
}

it('updates pending monthly activities to requested when submitted', function () {
    Schema::dropIfExists('monthly_activities');
    Schema::dropIfExists('par_calendars');

    Schema::create('par_calendars', function (Blueprint $table) {
        $table->id();
        $table->string('month');
        $table->integer('year');
        $table->integer('week');
        $table->softDeletes();
        $table->timestamps();
    });

    Schema::create('monthly_activities', function (Blueprint $table) {
        $table->id();
        $table->foreignId('par_calendar_id')->constrained('par_calendars')->onDelete('cascade');
        $table->string('emp_id');
        $table->string('status')->default('pending');
        $table->softDeletes();
        $table->timestamps();
    });

    $this->actingAs(new TestUser);

    $parCalendar = ParCalendar::create([
        'month' => 'January',
        'year' => 2026,
        'week' => 1,
    ]);

    $monthlyActivity = MonthlyActivity::create([
        'par_calendar_id' => $parCalendar->id,
        'emp_id' => 'EMP001',
        'status' => 'pending',
    ]);

    Livewire::test(Index::class)
        ->set('selectedYear', 2026)
        ->set('selectedMonth', 'January')
        ->set('selectedEmployee', 'EMP001')
        ->call('submitActivityAllocationData')
        ->assertSet('activityStatus', 'requested')
        ->assertSet('submissionMessage', 'Activity allocation submitted successfully.');

    expect($monthlyActivity->fresh()->status)->toBe('requested');
});
