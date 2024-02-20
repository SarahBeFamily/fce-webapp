@extends('/layouts/app')

@section('content')

	<cmp-success route="{{ request()->route('film') }}"></cmp-success>
@endsection
