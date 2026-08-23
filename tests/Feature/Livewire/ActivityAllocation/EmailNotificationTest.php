<?php

use App\Livewire\ActivityAllocation\Index;
use App\Mail\GenericEmail;
use App\Models\EmailSetting;
use App\Models\Employee;
use App\Models\MonthlyActivity;
use App\Models\ParCalendar;
use App\Services\EmailSender;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

class ActivityAllocationEmailUser extends Authenticatable
{
    public $id = 801;

    public $emp_id = 'EMP001';

    public $user_type = 'Employee';
}

it('emails the supervisor on submission and employee on approval', function () {
    config(['database.connections.sna_lms' => config('database.connections.sqlite')]);
    DB::purge('sna_lms');

    Schema::connection('sna_lms')->create('employees', function (Blueprint $table): void {
        $table->id();
        $table->string('emp_id')->unique();
        $table->string('name');
        $table->string('email');
        $table->string('user_type')->default('Employee');
        $table->string('user_status')->default('enable');
        $table->string('supervisor')->nullable();
        $table->unsignedBigInteger('supervisor_id')->nullable();
        $table->string('password')->nullable();
        $table->timestamps();
    });

    Schema::create('par_calendars', function (Blueprint $table): void {
        $table->id();
        $table->string('month');
        $table->integer('year');
        $table->integer('week');
        $table->softDeletes();
        $table->timestamps();
    });

    Schema::create('monthly_activities', function (Blueprint $table): void {
        $table->id();
        $table->foreignId('par_calendar_id');
        $table->string('emp_id');
        $table->string('status')->default('pending');
        $table->softDeletes();
        $table->timestamps();
    });

    Schema::create('email_settings', function (Blueprint $table): void {
        $table->id();
        $table->boolean('enabled');
        $table->string('mailer');
        $table->string('host')->nullable();
        $table->unsignedSmallInteger('port')->nullable();
        $table->string('scheme')->nullable();
        $table->string('username')->nullable();
        $table->text('password')->nullable();
        $table->string('from_address');
        $table->string('from_name');
        $table->timestamps();
    });

    Employee::create([
        'emp_id' => 'SUP001',
        'name' => 'Supervisor',
        'email' => 'supervisor@example.com',
    ]);
    Employee::create([
        'emp_id' => 'EMP001',
        'name' => 'Employee',
        'email' => 'employee@example.com',
        'supervisor' => 'SUP001',
    ]);

    $calendar = ParCalendar::create(['month' => 'January', 'year' => 2026, 'week' => 1]);
    MonthlyActivity::create([
        'par_calendar_id' => $calendar->id,
        'emp_id' => 'EMP001',
        'status' => 'pending',
    ]);
    EmailSetting::create([
        'enabled' => true,
        'mailer' => 'log',
        'from_address' => 'par@example.com',
        'from_name' => 'PAR App',
    ]);

    $this->actingAs(new ActivityAllocationEmailUser);
    Mail::fake();

    $component = new Index;
    $component->selectedEmployee = 'EMP001';
    $component->selectedMonth = 'January';
    $component->selectedYear = 2026;
    $component->submitActivityAllocationData(app(EmailSender::class));
    $component->approveActivityAllocationData(app(EmailSender::class));

    Mail::assertSent(GenericEmail::class, fn (GenericEmail $mail): bool => $mail->hasTo('supervisor@example.com'));
    Mail::assertSent(GenericEmail::class, fn (GenericEmail $mail): bool => $mail->hasTo('employee@example.com'));
    expect(MonthlyActivity::query()->first()->status)->toBe('approved');
});
