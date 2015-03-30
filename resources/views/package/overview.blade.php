@extends('app')

@section('content')

<div class="container">
	<div class="row">
		<div class="page-header">
			<h1>{{ $package->name }}</h1>
		</div>
		<p class="lead">{{ $package->description }}</p>
		<p>
			<strong>Repository URL:</strong> {{ $package->repository_url }}
		</p>
	</div>
	<div class="row">
		<h2>Libraries</h2>
		<table class="table table-striped">
			<thead>
				<th>Name</th>
				<th>Type</th>
			</thead>
			<tbody>
			@foreach( $package->libraries as $library )
				<tr>
					<td><a href="{{ action('LibraryController@overview', array($library->id)) }}">{{ $library->name }}</a></td>
					<td>eeschema</td>
				</tr>
			@endforeach
			</tbody>
		</table>
	</div>
	<div class="row">
		<h2>History</h2>
		<table class="table table-striped">
			<thead>
				<tr>
					<th></th>
					<th>Date</th>
				</tr>
			</thead>
			<tbody>
				@foreach( $package->events as $event )
					<tr>
						<td>
							{{ $event }}
						</td>
						<td>
							{{ $event->date_occurred }}
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@endsection
