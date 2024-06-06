{{-- Order details --}}
@php
	$date = new DateTime($order['created_at']);

@endphp
<div class="row">
	<div class="col-12 col-md-12">
		<div class="card">
			<div class="card-body">
				<h5 class="card-title">{{ __('Dettagli') }}</h5>
				<div class="table-responsive">
					<table class="table table-borderless">
						<tbody>
							<tr>
								<th>{{ __('ID Ordine') }}</th>
								<td>{{ $order['id'] }}</td>
							</tr>
							<tr>
								<th>{{ __('Data Ordine') }}</th>
								<td>{{ date_format($date, 'd/m/Y H:i:s') }}</td>
							</tr>
							<tr>
								<th>{{ __('Stato') }}</th>
								<td>{{ $order['order_status'] }}</td>
							</tr>
							{{-- Dati utente --}}
							<tr>
								<th>{{ __('Cliente') }}</th>
								<td>{{ $user['user_firstname'] }} {{ $user['user_lastname'] }}<br>
									{{ $user['email'] }}
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
{{-- /Order details --}}
