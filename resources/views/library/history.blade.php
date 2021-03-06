@extends('library.wrapper')

@section('library_content')
	<div class="row">
		{!! $eventspaged->render() !!}
				@foreach( $events as $k => $eventGroup )
				<h4>Changes on {{ Carbon\Carbon::parse($k)->toFormattedDateString() }}</h4>
				<table class="table table-striped">
					<thead>
						<tr>
							<th></th>
							<th>Time</th>
						</tr>
					</thead>
					<tbody>
					@foreach( $eventGroup as $event )
					<tr>
						<td>
							{{ $event }}
						</td>
						<td>
							{{ Carbon\Carbon::parse($event->date_occurred)->toTimeString() }}
						</td>
					</tr>
					@endforeach
				</tbody>
			</table>
			@endforeach
		{!! $eventspaged->render() !!}
	</div>
@endsection
