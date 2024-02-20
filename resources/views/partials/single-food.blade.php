@extends('/layouts/app')

@section('content')

	@php 
	$routeId = Route::current()->parameter('idCinema');
	$idCinema = request()->cookie('sessionIdCinema') ?? $routeId; 
	@endphp

	<cmp-singlefood 
		route="{{ request()->route('id') }}" 
		cinema="{{ $idCinema }}"
		path="{{ asset('/')}}"
	></cmp-singlefood>

@endsection