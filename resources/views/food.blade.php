@extends('/layouts/app')

@section('content')

	<div id="food-page">
		<h1>{{ __('Food & Beverage') }}</h1>
		<p>{{ __('Seleziona uno tra i nostri menù dai sapori ricercati per un’esperienza culinaria di qualità.') }}</p>

		@php 
		$routeId = Route::current()->parameter('idCinema');
		$idCinema = request()->cookie('sessionIdCinema') ?? $routeId; 
		@endphp
		<cmp-food cinema="{{ $idCinema }}" path="{{ asset('/')}}"></cmp-food>
	</div>

@endsection
