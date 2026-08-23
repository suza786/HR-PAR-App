<?php

namespace App\Livewire;

use App\Models\EmailSetting;
use App\Services\EmailSender;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class EmailSetup extends Component
{
    public bool $enabled = false;

    public string $mailer = 'smtp';

    public string $host = '';

    public ?int $port = 587;

    public string $scheme = '';

    public string $username = '';

    public string $password = '';

    public string $fromAddress = '';

    public string $fromName = '';

    public bool $hasSavedPassword = false;

    public string $testSubject = 'PAR App email test';

    public string $testTo = '';

    public string $testCc = '';

    public string $statusMessage = '';

    public string $statusType = 'success';

    public function mount(): void
    {
        abort_unless(Auth::user()?->user_type === 'HR', 403);

        $setting = EmailSetting::query()->first();

        if (! $setting) {
            $this->fromAddress = (string) config('mail.from.address');
            $this->fromName = (string) config('mail.from.name');
            $this->testTo = (string) Auth::user()->email;

            return;
        }

        $this->enabled = $setting->enabled;
        $this->mailer = $setting->mailer;
        $this->host = (string) $setting->host;
        $this->port = $setting->port;
        $this->scheme = (string) $setting->scheme;
        $this->username = (string) $setting->username;
        $this->fromAddress = $setting->from_address;
        $this->fromName = $setting->from_name;
        $this->hasSavedPassword = filled($setting->password);
        $this->testTo = (string) Auth::user()->email;
    }

    public function save(): void
    {
        $validated = $this->validate($this->settingsRules());
        $setting = EmailSetting::query()->firstOrNew();

        $setting->fill([
            'enabled' => $validated['enabled'],
            'mailer' => $validated['mailer'],
            'host' => $validated['host'] ?: null,
            'port' => $validated['port'],
            'scheme' => $validated['scheme'] ?: null,
            'username' => $validated['username'] ?: null,
            'from_address' => $validated['fromAddress'],
            'from_name' => $validated['fromName'],
        ]);

        if ($validated['password'] !== '') {
            $setting->password = $validated['password'];
        }

        $setting->save();
        $this->password = '';
        $this->hasSavedPassword = filled($setting->password);
        $this->statusType = 'success';
        $this->statusMessage = 'Email settings saved successfully.';
    }

    public function sendTestEmail(EmailSender $emailSender): void
    {
        $this->validate([
            'testSubject' => ['required', 'string', 'max:255'],
            'testTo' => ['required', 'email'],
            'testCc' => ['nullable', function (string $attribute, mixed $value, \Closure $fail): void {
                foreach (preg_split('/[,;]/', (string) $value) as $email) {
                    if (trim($email) !== '' && ! filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
                        $fail('Every CC address must be a valid email address.');
                    }
                }
            }],
        ]);

        $sent = $emailSender->send(
            subject: $this->testSubject,
            to: $this->testTo,
            cc: $this->testCc ?: null,
            body: "This is a test email from the PAR App Email Setup page.\n\nIf you received it, email sending is configured correctly.",
        );

        $this->statusType = $sent ? 'success' : 'error';
        $this->statusMessage = $sent
            ? 'Test email sent successfully.'
            : 'Test email could not be sent. Confirm that email is enabled and review the SMTP settings and application log.';
    }

    private function settingsRules(): array
    {
        return [
            'enabled' => ['boolean'],
            'mailer' => ['required', 'in:smtp,log'],
            'host' => ['nullable', 'required_if:mailer,smtp', 'string', 'max:255'],
            'port' => ['nullable', 'required_if:mailer,smtp', 'integer', 'min:1', 'max:65535'],
            'scheme' => ['nullable', 'in:smtp,smtps'],
            'username' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'max:255'],
            'fromAddress' => ['required', 'email', 'max:255'],
            'fromName' => ['required', 'string', 'max:255'],
        ];
    }

    public function render()
    {
        return view('livewire.email-setup');
    }
}
