{{-- <flux:navlist.item icon="home" :href="route('bank-accounts.index')" :current="request()->routeIs('bank-accounts.index')" wire:navigate>{{ __('Bank Accounts') }}</flux:navlist.item> --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                <x-app-logo />
            </a>
    
            <flux:navlist variant="outline">
                <flux:navlist.group :heading="__('User Dashboard')" class="grid">
                    <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
                    @if(auth()->user()->user_type === 'HR')
                    <flux:navlist.item icon="home" :href="route('donors.index')" :current="request()->routeIs('donors.index')" wire:navigate>{{ __('Donors') }}</flux:navlist.item>
                    <flux:navlist.item icon="home" :href="route('projects.index')" :current="request()->routeIs('projects.*')" wire:navigate>{{ __('Projects') }}</flux:navlist.item>
                    <flux:navlist.item icon="home" :href="route('departments.index')" :current="request()->routeIs('departments.index')" wire:navigate>{{ __('Departments') }}</flux:navlist.item>
                    <flux:navlist.item icon="home" :href="route('activities.index')" :current="request()->routeIs('activities.index')" wire:navigate>{{ __('Activities') }}</flux:navlist.item>
                    <flux:navlist.item icon="home" :href="route('locations.index')" :current="request()->routeIs('locations.index')" wire:navigate>{{ __('Locations') }}</flux:navlist.item>
                    <flux:navlist.item icon="home" :href="route('emp-types.index')" :current="request()->routeIs('emp-types.index')" wire:navigate>{{ __('Emp Types') }}</flux:navlist.item>
                    <flux:navlist.item icon="home" :href="route('roles.index')" :current="request()->routeIs('roles.index')" wire:navigate>{{ __('Emp Roles') }}</flux:navlist.item>
                    <flux:navlist.item icon="home" :href="route('education-trainings.index')" :current="request()->routeIs('education-trainings.*')" wire:navigate>{{ __('Education/Trainings') }}</flux:navlist.item>
                    <flux:navlist.item icon="home" :href="route('employees.index')" :current="request()->routeIs('employees.index')" wire:navigate>{{ __('Employees') }}</flux:navlist.item>
                    <flux:navlist.item icon="home" :href="route('salaries.index')" :current="request()->routeIs('salaries.index')" wire:navigate>{{ __('Salaries') }}</flux:navlist.item>
                    <flux:navlist.item icon="home" :href="route('bank-accounts.index')" :current="request()->routeIs('bank-accounts.index')" wire:navigate>{{ __('Bank Accounts') }}</flux:navlist.item>
                    <flux:navlist.item icon="home" :href="route('personal-details.index')" :current="request()->routeIs('personal-details.index')" wire:navigate>{{ __('Emp Personal Det.') }}</flux:navlist.item>
                        @if(auth()->user()->user_type === 'HR' &&auth()->user()->vr_type === '3')
                            <flux:navlist.item icon="envelope" :href="route('email-setup')" :current="request()->routeIs('email-setup')" wire:navigate>{{ __('Email Setup') }}</flux:navlist.item>
                        @endif
                    @endif
                </flux:navlist.group>
                <flux:navlist.group :heading="__('PAR Operations')" class="grid">
                    <flux:navlist.item icon="home" :href="route('salary-allocations.index')" :current="request()->routeIs('salary-allocations.*')" wire:navigate>{{ __('1. Salary Allocations') }}</flux:navlist.item>
                    <flux:navlist.item icon="home" :href="route('monthly-activities.index')" :current="request()->routeIs('monthly-activities.*')" wire:navigate>{{ __('2. Monthly Activities') }}</flux:navlist.item>
                    <flux:navlist.item icon="home" :href="route('activity-allocations.index')" :current="request()->routeIs('activity-allocations.*')" wire:navigate>{{ __('3. Activity Allocations') }}</flux:navlist.item>
                    <flux:navlist.item icon="home" :href="route('par-report.index')" :current="request()->routeIs('par-report.*')" wire:navigate>{{ __('4. PAR Report') }}</flux:navlist.item>
                </flux:navlist.group>
            </flux:navlist>

            <flux:spacer /> 

            <flux:navlist variant="outline">
                <flux:navlist.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit" target="_blank">
                {{ __('Repository') }}
                </flux:navlist.item>

                <flux:navlist.item icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire" target="_blank">
                {{ __('Documentation') }}
                </flux:navlist.item>
            </flux:navlist>

            <!-- Desktop User Menu -->
            <flux:dropdown class="hidden lg:block" position="bottom" align="start">
                <flux:profile
                    :name="auth()->user()->name"
                    :initials="auth()->user()->initials()"
                    icon:trailing="chevrons-up-down"
                />

                <flux:menu class="w-[220px]">
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @fluxScripts
    </body>
</html>
