@extends('/layouts/app')

@section('content')

	@php 
	$routeId = Route::current()->parameter('idCinema');
	$idCinema = request()->cookie('sessionIdCinema') ?? $routeId;
	$user = Auth::user();
	// var_dump($intent);
	@endphp

	<cmp-singlefilm 
		route="{{ request()->route('id') }}" 
		cinema="{{ $idCinema }}"
		path="{{ asset('/')}}"
		userp="{{ json_encode($user) }}"
		client_secret="{{ $intent }}"
		intent_idp="{{ $intent_id }}"
		{{-- carrello="{{ json_encode($orders->getCart()) }}" --}}
	></cmp-singlefilm>

@endsection
