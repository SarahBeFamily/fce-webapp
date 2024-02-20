@php 
$routeId = Route::current()->parameter('idCinema');

$cookieIdCinema = Cookie::get('sessionIdCinema');
var_dump($cookieIdCinema);
$idCinema = $cookieIdCinema ?? $routeId; 

@endphp

cookie