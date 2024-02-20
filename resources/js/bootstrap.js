/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import $ from 'jquery';

$(function() {
    console.log( "ready!" );
	$('#loginAccedi').on('click', function() {
		$('#login .intro').attr('phase', 'login');
		$('#login .start, #login .register, h1[start], h1[register]').addClass('hidden');
		$('#login .login, h1[login').removeClass('hidden');
	});

	$('#loginRegistrati').on('click', function() {
		$('#login .intro').attr('phase', 'register');
		$('#login .start, #login .login, h1[start], h1[login').addClass('hidden');
		$('#login .register, h1[register]').removeClass('hidden');
	});

	$('button[action="backStart"]').on('click', function() {
		$('#login .intro').attr('phase', 'start');
		$('#login .register, #login .login, h1[register], h1[login').addClass('hidden');
		$('#login .start, h1[start]').removeClass('hidden');
	});

	$('#recuperaPassword').on('click', function(e) {
		e.preventDefault();

		$('#login .intro').attr('phase', 'register');
		$('#login .start, #login .login, #login div.register, h1[start], h1[register], h1[login').addClass('hidden');
		$('#login .forgot-password, #login img.register, h1[recupera]').removeClass('hidden');
	});

	let idCinema = localStorage.getItem('idCinema');
	$('select#choose_cinema').val(idCinema);
	// Cambia route al cambio di cinema in base al suo id
	$('select#choose_cinema').on('change', function(e) {
		let idCinema = $(this).find('option:selected').val(),
			link = $(this).find('option:selected').attr('data-url');

		localStorage.setItem('idCinema', idCinema);
		$.ajax({
			url: `/cookie/sessionIdCinema/${idCinema}`,
			method: 'GET',
			success: function(data) {
				console.log(data)
				location.href = link;
			}
		});
		// $('form#getIdCinema').trigger('submit');
	});
});


/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// import Pusher from 'pusher-js';
// window.Pusher = Pusher;

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: import.meta.env.VITE_PUSHER_APP_KEY,
//     cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
//     wsHost: import.meta.env.VITE_PUSHER_HOST ? import.meta.env.VITE_PUSHER_HOST : `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
//     wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
//     wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
//     forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
//     enabledTransports: ['ws', 'wss'],
// });
