@extends('/layouts/app')

@section('content')

@php
// creo una tabella con gli ordini dell'utente loggato

// # Path: resources/views/profile/partials/my-orders.blade.php
// creo una tabella con gli ordini dell'utente loggato
$orders = Auth::user()->orders;
@endphp

<h1>{{ __('Ordini') }}</h1>

<div class="ordini">
	<table class="table">
		<thead>
			<tr>
				<th>{{ __('ID Ordine') }}</th>
				<th>{{ __('Data') }}</th>
				<th>{{ __('Totale') }}</th>
				<th>{{ __('Stato') }}</th>
				<th>{{ __('Dettagli') }}</th>
			</tr>
		</thead>
		<tbody>
			@foreach ($orders as $order)
				<tr>
					<td>{{ $order->id }}</td>
					<td>{{ $order->created_at->format('d/m/Y') }}</td>
					<td>{{ $order->total }}</td>
					<td>{{ $order->status }}</td>
					<td>
						<a href="{{ route('profile.orders.show', $order) }}">{{ __('Dettagli') }}</a>
					</td>
				</tr>
			@endforeach
		</tbody>
	</table>
</div>

@endsection