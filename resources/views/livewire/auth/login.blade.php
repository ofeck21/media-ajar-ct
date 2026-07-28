<x-layouts::auth :title="__('Masuk')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Masuk ke akun Anda')" :description="__('Masukkan Email, Username atau NISN beserta kata sandi Anda di bawah ini untuk masuk')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />


        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Username / NISN / Email -->
            <flux:input
                name="username"
                :label="__('Email / Username / NISN')"
                :value="old('username')"
                required
                autofocus
                autocomplete="username"
                placeholder="Masukkan Email, Username, atau NISN"
            />

            <!-- Password -->
            <div class="relative">
                <flux:input
                    name="password"
                    :label="__('Kata Sandi')"
                    type="password"
                    required
                    autocomplete="current-password"
                    :placeholder="__('Kata Sandi')"
                    viewable
                />

                @if (Route::has('password.request'))
                    <flux:link class="absolute top-0 text-sm end-0" :href="route('password.request')" wire:navigate>
                        {{ __('Lupa kata sandi Anda?') }}
                    </flux:link>
                @endif
            </div>

            <!-- Remember Me -->
            <flux:checkbox name="remember" :label="__('Ingat saya')" :checked="old('remember')" />

            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit" class="w-full" data-test="login-button">
                    {{ __('Masuk') }}
                </flux:button>
            </div>
        </form>

        <div class="space-x-1 text-sm text-center rtl:space-x-reverse text-zinc-600 dark:text-zinc-400">
            <span>{{ __('Belum punya akun?') }}</span>
            <flux:link :href="route('register')" wire:navigate>{{ __('Daftar') }}</flux:link>
        </div>
    </div>
</x-layouts::auth>
