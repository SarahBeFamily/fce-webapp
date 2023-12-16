
<h1 {{ $attributes }}>

	@if ( $attributes->has('start') )
		{{ __('Benvenuto') }}
	@elseif ( $attributes->has('login') )
		{{ __('Accedi') }}
	@elseif ( $attributes->has('register') )
		{{ __('Registrati') }}
	@endif
	
</h1>