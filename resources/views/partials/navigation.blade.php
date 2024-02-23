@php 
$cookieIdCinema = Cookie::get('sessionIdCinema') ? Cookie::get('sessionIdCinema') : session('idCinema');
$idCinema = $cookieIdCinema ?? 600; 
@endphp

<div class="nav-wrapper">
	<nav>
		<a href="/" class="{{ (request()->routeIs('home')) ? 'router-link-active' : '' }}"><i class="home"></i>{{ __('Home') }}</a>
		<a href="{{ route('film', $idCinema) }}" class="{{ (request()->routeIs('film')) ? 'router-link-active' : '' }}"><i class="film"></i>{{ __('Film') }}</a>
		<a href="/biglietti" class="{{ (request()->routeIs('biglietti')) ? 'router-link-active' : '' }}"><i class="tickets"></i>{{ __('Biglietti') }}</a>
		<a href="{{ route('food', $idCinema) }}" class="{{ (request()->routeIs('food')) ? 'router-link-active' : '' }}"><i class="food"></i>{{ __('Food') }}</a>
		<a href="{{ route('profilo') }}" class="{{ (request()->routeIs('profilo')) ? 'router-link-active' : '' }}"><i class="profile"></i>{{ __('Profilo') }}</a>
	</nav>
</div>