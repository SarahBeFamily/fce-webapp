@extends('/layouts/app')

@section('content')

	@php
		$orderID = $order;
		$orderOBj = App\Models\Orders::where('id', $orderID)->first();
	@endphp

	<cmp-success 
		film="{{ $film }}"
		order="{{ $orderOBj }}"
	>
	</cmp-success>
@endsection
