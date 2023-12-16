@extends('/layouts/app')

@section('content')

@php($nome = Auth::user()->user_firstname)

	<div class="pad-both-24">
		<p>Ciao {{ $nome }}!</p>

		@include('profile.partials.update-profile-information-form')

		@include('auth.logout')
	</div>
	
@endsection
