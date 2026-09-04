<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900 relative">
        <x-fullscreen-button class="fixed top-4 right-4 z-50" />
        <div class="relative grid h-dvh flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0">
            <div class="bg-muted relative hidden h-full flex-col p-10 text-white lg:flex dark:border-e dark:border-emerald-800">
                <div class="absolute inset-0 bg-emerald-900 dark:bg-linear-to-b dark:from-emerald-900 dark:to-emerald-950"></div>
                <a href="{{ route('home') }}" class="relative z-20 flex items-center text-lg font-medium text-white" wire:navigate>
                    <span class="flex h-10 w-10 items-center justify-center rounded-md">
                        <x-app-logo-icon class="me-2 h-7 fill-current text-white" />
                    </span>
                    CT Quest: Safari Mengaji
                </a>

                <div class="relative z-20 flex-1 flex items-center justify-center">
                    <div class="text-[150px] drop-shadow-2xl opacity-90 hover:scale-110 transition-transform duration-500 cursor-default">
                        👳‍♂️
                    </div>
                </div>

                <div class="relative z-20 mt-auto">
                    <blockquote class="space-y-2">
                        <h2 class="text-xl font-semibold text-white leading-relaxed">&ldquo;Belajar coding dan logika layaknya menata niat dan merangkai langkah menuju kebaikan.&rdquo;</h2>
                        <footer class="text-emerald-200 font-medium">Misi Safari Mengaji</footer>
                    </blockquote>
                </div>
            </div>
            <div class="w-full lg:p-8">
                <div class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[350px]">
                    <a href="{{ route('home') }}" class="z-20 flex flex-col items-center gap-2 font-medium lg:hidden" wire:navigate>
                        <span class="flex h-9 w-9 items-center justify-center rounded-md">
                            <x-app-logo-icon class="size-9 fill-current text-black dark:text-white" />
                        </span>

                        <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
                    </a>
                    {{ $slot }}
                </div>
            </div>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
