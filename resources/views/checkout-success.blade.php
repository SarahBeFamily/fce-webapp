@extends('/layouts/app')

@section('content')

	@php
		$orderID = $order;
		$orderOBj = App\Models\Orders::where('id', $orderID)->first();
		$user = App\Models\User::where('id', $orderOBj->user_id)->first();
		$performance = $_GET['performance'];
		$sessionId = $orderOBj->session_id;
		$raw_items = $orderOBj->order_data_list;
		$items = Helper::jsonReplace($raw_items);
		$data = json_encode($items, true);
	@endphp

	<cmp-success 
		film="{{ $film }}"
		order="{{ $orderOBj }}"
		userp="{{ json_encode($user) }}"
		performance="{{ $performance }}"
		sessionID="{{ $sessionId }}"
		items="{{ $items }}"
	>
	</cmp-success>
@endsection
