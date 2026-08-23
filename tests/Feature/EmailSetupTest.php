<?php

use App\Livewire\EmailSetup;
use App\Mail\GenericEmail;
use App\Models\EmailSetting;
use App\Services\EmailSender;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;

class EmailSetupTestUser extends Authenticatable
{
    public $id = 501;

    public $name = 'HR User';

    public $email = 'hr@example.com';

    public $user_type = 'HR';
}

beforeEach(function () {
    Schema::dropIfExists('email_settings');
    Schema::create('email_settings', function (Blueprint $table): void {
        $table->id();
        $table->boolean('enabled')->default(false);
        $table->string('mailer')->default('smtp');
        $table->string('host')->nullable();
        $table->unsignedSmallInteger('port')->nullable();
        $table->string('scheme')->nullable();
        $table->string('username')->nullable();
        $table->text('password')->nullable();
        $table->string('from_address');
        $table->string('from_name');
        $table->timestamps();
    });
});

it('allows an HR user to save email settings', function () {
    $this->actingAs(new EmailSetupTestUser);

    Livewire::test(EmailSetup::class)
        ->set('enabled', true)
        ->set('mailer', 'log')
        ->set('fromAddress', 'par@example.com')
        ->set('fromName', 'PAR App')
        ->call('save')
        ->assertSet('statusMessage', 'Email settings saved successfully.');

    expect(EmailSetting::query()->first())
        ->enabled->toBeTrue()
        ->mailer->toBe('log')
        ->from_address->toBe('par@example.com');
});

it('blocks non-HR users from the email setup component', function () {
    $user = new EmailSetupTestUser;
    $user->user_type = 'Employee';
    $this->actingAs($user);

    Livewire::test(EmailSetup::class)->assertForbidden();
});

it('sends reusable email with to and optional cc addresses', function () {
    EmailSetting::create([
        'enabled' => true,
        'mailer' => 'log',
        'from_address' => 'par@example.com',
        'from_name' => 'PAR App',
    ]);

    Mail::fake();

    $sent = app(EmailSender::class)->send(
        subject: 'Test subject',
        to: 'user@example.com',
        cc: 'supervisor@example.com',
        body: 'Test body',
    );

    expect($sent)->toBeTrue();

    Mail::assertSent(GenericEmail::class, fn (GenericEmail $mail): bool => $mail->hasTo('user@example.com')
        && $mail->hasCc('supervisor@example.com')
        && $mail->emailSubject === 'Test subject');
});
