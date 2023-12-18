@extends('/layouts/app')

@section('content')

	<cmp-singlefood route="{{ request()->route('id') }}" path="{{ asset('/')}}"></cmp-singlefood>

@endsection