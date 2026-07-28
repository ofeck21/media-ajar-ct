<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        @if(auth()->user()->role === 'student')
            <!-- Student Top Header (No Sidebar) -->
            <header class="bg-gradient-to-r from-emerald-600 to-teal-700 text-white shadow-lg sticky top-0 z-50">
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="bg-white/20 backdrop-blur-md p-2 rounded-xl text-white">
                            <flux:icon.academic-cap class="size-6" />
                        </div>
                        <div>
                            <span class="font-extrabold text-lg tracking-wide text-white">CT Quest</span>
                            <span class="text-xs text-emerald-100 block font-medium">Platform Belajar CT</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2 bg-white/10 backdrop-blur-md px-3 py-1.5 rounded-full border border-white/20">
                            <flux:avatar :name="auth()->user()->name" size="xs" class="ring-2 ring-white/50" />
                            <span class="text-xs font-semibold text-white">{{ auth()->user()->name }}</span>
                        </div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-xs bg-white/10 hover:bg-red-500/80 text-white font-medium px-3 py-1.5 rounded-full transition-all flex items-center gap-1.5 border border-white/20">
                                <flux:icon.arrow-right-start-on-rectangle class="size-4" />
                                <span class="hidden sm:inline">Keluar</span>
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="w-full">
                {{ $slot }}
            </main>
        @else
            <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
                <flux:sidebar.header>
                    <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                    <flux:sidebar.collapse class="lg:hidden" />
                </flux:sidebar.header>

                <flux:sidebar.nav>
                    <flux:sidebar.group :heading="__('Platform')" class="grid">
                        <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                            {{ __('Dashboard') }}
                        </flux:sidebar.item>
                        
                        @if(auth()->user()->role === 'superadmin')
                            <flux:sidebar.item icon="building-library" :href="route('superadmin.schools.index')" :current="request()->routeIs('superadmin.schools.*')" wire:navigate>
                                {{ __('Manajemen Sekolah') }}
                            </flux:sidebar.item>
                            <flux:sidebar.item icon="users" :href="route('superadmin.users.index')" :current="request()->routeIs('superadmin.users.*')" wire:navigate>
                                {{ __('Manajemen Pengguna') }}
                            </flux:sidebar.item>
                            <flux:sidebar.item icon="document-text" :href="route('superadmin.questions.index')" :current="request()->routeIs('superadmin.questions.*')" wire:navigate>
                                {{ __('Manajemen Soal') }}
                            </flux:sidebar.item>
                        @endif

                        @if(auth()->user()->role === 'teacher')
                            <flux:sidebar.item icon="building-library" :href="route('teacher.classrooms.index')" :current="request()->routeIs('teacher.classrooms.*')" wire:navigate>
                                {{ __('Manajemen Kelas') }}
                            </flux:sidebar.item>
                            <flux:sidebar.item icon="academic-cap" :href="route('teacher.students.index')" :current="request()->routeIs('teacher.students.*')" wire:navigate>
                                {{ __('Daftar Siswa') }}
                            </flux:sidebar.item>
                        @endif
                    </flux:sidebar.group>
                </flux:sidebar.nav>

                <flux:spacer />

                <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
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
                                    <flux:avatar
                                        :name="auth()->user()->name"
                                        :initials="auth()->user()->initials()"
                                    />

                                    <div class="grid flex-1 text-start text-sm leading-tight">
                                        <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                        <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                    </div>
                                </div>
                            </div>
                        </flux:menu.radio.group>

                        <flux:menu.separator />

                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <flux:menu.item
                                as="button"
                                type="submit"
                                icon="arrow-right-start-on-rectangle"
                                class="w-full cursor-pointer"
                                data-test="logout-button"
                            >
                                {{ __('Log out') }}
                            </flux:menu.item>
                        </form>
                    </flux:menu>
                </flux:dropdown>
            </flux:header>

            {{ $slot }}
        @endif

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
