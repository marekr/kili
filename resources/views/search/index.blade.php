@extends('app')

@section('content')


<div class="container">
	<div class="row">
		<div class="page-header">
			<h1>Search for {{ $query }}</h1>
		</div>
	</div>
	<div class="row">
		@foreach ($results as $component)
			@include('search.result_component', ['component' => $component])
		@endforeach
	</div>
</div>
@endsection
