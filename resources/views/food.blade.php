@extends('/layouts/app')

@section('content')

	<div id="food-page">
		<h1>{{ __('Food & Beverage') }}</h1>
		<p>{{ __('Seleziona uno tra i nostri menù dai sapori ricercati per un’esperienza culinaria di qualità.') }}</p>

		<cmp-food path="{{ asset('/')}}"></cmp-food>
	</div>

@endsection
