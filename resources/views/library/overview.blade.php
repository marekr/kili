@extends('app')

@section('content')

<div class="container">
	<div class="row">
		<div class="page-header">
			<h1>{{ $library->name }}</h1>
		</div>
	</div>
	<div class="row">
		<h2>Components</h2>
		<table class="table table-striped">
			<thead>
				<th>Name</th>
			</thead>
			<tbody>
			@foreach( $library->components as $component )
				<tr>
					<td><a href="{{ action('ComponentController@index', array($component->id)) }}">{{ $component->name }}</a></td>
				</tr>
			@endforeach
			</tbody>
		</table>
	</div>
</div>
@endsection
