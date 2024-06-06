@extends('layouts.app')

@section('content')

@php($phase = $errors->has('email') ? 'login' : 'start')

<main id="login">
	<div class="intro" phase="{{ $phase }}">
		<img class="start @if ($errors->has('email')) hidden @endif" src="{{ Vite::asset('resources/images/avatar.jpg') }}" alt="">
		<img class="register hidden" src="{{ Vite::asset('resources/images/batman.jpg') }}" alt="">
		<img class="login @if (!$errors->has('email')) hidden @endif" src="{{ Vite::asset('resources/images/joker.jpg') }}" alt="">
	</div>
	<div class="logo">
		<img src="{{ Vite::asset('resources/images/logo.png') }}" alt="FCE">
		
		<x-title-start start></x-title-start>
		<x-title-start login class="hidden"></x-title-start>
		<x-title-start register class="hidden"></x-title-start>
		<x-title-start recupera class="hidden"></x-title-start>

		<p>{{ __('Mettiti comodo e scegli il prossimo film') }}</p>
	</div>

	<div class="buttons pad-both-24 start @if ($errors->has('email')) hidden @endif">
		<button class="full-white" id="loginAccedi">{{ __('Accedi') }}</button>

		<p>{{ __('Non hai ancora un account?') }} <a href="#" class="simple-link" id="loginRegistrati">{{ __('Registrati') }}</a></p>
	</div>

	<div class="login pad-both-24 @if (!$errors->has('email')) hidden @endif">
		@include('auth.login-form')
	</div>

	<div class="register pad-both-24 hidden">
		@include('auth.register')
	</div>

	<div class="forgot-password pad-both-24 hidden">
		@include('auth.forgot-password')
	</div>

	<div class="reset-password pad-both-24 hidden">
		@include('auth.reset-password')
	</div>
	
</main>

@endsection
