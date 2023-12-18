@extends('/layouts/app')

@section('content')

	<div id="food-page">
		<cmp-food path="{{ asset('/')}}"></cmp-food>
	</div>

@endsection
