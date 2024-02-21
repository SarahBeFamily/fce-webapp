@extends('/layouts/app')

@section('content')

@php($nome = Auth::user()->user_firstname)

	<div id="profilo-utente" class="pad-both-24">
		<p>Ciao {{ $nome }}!</p>

		@include('profile.partials.update-profile-information-form')
		@include('profile.partials.payment-methods')

		<div class="logout">
			@include('auth.logout')
		</div>
	</div>
	
@endsection
