<div class="nav-wrapper">
	<nav>
		<a href="/" class="{{ (request()->is('/')) ? 'router-link-active' : '' }}"><i class="home"></i>{{ __('Home') }}</a>
		<a href="/film" class="{{ (request()->is('film')) ? 'router-link-active' : '' }}"><i class="film"></i>{{ __('Film') }}</a>
		<a href="/biglietti" class="{{ (request()->is('biglietti')) ? 'router-link-active' : '' }}"><i class="tickets"></i>{{ __('Biglietti') }}</a>
		<a href="/food" class="{{ (request()->is('food')) ? 'router-link-active' : '' }}"><i class="food"></i>{{ __('Food') }}</a>
		<a href="/profilo" class="{{ (request()->is('profilo')) ? 'router-link-active' : '' }}"><i class="profile"></i>{{ __('Profilo') }}</a>
	</nav>
</div>