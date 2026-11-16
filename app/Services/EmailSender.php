<?php

namespace App\Services;

use App\Mail\GenericEmail;
use App\Models\EmailSetting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Throwable;

class EmailSender
{
    public function enabled(): bool
    {
        return Schema::hasTable('email_settings')
            && EmailSetting::query()->where('enabled', true)->exists();
    }

    public function send(
        string $subject,
        string|array $to,
        string|array|null $cc = null,
        ?string $body = null,
        array $attachments = [],
    ): bool {
        $setting = $this->setting();

        if (! $setting || ! $setting->enabled || $this->normalizeAddresses($to) === []) {
            return false;
        }

        try {
            $this->applyConfiguration($setting);
            $toAddresses = $this->normalizeAddresses($to);
            $ccAddresses = $this->normalizeAddresses($cc);

            $pendingMail = Mail::to($toAddresses);

            if ($ccAddresses !== []) {
                $pendingMail->cc($ccAddresses);
            }

            $pendingMail->send(new GenericEmail($subject, $body ?: $subject, $attachments));

            return true;
        } catch (Throwable $exception) {
            Log::error('Email sending failed.', [
                'subject' => $subject,
                'to' => $this->normalizeAddresses($to),
                'error' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    private function setting(): ?EmailSetting
    {
        if (! Schema::hasTable('email_settings')) {
            return null;
        }

        return EmailSetting::query()->first();
    }

    private function applyConfiguration(EmailSetting $setting): void
    {
        config([
            'mail.default' => $setting->mailer,
            'mail.from.address' => $setting->from_address,
            'mail.from.name' => $setting->from_name,
        ]);

        if ($setting->mailer === 'smtp') {
            config([
                'mail.mailers.smtp.transport' => 'smtp',
                'mail.mailers.smtp.scheme' => $setting->scheme ?: null,
                'mail.mailers.smtp.host' => $setting->host,
                'mail.mailers.smtp.port' => $setting->port,
                'mail.mailers.smtp.username' => $setting->username ?: null,
                'mail.mailers.smtp.password' => $setting->password ?: null,
            ]);
        }

        app('mail.manager')->forgetMailers();
    }

    private function normalizeAddresses(string|array|null $addresses): array
    {
        if ($addresses === null) {
            return [];
        }

        $addresses = is_array($addresses) ? $addresses : preg_split('/[,;]/', $addresses);

        return collect($addresses)
            ->map(fn ($address): string => trim((string) $address))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
