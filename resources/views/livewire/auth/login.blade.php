<x-layouts::auth :title="__('Log in')">
    <div class="flex flex-col gap-6">
        {{-- Header dengan accent gradient --}}
        <x-auth-header :title="__('Log in to your account')" :description="__('Enter your email and password below to log in')" />

        {{-- Session Status --}}
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-5">
            @csrf

            {{-- Email Address --}}
            <flux:input
                name="email"
                :label="__('Email address')"
                :value="old('email')"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="email@example.com"
            />

            {{-- Password + Forgot link --}}
            <div class="relative">
                <flux:input
                    name="password"
                    :label="__('Password')"
                    type="password"
                    required
                    autocomplete="current-password"
                    :placeholder="__('Password')"
                    viewable
                />

                @if (Route::has('password.request'))
                    <flux:link class="absolute top-0 text-sm end-0" :href="route('password.request')" wire:navigate>
                        {{ __('Forgot your password?') }}
                    </flux:link>
                @endif
            </div>

            {{-- Remember Me --}}
            <flux:checkbox name="remember" :label="__('Remember me')" :checked="old('remember')" />

            {{-- Submit button: gradient emerald→cyan seperti logo sidebar --}}
            <flux:button variant="primary" type="submit" class="w-full justify-center rounded-xl bg-gradient-to-r from-emerald-500 to-cyan-500 text-slate-950 shadow-lg shadow-emerald-500/20 transition hover:from-emerald-400 hover:to-cyan-400" data-test="login-button">
                {{ __('Log in') }}
            </flux:button>
        </form>

        {{-- Divider --}}
        <div class="flex items-center gap-3 text-xs text-slate-400">
            <span class="h-px flex-1 bg-slate-200 dark:bg-slate-800"></span>
            <span>{{ __('or') }}</span>
            <span class="h-px flex-1 bg-slate-200 dark:bg-slate-800"></span>
        </div>

        <div class="space-x-1 text-sm text-center rtl:space-x-reverse text-slate-600 dark:text-slate-400">
            <span>{{ __('Don\'t have an account?') }}</span>
            <flux:link :href="route('register')" wire:navigate>{{ __('Sign up') }}</flux:link>
        </div>
    </div>
</x-layouts::auth>