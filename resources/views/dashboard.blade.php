<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            
            <a href="{{ route('par-calendars.index') }}" class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors" wire:navigate>
                <div class="flex items-center justify-center h-full">
                    <div class="text-center">
                        <flux:icon.calendar class="mx-auto mb-2 size-8 text-gray-600 dark:text-gray-400" />
                        <div class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Par Calendars') }}</div>
                    </div>
                </div>
            </a>
            <a href="{{ route('par-report.index') }}" class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors" wire:navigate>
                <div class="flex items-center justify-center h-full">
                    <div class="text-center">
                        <flux:icon.document-chart-bar class="mx-auto mb-2 size-8 text-gray-600 dark:text-gray-400" />
                        <div class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('PAR Report') }}</div>
                    </div>
                </div>
            </a>
            <a href="{{ route('leave-information.index') }}" class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors" wire:navigate>
                <div class="flex items-center justify-center h-full">
                    <div class="text-center">
                        <flux:icon.document-text class="mx-auto mb-2 size-8 text-gray-600 dark:text-gray-400" />
                        <div class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Leave Information') }}</div>
                    </div>
                </div>
            </a>
        </div>
        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
        </div>
    </div>
</x-layouts.app>
