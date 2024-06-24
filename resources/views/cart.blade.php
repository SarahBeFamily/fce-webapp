@extends('/layouts/app')

@section('content')

<table>
   	<thead>
       	<tr>
           	<th>Product</th>
           	<th>Qty</th>
           	<th>Price</th>
           	<th>Subtotal</th>
       	</tr>
   	</thead>

   	<tbody>

   		@foreach(session('cart') as $row)
			@php(var_dump($row))
       		<tr>
           		<td>
               		<p><strong>{{ $row->name }}</strong></p>
               		<p> {{ ($row->options->has('size') ? $row->options->size : '') }}</p>
           		</td>
           		<td><input type="text" value="{{ $row->qty }}"></td>
           		<td>$ {{ $row->price }}</td>
           		<td>$ {{ $row->total }}</td>
       		</tr>

	   	@endforeach

   	</tbody>

</table>
@endsection