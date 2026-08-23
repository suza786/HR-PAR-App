<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold">{{ __('Email Setup') }}</h1>
        <p class="mt-1 text-sm text-gray-600">{{ __('Configure the reusable application email service. This page is available only to HR users.') }}</p>
    </div>

    @if($statusMessage)
        <div class="rounded border px-4 py-3 {{ $statusType === 'success' ? 'border-green-400 bg-green-100 text-green-700' : 'border-red-400 bg-red-100 text-red-700' }}">
            {{ $statusMessage }}
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow">
        <h2 class="text-2xl font-bold mb-4">{{ __('Mail Configuration') }}</h2>

        <form wire:submit.prevent="save" class="space-y-5">
            <label class="flex items-center gap-3">
                <input type="checkbox" wire:model="enabled" class="rounded border-gray-300">
                <span class="font-semibold">{{ __('Enable application email sending') }}</span>
            </label>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-2">{{ __('Mailer') }}</label>
                    <select wire:model.live="mailer" class="w-full border border-gray-300 rounded px-3 py-2">
                        <option value="smtp">SMTP</option>
                        <option value="log">Log (development)</option>
                    </select>
                    @error('mailer') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-2">{{ __('From Address') }}</label>
                    <input type="email" wire:model="fromAddress" class="w-full border border-gray-300 rounded px-3 py-2">
                    @error('fromAddress') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-2">{{ __('From Name') }}</label>
                    <input type="text" wire:model="fromName" class="w-full border border-gray-300 rounded px-3 py-2">
                    @error('fromName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            @if($mailer === 'smtp')
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-2">{{ __('SMTP Host') }}</label>
                        <input type="text" wire:model="host" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="smtp.example.com">
                        @error('host') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-2">{{ __('SMTP Port') }}</label>
                        <input type="number" wire:model="port" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="587">
                        @error('port') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-2">{{ __('Scheme') }}</label>
                        <select wire:model="scheme" class="w-full border border-gray-300 rounded px-3 py-2">
                            <option value="">Auto</option>
                            <option value="smtp">SMTP</option>
                            <option value="smtps">SMTPS</option>
                        </select>
                        @error('scheme') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-2">{{ __('Username') }}</label>
                        <input type="text" wire:model="username" class="w-full border border-gray-300 rounded px-3 py-2" autocomplete="off">
                        @error('username') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-2">{{ __('Password') }}</label>
                        <input type="password" wire:model="password" class="w-full border border-gray-300 rounded px-3 py-2" autocomplete="new-password" placeholder="{{ $hasSavedPassword ? __('Saved - leave blank to keep') : '' }}">
                        @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
            @endif

            <div class="flex justify-end">
                <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600 font-semibold">
                    {{ __('Save Settings') }}
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow">
        <h2 class="text-2xl font-bold mb-4">{{ __('Send Test Email') }}</h2>
        <form wire:submit.prevent="sendTestEmail" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-2">{{ __('Subject') }}</label>
                <input type="text" wire:model="testSubject" class="w-full border border-gray-300 rounded px-3 py-2">
                @error('testSubject') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold mb-2">{{ __('To') }}</label>
                <input type="email" wire:model="testTo" class="w-full border border-gray-300 rounded px-3 py-2">
                @error('testTo') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold mb-2">{{ __('CC (optional)') }}</label>
                <input type="text" wire:model="testCc" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="one@example.com, two@example.com">
                @error('testCc') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="md:col-span-3 flex justify-end">
                <button type="submit" class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600 font-semibold">
                    {{ __('Send Test') }}
                </button>
            </div>
        </form>
    </div>

    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 shadow">
        <h2 class="text-2xl font-bold mb-4">{{ __('Using Email in Other Components') }}</h2>
        <ol class="list-decimal list-inside space-y-2 text-sm text-gray-700 mb-4">
            <li>Save and enable a working configuration above, then verify it with Send Test.</li>
            <li>Type-hint <code>App\Services\EmailSender</code> in a Livewire action, or resolve it with <code>app(EmailSender::class)</code>.</li>
            <li>Call <code>send()</code> with <code>$subject</code>, <code>$to</code>, optional <code>$cc</code>, and optional message body.</li>
            <li>Check the returned boolean and show suitable feedback. Transport failures are also written to the application log.</li>
        </ol>
        <pre class="overflow-x-auto rounded bg-gray-900 p-4 text-sm text-gray-100"><code>use App\Services\EmailSender;

public function notify(EmailSender $emailSender): void
{
    $sent = $emailSender-&gt;send(
        subject: 'Your subject',
        to: 'user@example.com',
        cc: 'manager@example.com', // optional
        body: 'Your email message.' // optional
    );
}</code></pre>
    </div>
</div>
