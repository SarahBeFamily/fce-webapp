@extends('/layouts/app')

@section('content')

	<cmp-singlefilm route="{{ request()->route('id') }}" path="{{ asset('/')}}"></cmp-singlefilm>

@endsection