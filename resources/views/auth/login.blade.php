<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded-md dark:bg-slate-900 border-slate-300 dark:border-slate-700 text-blue-600 shadow-xs focus:ring-blue-600 dark:focus:ring-offset-slate-900" name="remember">
                <span class="ms-2 text-xs font-semibold text-slate-600 dark:text-slate-400">{{ __('Ingat Saya') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-6 pt-2">
            @if (Route::has('password.request'))
                <a class="text-xs font-semibold text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 focus:outline-none focus:underline transition" href="{{ route('password.request') }}">
                    {{ __('Lupa password?') }}
                </a>
            @else
                <div></div>
            @endif

            <x-primary-button>
                {{ __('Masuk ke Akun') }}
            </x-primary-button>
        </div>
    </form>

</x-guest-layout>
