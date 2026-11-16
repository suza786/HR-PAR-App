<?php

use App\Livewire\ParReport;
use App\Mail\GenericEmail;
use App\Models\EmailSetting;
use App\Models\MonthlyActivity;
use App\Models\ParCalendar;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;

class ParReportApprovalUser extends Authenticatable
{
    protected $guarded = [];

    public $emp_id = 'SUP001';

    public $user_type = 'Employee';
}

beforeEach(function () {
    config([
        'database.connections.sna_lms.driver' => 'sqlite',
        'database.connections.sna_lms.database' => ':memory:',
    ]);
    DB::purge('sna_lms');

    Schema::connection('sna_lms')->create('employees', function (Blueprint $table) {
        $table->id();
        $table->string('emp_id')->unique();
        $table->string('name');
        $table->string('email')->nullable();
        $table->string('supervisor')->nullable();
        $table->string('user_status')->default('enable');
    });

    Schema::create('par_calendars', function (Blueprint $table) {
        $table->id();
        $table->string('month');
        $table->integer('year');
        $table->integer('week');
        $table->date('weekStart')->default('2026-06-01');
        $table->date('weekEnd')->default('2026-06-07');
        $table->softDeletes();
        $table->timestamps();
    });

    Schema::create('monthly_activities', function (Blueprint $table) {
        $table->id();
        $table->foreignId('par_calendar_id')->constrained('par_calendars');
        $table->string('emp_id');
        $table->string('status');
        $table->softDeletes();
        $table->timestamps();
    });

    Schema::create('email_settings', function (Blueprint $table) {
        $table->id();
        $table->boolean('enabled')->default(false);
        $table->string('mailer')->default('log');
        $table->string('host')->nullable();
        $table->unsignedSmallInteger('port')->nullable();
        $table->string('scheme')->nullable();
        $table->string('username')->nullable();
        $table->text('password')->nullable();
        $table->string('from_address');
        $table->string('from_name');
        $table->timestamps();
    });

    foreach (['projects', 'activities'] as $tableName) {
        Schema::create($tableName, function (Blueprint $table) {
            $table->id();
            $table->string($table->getTable() === 'projects' ? 'project_name' : 'name');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    Schema::create('activity_allocations', function (Blueprint $table) {
        $table->id();
        $table->foreignId('monthly_activity_id');
        $table->foreignId('project_id');
        $table->foreignId('activity_id')->nullable();
        $table->string('activity_description')->nullable();
        $table->decimal('working_hrs', 8, 2);
        $table->softDeletes();
        $table->timestamps();
    });

    DB::connection('sna_lms')->table('employees')->insert([
        ['emp_id' => 'SUP001', 'name' => 'Supervisor', 'email' => null, 'supervisor' => null, 'user_status' => 'enable'],
        ['emp_id' => 'EMP001', 'name' => 'Subordinate', 'email' => 'subordinate@example.com', 'supervisor' => 'SUP001', 'user_status' => 'enable'],
        ['emp_id' => 'EMP002', 'name' => 'Other employee', 'email' => 'other@example.com', 'supervisor' => 'OTHER', 'user_status' => 'enable'],
    ]);

    $this->actingAs(new ParReportApprovalUser);
});

it('lists only subordinates and approves requested activity allocations for the selected period', function () {
    $calendar = ParCalendar::create(['month' => 'June', 'year' => 2026, 'week' => 1]);
    $requested = MonthlyActivity::create([
        'par_calendar_id' => $calendar->id,
        'emp_id' => 'EMP001',
        'status' => 'requested',
    ]);
    $approved = MonthlyActivity::create([
        'par_calendar_id' => $calendar->id,
        'emp_id' => 'EMP001',
        'status' => 'approved',
    ]);
    $otherPeriod = MonthlyActivity::create([
        'par_calendar_id' => $calendar->id,
        'emp_id' => 'EMP002',
        'status' => 'requested',
    ]);

    EmailSetting::create([
        'enabled' => true,
        'mailer' => 'log',
        'from_address' => 'par@example.com',
        'from_name' => 'PAR App',
    ]);
    Mail::fake();

    $projectId = DB::table('projects')->insertGetId(['project_name' => 'Project One']);
    DB::table('activity_allocations')->insert([
        'monthly_activity_id' => $requested->id,
        'project_id' => $projectId,
        'activity_description' => 'Project work',
        'working_hrs' => 6,
    ]);

    Livewire::test(ParReport::class)
        ->assertSet('approvalEmployee', null)
        ->assertSet('approvalEmployees', [['emp_id' => 'EMP001', 'name' => 'Subordinate']])
        ->set('approvalYear', 2026)
        ->call('showApprovalPars')
        ->assertSet('approvalRows.0.status', 'Approved/Requested')
        ->assertSet('approvalRows.0.has_requested', true)
        ->assertSet('approvalRows.0.month', 'June')
        ->assertSee('Approve')
        ->call('approvePar', 'EMP001', 'June')
        ->assertSet('approvalMessage', 'Activity allocation approved successfully.')
        ->assertSet('approvalRows.0.status', 'Approved')
        ->assertSet('approvalRows.0.has_requested', false)
        ->assertDontSee('wire:click="approvePar', false);

    Mail::assertSent(GenericEmail::class, fn (GenericEmail $mail): bool => $mail->hasTo('subordinate@example.com')
        && $mail->emailSubject === 'Activity Allocations Approved - June 2026'
        && str_contains($mail->emailBody, 'Please upload your approved PAR using the link provided below:')
        && str_contains($mail->emailBody, 'https://drive.google.com/drive/folders/19A1BA9pG2MJZCJrj9Rr__D5mdIBzBXHg')
        && count($mail->attachments()) === 1
        && $mail->attachments()[0]->as === 'PAR-Report-EMP001-June-2026.pdf'
        && $mail->attachments()[0]->mime === 'application/pdf');

    $mail = Mail::sent(GenericEmail::class)->first();
    $mail->attachments()[0]->attachWith(
        fn () => throw new RuntimeException('Expected an in-memory PDF attachment.'),
        function ($data, $attachment) {
            expect($data())->toStartWith('%PDF-');
        },
    );
    expect(app(\App\Services\ParReportData::class)->build(2026, 'June', 'EMP001')['status'])
        ->toBe('Approved');

    expect($requested->fresh()->status)->toBe('approved')
        ->and($approved->fresh()->status)->toBe('approved')
        ->and($otherPeriod->fresh()->status)->toBe('requested');
});

it('shows activity allocations in the selected subordinate PAR modal', function () {
    $calendar = ParCalendar::create(['month' => 'June', 'year' => 2026, 'week' => 2]);
    $monthlyActivity = MonthlyActivity::create([
        'par_calendar_id' => $calendar->id,
        'emp_id' => 'EMP001',
        'status' => 'requested',
    ]);
    $projectId = DB::table('projects')->insertGetId(['project_name' => 'Project One']);
    $activityId = DB::table('activities')->insertGetId(['name' => 'Activity One']);
    DB::table('activity_allocations')->insert([
        'monthly_activity_id' => $monthlyActivity->id,
        'project_id' => $projectId,
        'activity_id' => $activityId,
        'activity_description' => null,
        'working_hrs' => 6,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    Livewire::test(ParReport::class)
        ->set('approvalYear', 2026)
        ->call('viewPar', 'EMP001', 'June')
        ->assertSet('showApprovalModal', true)
        ->assertSet('approvalModal.month', 'June')
        ->assertSet('approvalModal.year', 2026)
        ->assertSet('approvalModal.rows.0.project', 'Project One')
        ->assertSet('approvalModal.rows.0.activity', 'Activity One')
        ->assertSet('approvalModal.rows.0.hours', [6]);
});

it('reports when approval succeeds but email notifications are disabled', function () {
    $calendar = ParCalendar::create(['month' => 'June', 'year' => 2026, 'week' => 1]);
    MonthlyActivity::create([
        'par_calendar_id' => $calendar->id,
        'emp_id' => 'EMP001',
        'status' => 'requested',
    ]);

    Livewire::test(ParReport::class)
        ->set('approvalYear', 2026)
        ->call('approvePar', 'EMP001', 'June')
        ->assertSet('approvalMessage', 'Activity allocation approved successfully.')
        ->assertSet('approvalEmailMessage', 'The PAR was approved, but no email was sent because email notifications are disabled.');
});

it('rejects attempts to view a non-subordinate PAR', function () {
    Livewire::test(ParReport::class)
        ->call('viewPar', 'EMP002', 'June')
        ->assertForbidden();
});
