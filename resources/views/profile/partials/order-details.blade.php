@extends('/layouts/app')

@section('content')

@php
// creo una tabella con i dettagli dell'ordine selezionato
$orderID = $order->id;
// Get order from id
$order = App\Models\Orders::find($orderID);
$prodotti = $order->order_data_list;
var_dump($order->order_data_list);
@endphp

<h1>{{ __('Ordine #').$orderID }}</h1>

<div class="dettagli-ordine">
	<table class="table">
		<thead>
			<tr>
				<th>{{ __('ID Prodotto') }}</th>
				<th>{{ __('Nome Prodotto') }}</th>
				<th>{{ __('Quantità') }}</th>
				<th>{{ __('Prezzo') }}</th>
				<th>{{ __('Totale') }}</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>

@endsection