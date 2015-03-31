@extends('package.wrapper')

@section('package_content')
	<div class="row">
		<p>
			<strong>Repository</strong>
			<input type="text" class="form-control" readonly="readonly" value="{{ $package->repository_url }}">
		</p>
		<h2>Libraries</h2>
		<table class="table table-striped">
			<thead>
				<th>Name</th>
				<th>Type</th>
			</thead>
			<tbody>
			@foreach( $package->librariesOrdered as $library )
				<tr>
					<td><a href="{{ action('LibraryController@overview', array($library->id)) }}">{{ $library->name }}</a></td>
					<td>eeschema</td>
				</tr>
			@endforeach
			</tbody>
		</table>
	</div>
@endsection
