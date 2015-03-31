@extends('library.wrapper')

@section('library_content')
	<div class="row">
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
@endsection
