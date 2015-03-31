@extends('package.wrapper')

@section('package_content')
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
@endsection
