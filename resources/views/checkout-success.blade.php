@extends('/layouts/app')

@section('content')

	<cmp-success 
		route="{{ request()->route('film') }}"
		order="{{ request()->route('order') }}"
	>
	</cmp-success>
@endsection
