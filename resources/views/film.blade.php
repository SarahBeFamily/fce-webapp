@extends('/layouts/app')

@section('content')
	
	<div id="film-page">
		<h1 class="pad-both-24">{{ __('Film in programmazione') }}</h1>

		<p class="pad-both-24">Lorem ipsum dolor sit amet consectetur adipisicing elit. Ipsa, explicabo, doloremque voluptate earum quasi laboriosam obcaecati commodi illo, sint placeat laborum mollitia autem modi alias perspiciatis soluta magnam qui? Rerum?</p>

		<div class="header pad-both-24">
			<div class="bf-col flex flex-col">
			  <span class="location">Firenze</span>
			  <select name="Cinema" id="choose_cinema">
				@foreach ($cinemas as $idCinema => $nomeCinema )
				  @php $selected = $idCinema == request()->route('idCinema') ? 'selected' : '' @endphp
				  <option value="{{ $idCinema }}" data-url="{{ route("film", $idCinema) }}" {{ $selected }}>{{ $nomeCinema }}</option>
				@endforeach
			  </select>
			</div>
	  
			<div class="bf-col">
			  <div class="notifications">
				<svg width="32" height="28" viewBox="0 0 32 28" fill="none" xmlns="http://www.w3.org/2000/svg">
				  <path d="M16.0787 3.39502C11.6798 3.39502 8.1048 6.53335 8.1048 10.395V13.7667C8.1048 14.4784 7.75927 15.5634 7.34728 16.17L5.81895 18.3984C4.87537 19.775 5.52657 21.3034 7.25425 21.8167C12.9822 23.4967 19.162 23.4967 24.8899 21.8167C26.498 21.35 27.2023 19.6817 26.3252 18.3984L24.7969 16.17C24.3982 15.5634 24.0526 14.4784 24.0526 13.7667V10.395C24.0526 6.54502 20.4644 3.39502 16.0787 3.39502Z" stroke="white" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"/>
				  <path d="M18.5374 3.73331C16.9302 3.33149 15.2272 3.33149 13.6201 3.73331C14.0055 2.86997 14.9624 2.26331 16.0787 2.26331C17.1951 2.26331 18.152 2.86997 18.5374 3.73331Z" stroke="white" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
				  <path d="M20.0657 22.2367C20.0657 24.1617 18.2716 25.7367 16.0788 25.7367C14.989 25.7367 13.979 25.34 13.2613 24.71C12.5146 24.0535 12.0942 23.1644 12.0918 22.2367" stroke="white" stroke-width="1.5" stroke-miterlimit="10"/>
				</svg>
			  </div>
			</div>
		</div>
	  
		<div id="search" class="pad-both-24 mb-24">
			<div class="fake-search">
			  <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M18.6667 18.6667L17 17M9.91667 17.8333C10.9563 17.8333 11.9857 17.6286 12.9462 17.2307C13.9067 16.8329 14.7795 16.2497 15.5146 15.5146C16.2497 14.7795 16.8329 13.9067 17.2307 12.9462C17.6286 11.9857 17.8333 10.9563 17.8333 9.91667C17.8333 8.87704 17.6286 7.84758 17.2307 6.88709C16.8329 5.92659 16.2497 5.05387 15.5146 4.31874C14.7795 3.58361 13.9067 3.00047 12.9462 2.60262C11.9857 2.20477 10.9563 2 9.91667 2C7.81704 2 5.8034 2.83407 4.31874 4.31874C2.83407 5.8034 2 7.81704 2 9.91667C2 12.0163 2.83407 14.0299 4.31874 15.5146C5.8034 16.9993 7.81704 17.8333 9.91667 17.8333Z" stroke="#4A4B57" stroke-linecap="round" stroke-linejoin="round"/>
			  </svg> 
			  <input type="search" name="cerca-film" id="cerca_film" placeholder="Cerca un film...">
			</div>
		</div>

		<div class="film-wrapper pad-both-24">
			<cmp-film route="{{ request()->route('idCinema') }}" path="{{ asset('/')}}"></cmp-film>
		</div>
		
	</div>

@endsection
