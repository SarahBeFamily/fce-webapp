{{-- Order products --}}
@php
	$raw_items = $order['order_data_list'];
	$items = Helper::jsonReplace($raw_items);
	$data = json_decode($items, true);
	$date = new DateTime($order['created_at']);

	// Controllo se tra i prodotti c'è almeno uno snack
	$snack = false;
	foreach ($data as $item) {
		if ($item['type'] === 'snack') {
			$snack = true;
		}
	}

@endphp

<div class="row">
	<div class="col-12 col-md-12">
		<div class="card">
			<div class="card-body">
				<h5 class="card-title">{{ __('Dettagli') }}</h5>
				<div class="table-responsive">
					<h6>{{ __('Biglietti') }}</h6>
					<table class="table table-ticket table-borderless">
						<thead>
							<th>{{ __('Quantità') }}</th>
							<th>{{ __('Prezzo') }}</th>
							<th>{{ __('Film') }}</th>
							<th>{{ __('Sala') }}</th>
							<th>{{ __('Data') }}</th>
							<th>{{ __('Posto') }}</th>
						</thead>
						<tbody>
							@foreach ($data as $item)
							@if ($item['type'] === 'biglietto')
							<tr>
								<td>{{ $item['qty'] }}</td>
								<td>{{ $item['prezzo'] }}€</td>
								<td>
									<strong>{{ __('Titolo') }}</strong>: {{ $item['titolo'] }}<br>
									<strong>{{ __('Categoria') }}</strong>: {{ $item['cat'] }}
								</td>
								<td>{{ $item['sala'] }}</td>
								<td>{{ $item['data'] }}</td>
								<td>{{ $item['posto'] }}</td>
							</tr>
							@endif
							@endforeach
						</tbody>
					</table>

					@if ($snack === true)
					<br>
					<h6>{{ __('Food & Beverage') }}</h6>
					<table class="table table-snack table-borderless">
						<thead>
							<th>{{ __('Prodotto') }}</th>
							<th>{{ __('Quantità') }}</th>
							<th>{{ __('Prezzo Unitario') }}</th>
							<th>{{ __('Prezzo Totale') }}</th>
						</thead>
						<tbody>
							@foreach ($data as $item)
								@if ($item['type'] === 'snack')
								<tr>
									<td>{{ $item['prodotto'] }}</td>
									<td>{{ $item['qty'] }}</td>
									<td>{{ $item['prezzo'] }}€</td>
									<td>{{ $item['prezzoTot'] }}€</td>
								</tr>
								@endif
							@endforeach
						</tbody>
					</table>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>
