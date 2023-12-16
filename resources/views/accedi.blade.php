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
		@include('auth.register')
	</div>
	
</main>

@endsection
