@extends('layouts.app')

@section('content')

<main id="login">
	<div class="intro" phase="start">
		<img class="start" src="{{ Vite::asset('resources/images/avatar.jpg') }}" alt="">
		<img class="register hidden" src="{{ Vite::asset('resources/images/batman.jpg') }}" alt="">
		<img class="login hidden" src="{{ Vite::asset('resources/images/joker.jpg') }}" alt="">
	</div>
	<div class="logo">
		<img src="{{ Vite::asset('resources/images/logo.png') }}" alt="FCE">
		
		<x-title-start start></x-title-start>
		<x-title-start login class="hidden"></x-title-start>
		<x-title-start register class="hidden"></x-title-start>

		<p>{{ __('Mettiti comodo e scegli il prossimo film') }}</p>
	</div>

	<div class="buttons pad-both-24 start">
		<button class="full-white" id="loginAccedi">{{ __('Accedi') }}</button>

		<p>{{ __('Non hai ancora un account?') }} <a href="#" class="simple-link" id="loginRegistrati">{{ __('Registrati') }}</a></p>
	</div>

	<div class="login pad-both-24 hidden">
        <x-guest-layout>
            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Address -->
                <div class="flex flex-col">
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="flex flex-col">
                    <x-input-label for="password" :value="__('Password')" />

                    <x-text-input id="password" class="block mt-1 w-full"
                                    type="password"
                                    name="password"
                                    required autocomplete="current-password" />

                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="flex items-center justify-between">
                    <x-secondary-button action="backStart" class="fb-48">{{ __('Indietro') }}</x-secondary-button>

                    <x-primary-button class="fb-48">{{ __('Accedi') }}</x-primary-button>
                </div>

                <!-- Remember Me -->
                <div class="block mt-4">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="remember">
                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Ricordami') }}</span>
                    </label>
                </div>

                <p>
                    @if (Route::has('password.request'))
                        <a class="simple-link" href="{{ route('password.request') }}">
                            {{ __('Password dimenticata?') }}
                        </a>
                    @endif
                </p>
            </form>
        </x-guest-layout>
    </div>

	<div class="register pad-both-24 hidden">
		@include('auth.register')
	</div>
	
</main>

@endsection
