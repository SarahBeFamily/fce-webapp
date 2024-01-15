@extends('/layouts/app')

@section('content')
	
	<div id="film-page">
		<h1>{{ __('Film in programmazione') }}</h1>

		<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Ipsa, explicabo, doloremque voluptate earum quasi laboriosam obcaecati commodi illo, sint placeat laborum mollitia autem modi alias perspiciatis soluta magnam qui? Rerum?</p>

		<div class="film-wrapper">
			<cmp-film path="{{ asset('/')}}"></cmp-film>
		</div>
		
	</div>

@endsection
