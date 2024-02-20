@extends('/layouts/app')

@section('content')

	@php 
	$routeId = Route::current()->parameter('idCinema');
	$idCinema = request()->cookie('sessionIdCinema') ?? $routeId; 
	// var_dump(request()->session('cart'));
	@endphp

	<cmp-singlefilm 
		route="{{ request()->route('id') }}" 
		cinema="{{ $idCinema }}"
		path="{{ asset('/')}}"
		{{-- carrello="{{ json_encode($orders->getCart()) }}" --}}
	></cmp-singlefilm>

@endsection