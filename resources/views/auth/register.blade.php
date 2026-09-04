<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-muted hover:text-ink rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>

    <div class="mt-4 flex items-center">
        <div class="flex-1 border-t border-brand-200"></div>
        <span class="px-3 text-sm text-muted">{{ __('or') }}</span>
        <div class="flex-1 border-t border-brand-200"></div>
    </div>

    <div class="mt-4">
        <a href="{{ route('google.redirect') }}" class="flex w-full items-center justify-center rounded-md border border-brand-200 bg-surface px-4 py-2 text-sm font-medium text-ink shadow-sm transition hover:bg-brand-50">
            <svg class="mr-2 h-4 w-4" viewBox="0 0 24 24" fill="none">
                <path d="M23.5 12.27c0-.79-.07-1.54-.2-2.27H12v4.51h6.47a5.58 5.58 0 0 1-2.42 3.66v3.04h3.92c2.29-2.1 3.53-5.2 3.53-8.94z" fill="#4285F4"/>
                <path d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.92-3.04c-1.09.73-2.48 1.17-4.01 1.17-3.08 0-5.69-2.08-6.62-4.88H1.3v3.14A12 12 0 0 0 12 24z" fill="#34A853"/>
                <path d="M5.38 14.34a7.2 7.2 0 0 1 0-4.68V6.52H1.3a12 12 0 0 0 0 10.96l4.08-3.14z" fill="#FBBC05"/>
                <path d="M12 4.78c1.76 0 3.34.61 4.59 1.8l3.44-3.44A12 12 0 0 0 12 0 11.99 11.99 0 0 0 1.3 6.52l4.08 3.14C6.31 6.86 8.92 4.78 12 4.78z" fill="#EA4335"/>
            </svg>
            {{ __('Continue with Google') }}
        </a>
        <x-input-error :messages="$errors->get('google')" class="mt-2" />
    </div>
</x-guest-layout>
