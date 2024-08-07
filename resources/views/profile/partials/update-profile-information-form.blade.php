@php($user = Auth::user())

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div class="flex flex-col">
            <x-input-label for="user_firstname" :value="__('Nome')" />
            <x-text-input id="user_firstname" name="user_firstname" type="text" class="mt-1 block w-full" :value="old('user_firstname', $user->user_firstname)" required autofocus autocomplete="user_firstname" />
            <x-input-error class="mt-2" :messages="$errors->get('user_firstname')" />
        </div>

        <div class="flex flex-col">
            <x-input-label for="user_lastname" :value="__('Cognome')" />
            <x-text-input id="user_lastname" name="user_lastname" type="text" class="mt-1 block w-full" :value="old('user_lastname', $user->user_lastname)" required autofocus autocomplete="user_lastname" />
            <x-input-error class="mt-2" :messages="$errors->get('user_lastname')" />
        </div>

        <div class="flex flex-col">
            <x-input-label for="user_birthdate" :value="__('Data di Nascita')" />
            <x-text-input data-provide="datepicker" data-date-format="dd-mm-yyyy" id="user_birthdate" name="user_birthdate" type="date" class="mt-1 block w-full" :value="old('user_birthdate', $user->user_birthdate)" required autofocus />
            <x-input-error class="mt-2" :messages="$errors->get('user_birthdate')" />
        </div>

        <div class="flex flex-col">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex flex-col">
            <x-input-label for="favorite_fce" :value="__('FCE Preferito')" />
            <select name="favorite_fce" id="favorite_fce">
                <option value="">{{ __('Seleziona un FCE') }}</option>
                @foreach ($cinemas as $id => $fce)
                    <option value="{{ $id }}" @if ($user->favorite_fce == $id) selected @endif>{{ $fce }}</option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('favorite_fce')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Salva') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Successo') }}</p>
            @endif
        </div>
    </form>
</section>
