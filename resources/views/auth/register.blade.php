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
		@include('auth.login')
	</div>

	<div class="register pad-both-24 hidden">
        <x-guest-layout>
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Email Address -->
                <div class="flex flex-col">
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="flex flex-col">
                    <x-input-label for="password" :value="__('Password')" />

                    <x-text-input id="password" class="block mt-1 w-full"
                                    type="password"
                                    name="password"
                                    required autocomplete="new-password" />

                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div class="flex flex-col">
                    <x-input-label for="password_confirmation" :value="__('Conferma Password')" />

                    <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                    type="password"
                                    name="password_confirmation" required autocomplete="new-password" />

                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <div class="flex items-center justify-between">
                    <x-secondary-button action="backStart" class="fb-48">{{ __('Indietro') }}</x-secondary-button>

                    <x-primary-button class="fb-48">{{ __('Registrati') }}</x-primary-button>
                </div>
            </form>
        </x-guest-layout>
    </div>
	
</main>

@endsection