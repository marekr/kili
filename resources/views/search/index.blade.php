@extends('app')

@section('content')


<div class="container">
	<div class="row">
		<div class="page-header">
			<h1>Search for {{ $query }}</h1>
		</div>
	</div>
	<div class="row">
		<h2>Components</h2>
		<table class="table table-striped">
			<thead>
				<th>Library</th>
				<th>Name</th>
				<th>Type</th>
			</thead>
			<tbody>
			@foreach ($results as $component)
				
					<td>{{ $component->library->name }} </td>
					<td>{{ $component->name }}</td>
					<td>eeschema</td>
				</tr>
			@endforeach
			</tbody>
		</table>
	</div>
</div>
@endsection
